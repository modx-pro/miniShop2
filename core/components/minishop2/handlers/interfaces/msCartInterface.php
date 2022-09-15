<?php

interface msCartInterface
{

    /**
     * Initializes cart to context
     * Here you can load custom javascript or styles
     *
     * @param string $ctx Context for initialization
     *
     * @return boolean
     */
    public function initialize($ctx = 'web');

    /**
     * Adds product to cart
     *
     * @param integer $id Id of MODX resource. It must be an msProduct descendant
     * @param integer $count .A number of product exemplars
     * @param array $options Additional options of the product: color, size etc.
     *
     * @return array|string $response
     */
    public function add($id, $count = 1, $options = []);

    /**
     * Removes product from cart
     *
     * @param string $key The unique key of cart item
     *
     * @return array|string $response
     */
    public function remove($key);

    /**
     * Changes products count in cart
     *
     * @param string $key The unique key of cart item
     * @param integer $count .A number of product exemplars
     *
     * @return array|string $response
     */
    public function change($key, $count);

    /**
     * Cleans the cart
     *
     * @return array|string $response
     */
    public function clean();

    /**
     * Returns the cart status: number of items, weight, price.
     *
     * @param array $data Additional data to return with status
     *
     * @return array $status
     */
    public function status($data = []);

    /**
     * Returns the cart items
     *
     * @return array $cart
     */
    public function get();

    /**
     * Set all the cart items by one array
     *
     * @param array $cart
     *
     * @return void
     */
    public function set($cart = []);
}
