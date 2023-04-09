<?php

require_once dirname(__FILE__) . '/interfaces/msCartInterface.php';

class msCartHandler implements msCartInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var array $config */
    public $config = [];
    /** @var array $cart */
    protected $cart;
    protected $ctx = 'web';
    protected $storage = 'session';
    protected $storageHandler;

    /**
     * msCartHandler constructor.
     *
     * @param miniShop2 $ms2
     * @param array $config
     */
    public function __construct(miniShop2 $ms2, array $config = [])
    {
        $this->ms2 = $ms2;
        $this->modx = $ms2->modx;

        $this->storage = $this->modx->getOption('ms2_tmp_storage', null, 'session');
        $this->storageInit();

        $this->config = array_merge([
            'cart' => $this->storageHandler->get(),
            'max_count' => $this->modx->getOption('ms2_cart_max_count', null, 1000, true),
            'cart_product_key_fields' => $this->modx->getOption(
                'ms2_cart_product_key_fields',
                null,
                'id,options',
                true
            ),
            'allow_deleted' => false,
            'allow_unpublished' => false,
        ], $config);

        $this->cart = &$this->config['cart'];
        $this->modx->lexicon->load('minishop2:cart');

        if (empty($this->cart) || !is_array($this->cart)) {
            $this->cart = [];
        }
    }

    /**
     * @param string $ctx
     *
     * @return bool
     */
    public function initialize($ctx = 'web')
    {
        $ms2_cart_context = (bool)$this->modx->getOption('ms2_cart_context', null, '0', true);
        if ($ms2_cart_context) {
            $ctx = 'web';
        }
        $this->ctx = $ctx;
        $this->storageHandler->setContext($this->ctx);
        return true;
    }

    /**
     * @param int $id
     * @param int $count
     * @param array $options
     *
     * @return array|string
     */
    public function add($id, $count = 1, $options = [])
    {
        if (empty($id) || !is_numeric($id)) {
            return $this->error('ms2_cart_add_err_id');
        }
        $count = intval($count);
        if (is_string($options)) {
            $options = json_decode($options, true);
        }
        if (!is_array($options)) {
            $options = [];
        }

        $filter = ['id' => $id, 'class_key' => 'msProduct'];
        if (!$this->config['allow_deleted']) {
            $filter['deleted'] = 0;
        }
        if (!$this->config['allow_unpublished']) {
            $filter['published'] = 1;
        }
        /** @var msProduct $product */
        $product = $this->modx->getObject('msProduct', $filter);
        if (!$product) {
            return $this->error('ms2_cart_add_err_nf', $this->status());
        }

        if ($count > $this->config['max_count'] || $count <= 0) {
            return $this->error('ms2_cart_add_err_count', $this->status(), ['count' => $count]);
        }

        /* You can prevent add of product to cart by adding some text to $modx->event->_output
        <?php
                if ($modx->event->name = 'msOnBeforeAddToCart') {
                    $modx->event->output('Error');
                }

        // Also you can modify $count and $options variables by add values to $this->modx->event->returnedValues
            <?php
                if ($modx->event->name = 'msOnBeforeAddToCart') {
                    $values = & $modx->event->returnedValues;
                    $values['count'] = $count + 10;
                    $values['options'] = array('size' => '99');
                }
        */

        $response = $this->ms2->invokeEvent('msOnBeforeAddToCart', [
            'product' => $product,
            'count' => $count,
            'options' => $options,
            'cart' => $this,
        ]);
        if (!($response['success'])) {
            return $this->error($response['message']);
        }
        $price = $product->getPrice();
        $oldPrice = $product->get('old_price');
        $weight = $product->getWeight();
        $count = $response['data']['count'];
        $options = $response['data']['options'];
        $discount_price = $oldPrice > 0 ? $oldPrice - $price : 0;
        $discount_cost = $discount_price * $count;
        $key = $this->getProductKey($product->toArray(), $options);

        if (array_key_exists($key, $this->cart)) {
            return $this->change($key, $this->cart[$key]['count'] + $count);
        }
        $ctx_key = 'web';
        $ms2_cart_context = (bool)$this->modx->getOption('ms2_cart_context', null, '0', true);
        if (!$ms2_cart_context) {
            $ctx_key = $this->ctx;
        }

        $cartItem = [
            'id' => $id,
            'price' => $price,
            'old_price' => $oldPrice,
            'discount_price' => $discount_price,
            'discount_cost' => $discount_cost,
            'weight' => $weight,
            'count' => $count,
            'options' => $options,
            'ctx' => $ctx_key,
            'key' => $key
        ];
        $this->cart = $this->storageHandler->add($cartItem);
        $response = $this->ms2->invokeEvent('msOnAddToCart', ['key' => $key, 'cart' => $this]);
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        return $this->success(
            'ms2_cart_add_success',
            $this->status([
                'key' => $key,
                'cart' => $this->cart,
                'row' => $this->cart[$key]
            ]),
            ['count' => $count]
        );
    }

    /**
     * @param string $key
     *
     * @return array|string
     */
    public function remove($key)
    {
        if (!array_key_exists($key, $this->cart)) {
            return $this->error('ms2_cart_remove_error');
        }

        $response = $this->ms2->invokeEvent('msOnBeforeRemoveFromCart', ['key' => $key, 'cart' => $this]);
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $row = $this->cart[$key];
        $this->cart = $this->storageHandler->remove($key);

        $response = $this->ms2->invokeEvent('msOnRemoveFromCart', ['key' => $key, 'cart' => $this]);
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        return $this->success(
            'ms2_cart_remove_success',
            $this->status([
                'cart' => $this->cart,
                'row' => $row
            ])
        );
    }

    /**
     * @param string $key
     * @param int $count
     *
     * @return array|string
     */
    public function change($key, $count)
    {
        $status = [];
        if (!array_key_exists($key, $this->cart)) {
            return $this->error('ms2_cart_change_error', $this->status($status));
        }

        if ($count <= 0) {
            return $this->remove($key);
        }

        if ($count > $this->config['max_count']) {
            return $this->error('ms2_cart_add_err_count', $this->status(), ['count' => $count]);
        }

        $response = $this->ms2->invokeEvent(
            'msOnBeforeChangeInCart',
            ['key' => $key, 'count' => $count, 'cart' => $this]
        );
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $count = $response['data']['count'];
        $this->cart = $this->storageHandler->change($key, $count);
        $response = $this->ms2->invokeEvent(
            'msOnChangeInCart',
            ['key' => $key, 'count' => $count, 'cart' => $this]
        );
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $status['key'] = $key;
        $status['cost'] = $count * $this->cart[$key]['price'];
        $status['cart'] = $this->cart;
        $status['row'] = $this->cart[$key];

        return $this->success(
            'ms2_cart_change_success',
            $this->status($status),
            ['count' => $count]
        );
    }

    /**
     * @return array|string
     */
    public function clean()
    {
        $response = $this->ms2->invokeEvent('msOnBeforeEmptyCart', ['cart' => $this]);
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $this->cart = $this->storageHandler->clean($this->ctx);

        $response = $this->ms2->invokeEvent('msOnEmptyCart', ['cart' => $this]);
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        return $this->success('ms2_cart_clean_success', $this->status());
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function status($data = [])
    {
        $status = [
            'total_count' => 0,
            'total_cost' => 0,
            'total_weight' => 0,
            'total_discount' => 0,
            'total_positions' => count($this->cart),
        ];
        foreach ($this->cart as $item) {
            if (empty($item['ctx']) || $item['ctx'] == $this->ctx) {
                $status['total_count'] += $item['count'];
                $status['total_cost'] += $item['price'] * $item['count'];
                $status['total_weight'] += $item['weight'] * $item['count'];
                $status['total_discount'] += $item['discount_price'] * $item['count'];
            }
        }

        $status = array_merge($data, $status);

        $response = $this->ms2->invokeEvent('msOnGetStatusCart', [
            'status' => $status,
            'cart' => $this,
        ]);
        if ($response['success']) {
            $status = $response['data']['status'];
        }

        return $status;
    }

    /**
     * @return array
     */
    public function get()
    {
        $cart = [];
        foreach ($this->cart as $key => $item) {
            if (empty($item['ctx']) || $item['ctx'] == $this->ctx) {
                $cart[$key] = $item;
            }
        }

        return $cart;
    }

    /**
     * @param array $cart
     */
    public function set($cart = [])
    {
        $this->cart = $this->storageHandler->set($cart);
    }

    /**
     * Set controller for Cart
     */
    protected function storageInit()
    {
        switch ($this->storage) {
            case 'session':
                require_once dirname(__FILE__) . '/storage/session/cartsessionhandler.class.php';
                $this->storageHandler = new CartSessionHandler($this->modx);
                break;
            case 'db':
                require_once dirname(__FILE__) . '/storage/db/cartdbhandler.class.php';
                $this->storageHandler = new CartDBHandler($this->modx, $this->ms2);
                break;
        }
    }

    /**
     * Shorthand for MS2 error method
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function error($message = '', $data = [], $placeholders = [])
    {
        return $this->ms2->error($message, $data, $placeholders);
    }

    /**
     * Shorthand for MS2 success method
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = [], $placeholders = [])
    {
        return $this->ms2->success($message, $data, $placeholders);
    }

    /**
     * Generate cart product key
     *
     * @param array $product
     * @param array $options
     * @return string
     *
     */
    private function getProductKey(array $product, array $options = [])
    {
        $key_fields = explode(',', $this->config['cart_product_key_fields']);
        $product['options'] = $options;
        $key = '';

        foreach ($key_fields as $key_field) {
            if (isset($product[$key_field])) {
                if (is_array($product[$key_field])) {
                    $key .= json_encode($product[$key_field]);
                } else {
                    $key .= $product[$key_field];
                }
            }
        }

        return 'ms' . md5($key);
    }
}
