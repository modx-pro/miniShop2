<?php

interface msOrderInterface
{

    /**
     * Initializes order to context
     * Here you can load custom javascript or styles
     *
     * @param string $ctx Context for initialization
     *
     * @return boolean
     */
    public function initialize($ctx = 'web');

    /**
     * Add one field to order
     *
     * @param string $key Name of the field
     * @param string $value .Value of the field
     *
     * @return boolean
     */
    public function add($key, $value);

    /**
     * Validates field before it set
     *
     * @param string $key The key of the field
     * @param string $value .Value of the field
     *
     * @return boolean|mixed
     */
    public function validate($key, $value);

    /**
     * Removes field from order
     *
     * @param string $key The key of the field
     *
     * @return boolean
     */
    public function remove($key);

    /**
     * Returns the whole order
     *
     * @return array $order
     */
    public function get();

    /**
     * Returns the one field of order
     *
     * @param array $order Whole order at one time
     *
     * @return array $order
     */
    public function set(array $order);

    /**
     * Submit the order. It will create record in database and redirect user to payment, if set.
     *
     * @return array $status Array with order status
     */
    public function submit();

    /**
     * Cleans the order
     *
     * @return boolean
     */
    public function clean();

    /**
     * Returns the cost of delivery depending on its settings and the goods in a cart
     *
     * @return array $response
     */
    public function getCost();
}
