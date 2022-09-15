<?php

interface msPaymentInterface
{

    /**
     * Send user to payment service
     *
     * @param msOrder $order Object with an order
     *
     * @return array|boolean $response
     */
    public function send(msOrder $order);

    /**
     * Receives payment
     *
     * @param msOrder $order Object with an order
     *
     * @return array|boolean $response
     */
    public function receive(msOrder $order);

    /**
     * Returns an additional cost depending on the method of payment
     *
     * @param msOrderInterface $order
     * @param msPayment $payment
     * @param float $cost
     *
     * @return integer
     */
    public function getCost(msOrderInterface $order, msPayment $payment, $cost = 0.0);

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
