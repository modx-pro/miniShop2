<?php

require_once dirname(__FILE__) . '/interfaces/msDeliveryInterface.php';

class msDeliveryHandler implements msDeliveryInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;

    /**
     * @param xPDOObject $object
     * @param array $config
     */
    public function __construct(xPDOObject $object, $config = [])
    {
        $this->modx = $object->xpdo;
        $this->ms2 = $object->xpdo->getService('miniShop2');
    }

    /**
     * @param msOrderInterface $order
     * @param msDelivery $delivery
     * @param float $cost
     *
     * @return float|int
     */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0.0)
    {
        if (empty($this->ms2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }
        if (empty($this->ms2->cart)) {
            $this->ms2->loadServices($this->ms2->config['ctx']);
        }
        $cart = $this->ms2->cart->status();
        $weight_price = $delivery->get('weight_price');

        $cart_weight = $cart['total_weight'];
        $cost += $weight_price * $cart_weight;

        $free_delivery_amount = $delivery->get('free_delivery_amount');
        if ($free_delivery_amount > 0 && $free_delivery_amount <= $cart['total_cost']) {
            $add_price = 0;
        } else {
            $add_price = $delivery->get('price');
            if (preg_match('/%$/', $add_price)) {
                $add_price = str_replace('%', '', $add_price);
                $add_price = $cost / 100 * $add_price;
            }
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
}
