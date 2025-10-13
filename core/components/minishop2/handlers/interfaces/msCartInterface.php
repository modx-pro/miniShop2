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
    public function initialize(string $ctx = 'web'): bool;

    /**
     * Adds product to cart
     *
     * @param integer $id Id of MODX resource. It must be an msProduct descendant
     * @param integer $count .A number of product exemplars
     * @param array $options Additional options of the product: color, size etc.
     *
     * @return array|string $response
     */
    public function add(int $id, int $count = 1, array $options = []);

    /**
     * Removes product from cart
     *
     * @param string $key The unique key of cart item
     *
     * @return array|string $response
     */
    public function remove(string $key);

    /**
     * Changes products count in cart
     *
     * @param string $key The unique key of cart item
     * @param integer $count .A number of product exemplars
     *
     * @return array|string $response
     */
    public function change(string $key, int $count);

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
    public function status(array $data = []): array;

    /**
     * Returns the cart items
     *
     * @return array $cart
     */
    public function get(): array;

    /**
     * Set all the cart items by one array
     *
     * @param array $cart
     *
     * @return void
     */
    public function set(array $cart = []): void;
}
