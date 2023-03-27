<?php

require_once dirname(__FILE__) . '/interfaces/msPaymentInterface.php';

class msPaymentHandler implements msPaymentInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var array $config */
    public $config = [];

    /**
     * @param xPDOObject $object
     * @param array $config
     */
    public function __construct(xPDOObject $object, $config = [])
    {
        $this->modx = $object->xpdo;
        $this->ms2 = $object->xpdo->getService('miniShop2');
        $this->config = $config;
    }

    /**
     * @param msOrder $order
     *
     * @return array|string
     */
    public function send(msOrder $order)
    {
        return $this->success('', ['msorder' => $order->get('id')]);
    }

    /**
     * @param msOrder $order
     *
     * @return array|string
     */
    public function receive(msOrder $order)
    {
        return $this->success('');
    }

    /**
     * @param msOrderInterface $order
     * @param msPayment $payment
     * @param float $cost
     *
     * @return float|int
     */
    public function getCost(msOrderInterface $order, msPayment $payment, $cost = 0.0)
    {
        $add_price = $payment->get('price');
        if (preg_match('/%$/', $add_price)) {
            $add_price = str_replace('%', '', $add_price);
            $add_price = $cost / 100 * $add_price;
        }
        $cost += $add_price;

        return $cost;
    }

    /**
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function error($message = '', $data = [], $placeholders = [])
    {
        if (empty($this->ms2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }

        return $this->ms2->error($message, $data, $placeholders);
    }

    /**
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = [], $placeholders = [])
    {
        if (empty($this->ms2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }

        return $this->ms2->success($message, $data, $placeholders);
    }

    /**
     * Returns hash of order for various checks
     *
     * @param msOrder $order
     *
     * @return string
     */
    public function getOrderHash(msOrder $order)
    {
        return md5(
            $order->get('id') .
            $order->get('num') .
            $order->get('cart_cost') .
            $order->get('delivery_cost') .
            $order->get('createdon')
        );
    }
}
