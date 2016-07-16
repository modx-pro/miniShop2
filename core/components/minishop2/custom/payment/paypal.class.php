<?php
if (!class_exists('msPaymentInterface')) {
    require_once dirname(dirname(dirname(__FILE__))) . '/model/minishop2/mspaymenthandler.class.php';
}

class PayPal extends msPaymentHandler implements msPaymentInterface
{

    /**
     * PayPal constructor.
     *
     * @param xPDOObject $object
     * @param array $config
     */
    function __construct(xPDOObject $object, $config = array())
    {
        parent::__construct($object, $config);

        $siteUrl = $this->modx->getOption('site_url');
        $assetsUrl = $this->modx->getOption('assets_url') . 'components/minishop2/';
        $paymentUrl = $siteUrl . substr($assetsUrl, 1) . 'payment/paypal.php';

        $this->config = array_merge(array(
            'paymentUrl' => $paymentUrl,
            'apiUrl' => $this->modx->getOption('ms2_payment_paypal_api_url', null, 'https://api-3t.paypal.com/nvp'),
            'checkoutUrl' => $this->modx->getOption('ms2_payment_paypal_checkout_url', null,
                'https://www.paypal.com/webscr?cmd=_express-checkout&token='
            ),
            'currency' => $this->modx->getOption('ms2_payment_paypal_currency', null, 'USD'),
            'user' => $this->modx->getOption('ms2_payment_paypal_user'),
            'password' => $this->modx->getOption('ms2_payment_paypal_pwd'),
            'signature' => $this->modx->getOption('ms2_payment_paypal_signature'),
            'json_response' => false,
        ), $config);
    }


    /**
     * @param msOrder $order
     *
     * @return array|string
     */
    public function send(msOrder $order)
    {
        if ($order->get('status') > 1) {
            return $this->error('ms2_err_status_wrong');
        }
        $params = array(
            'METHOD' => 'SetExpressCheckout',
            'PAYMENTREQUEST_0_CURRENCYCODE' => $this->config['currency'],
            'PAYMENTREQUEST_0_ITEMAMT' => str_replace(',', '.', $order->get('cart_cost')),
            'PAYMENTREQUEST_0_SHIPPINGAMT' => str_replace(',', '.', $order->get('delivery_cost')),
            'PAYMENTREQUEST_0_AMT' => str_replace(',', '.', $order->get('cost')),
            'RETURNURL' => $this->config['paymentUrl'] . '?action=success',
            'CANCELURL' => $this->config['paymentUrl'] . '?action=cancel',
            'PAYMENTREQUEST_0_INVNUM' => $order->get('id'),
        );

        /** @var msOrderProduct $item */
        $i = 0;
        if ($this->modx->getOption('ms2_payment_paypal_order_details', null, true)) {
            $products = $order->getMany('Products');
            foreach ($products as $item) {
                /** @var msProduct $product */
                $name = $item->get('name');
                if (empty($name) && $product = $item->getOne('Product')) {
                    $name = $product->get('pagetitle');
                }
                $params['L_PAYMENTREQUEST_0_NAME' . $i] = $name;
                $params['L_PAYMENTREQUEST_0_AMT' . $i] = str_replace(',', '.', $item->get('price'));
                $params['L_PAYMENTREQUEST_0_QTY' . $i] = $item->get('count');
                $i++;
            }
        }

        $response = $this->request($params);
        if (is_array($response) && !empty($response['ACK']) && $response['ACK'] == 'Success') {
            $token = $response['TOKEN'];

            return $this->success('', array('redirect' => $this->config['checkoutUrl'] . urlencode($token)));
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                '[miniShop2] Payment error while request. Request: ' . print_r($params,
                    1) . ', response: ' . print_r($response, 1));

            return $this->success('', array('msorder' => $order->get('id')));
        }
    }


    /**
     * @param msOrder $order
     * @param array $params
     *
     * @return bool
     */
    public function receive(msOrder $order, $params = array())
    {
        if (!empty($params['PAYERID'])) {
            $params = array(
                'METHOD' => 'DoExpressCheckoutPayment',
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                'PAYMENTREQUEST_0_AMT' => $params['PAYMENTREQUEST_0_AMT'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => $this->config['currency'],
                'PAYERID' => $params['PAYERID'],
                'TOKEN' => $params['TOKEN'],
            );
            $response = $this->request($params);

            if (!empty($response['ACK']) && $response['ACK'] == 'Success') {
                $this->ms2->changeOrderStatus($order->get('id'), 2); // Set status "paid"
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    '[miniShop2] Could not finalize operation: Request: ' . print_r($params, true) .
                    ', response: ' . print_r($response, true)
                );
            }
        } else {
            if ($this->modx->getOption('ms2_payment_paypal_cancel_order', null, false)) {
                $this->ms2->changeOrderStatus($order->get('id'), 4); // Set status "cancelled"
            }
        }

        return true;
    }


    /**
     * Building query
     *
     * @param array $params Query params
     *
     * @return array/boolean
     */
    public function request($params = array())
    {
        $requestParams = array_merge(array(
            'USER' => $this->config['user'],
            'PWD' => $this->config['password'],
            'SIGNATURE' => $this->config['signature'],
            'VERSION' => '74.0',
        ), $params);

        $request = http_build_query($requestParams);
        $curlOptions = array(
            CURLOPT_URL => $this->config['apiUrl'],
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => dirname(__FILE__) . '/lib/paypal/cacert.pem',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = curl_error($ch);
        } else {
            $result = array();
            parse_str($response, $result);
        }

        curl_close($ch);

        return $result;
    }


    /**
     * Returns a direct link for continue payment process of existing order
     *
     * @param msOrder $order
     *
     * @return string
     */
    public function getPaymentLink(msOrder $order)
    {
        return $this->config['paymentUrl'] . '?' .
        http_build_query(array(
            'action' => 'continue',
            'msorder' => $order->get('id'),
            'mscode' => $this->getOrderHash($order),
        ));
    }

}