<?php

interface msDeliveryInterface
{

    /**
     * Returns an additional cost depending on the method of delivery
     *
     * @param msOrderInterface $order
     * @param msDelivery $delivery
     * @param float $cost
     *
     * @return float|integer
     */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0.0);


    /**
     * Returns failure response
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function error($message = '', $data = array(), $placeholders = array());


    /**
     * Returns success response
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array());
}


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
    function __construct(xPDOObject $object, $config = array())
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
        $cart = $this->ms2->cart->status();
        $weight_price = $delivery->get('weight_price');
        //$distance_price = $delivery->get('distance_price');

        $cart_weight = $cart['total_weight'];
        $cost += $weight_price * $cart_weight;

        $add_price = $delivery->get('price');
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
    public function error($message = '', $data = array(), $placeholders = array())
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
    public function success($message = '', $data = array(), $placeholders = array())
    {
        if (empty($this->ms2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }

        return $this->ms2->success($message, $data, $placeholders);
    }
}