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
    public function error($message = '', $data = [], $placeholders = []);

    /**
     * Returns success response
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = [], $placeholders = []);
}
