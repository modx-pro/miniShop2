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
            'percent_precision' => $this->modx->getOption('ms2_cart_percent_precision', null, 2),
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

        $productData = $product->toArray();
        $productData['options'] = $response['data']['options'];
        $key = $this->getProductKey($productData);

        if (array_key_exists($key, $this->cart)) {
            return $this->change($key, $this->cart[$key]['count'] + $count);
        }

        $cartItem = $this->getCartItem($product, $key, $productData['options'], $count);

        $this->cart = $this->storageHandler->add($cartItem);
        $response = $this->ms2->invokeEvent('msOnAddToCart', [
            'key' => $key,
            'product' => $product,
            'count' => $count,
            'options' => $options,
            'cart' => $this,
        ]);
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $this->cart[$key] = $this->getCartItem($product, $key, $this->cart[$key]['options'], $this->cart[$key]['count']);

        return $this->success(
            'ms2_cart_add_success',
            $this->status([
                'key' => $key,
                'cart' => $this->cart,
                'html' => $this->getProductRow($key, $product->toArray())
            ]),
            ['count' => $count]
        );
    }

    public function getCartItem($product, $key, $options, $count)
    {
        $ctx_key = 'web';
        $ms2_cart_context = (bool)$this->modx->getOption('ms2_cart_context', null, '0', true);
        if (!$ms2_cart_context) {
            $ctx_key = $this->ctx;
        }

        $price = $this->cart[$key] ? $this->cart[$key]['price'] : $product->getPrice();
        $old_price = $this->cart[$key] ? $this->cart[$key]['old_price'] : $product->get('old_price');
        if ($price === $old_price) {
            $old_price = 0;
        }
        $discount_price = $old_price > 0 ? $old_price - $price : 0;
        $discount_percent = 0;
        if ($old_price > 0) {
            $discount_percent = round($discount_price / $old_price * 100, $this->config['percent_precision']);
        }

        $item = [
            'id' => $product->get('id'),
            'ctx' => $ctx_key,
            'key' => $key,

            'pagetitle' => $this->cart[$key]['pagetitle'] ?: $product->get('pagetitle'),
            'thumb' => $this->cart[$key]['thumb'] ?: $product->get('thumb'),
            'price' => $price,
            'old_price' => $old_price,
            'discount_price' => $discount_price,
            'discount_cost' => $discount_price * $count,
            'discount_percent' => $discount_percent,
            'weight' => $this->cart[$key]['weight'] ?: $product->getWeight(),
            'count' => $count,
            'options' => $this->cart[$key]['options'] ?: $options,
            'cost' => $price * $count,
            'old_cost' => $old_price * $count,
            'properties' => $this->cart[$key]['properties'] ?: ['original_price' => $product->getPrice(), 'original_old_price' => $product->get('old_price')]
        ];

        return $item;
    }

    public function getProductRow($key, $product)
    {
        $html = [];
        if (isset($_SESSION['minishop2']['msCart']) && is_array($_SESSION['minishop2']['msCart'])) {
            foreach ($_SESSION['minishop2']['msCart'] as $tplKey => $props) {
                $data = array_merge($product, $this->cart[$key]);
                $html[$tplKey] = $this->ms2->pdoTools->getChunk($props['tplRow'], array_merge($props, $data));
            }
        }
        return $html;
    }

    public function getProductKey($data)
    {
        $key_parts = explode(',', $this->modx->getOption('ms2_product_key_parts', '', 'id,options'));
        $key_str = '';

        foreach ($key_parts as $key_part) {
            if (isset($data[$key_part])) {
                if (is_array($data[$key_part])) {
                    $key_str .= json_encode($data[$key_part]);
                } else {
                    $key_str .= $data[$key_part];
                }
            }
        }
        return md5($key_str);
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
     * @param array $options
     *
     * @return array|string
     */
    public function change($key, $count, $options = [])
    {
        $status = [];
        $keyOld = false;
        $keyNew = false;
        $originKey = false;
        $lexicon = 'ms2_cart_change_success';


        if (!array_key_exists($key, $this->cart)) {
            return $this->error('ms2_cart_change_error', $this->status($status));
        }

        if ($count <= 0 && empty($options)) {
            return $this->remove($key);
        }

        if ($count <= 0 && !empty($options)) {
            $count = $this->cart[$key]['count'];
        }

        if ($count > $this->config['max_count']) {
            return $this->error('ms2_cart_add_err_count', $this->status(), ['count' => $count]);
        }

        $response = $this->ms2->invokeEvent(
            'msOnBeforeChangeInCart',
            ['key' => $key, 'count' => $count, 'cart' => $this, 'options' => $options]
        );
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $count = $response['data']['count'];
        $product = $this->modx->getObject('msProduct', $this->cart[$key]['id']);
        $productData = $product->toArray(); // товар нужен для получения ключа
        $productData['options'] = $response['data']['options'];

        if (!empty($productData['options'])) {
            $lexicon = 'ms2_cart_change_options_success';
            $keyNew = $this->getProductKey($productData);
            if (array_key_exists($keyNew, $this->cart) && $keyNew !== $key) { // если новый ключ есть в корзине
                $count = $this->cart[$keyNew]['count'] + $count; // объединяем количества товаров
                $this->cart = $this->storageHandler->change($keyNew, $count); // обновляем товар с новым ключом
                $this->cart[$keyNew]['options'] = $productData['options']; // обновляем опции
                $this->remove($key); // удаляем тот товар, который был до изменения опций
                $keyOld = $key;
            } else { // если ключа в корзине нет
                $this->add($this->cart[$key]['id'], $this->cart[$key]['count'], $productData['options']); // добавляем новый элемент
                $this->remove($key); // удаляем тот товар, который был до изменения опций
                $originKey = $key;
            }
            $key = $keyNew;
        } else {
            $this->cart = $this->storageHandler->change($key, $count);
        }

        $response = $this->ms2->invokeEvent(
            'msOnChangeInCart',
            ['key' => $key, 'count' => $count, 'cart' => $this]
        );
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $this->cart[$key] = $this->getCartItem($product, $key, $productData['options'], $count);

        $status['key'] = $originKey ?: $key;
        $status['key_old'] = $keyOld;
        $status['key_new'] = $keyNew;
        $status['cart'] = $this->cart;

        return $this->success(
            $lexicon,
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
            'total_old_cost' => 0,
            'total_weight' => 0,
            'total_discount' => 0,
            'total_discount_percent' => 0,
            'total_positions' => count($this->cart),
        ];
        foreach ($this->cart as $item) {
            if (empty($item['ctx']) || $item['ctx'] == $this->ctx) {
                $status['total_count'] += $item['count'];
                $status['total_cost'] += $item['price'] * $item['count'];
                $status['total_old_cost'] += $item['old_price'] * $item['count'];
                $status['total_weight'] += $item['weight'] * $item['count'];
                $status['total_discount'] += $item['discount_price'] * $item['count'];
            }
        }
        if ($status['total_old_cost'] > 0) {
            $status['total_discount_percent'] = round($status['total_discount'] / $status['total_old_cost'] * 100, $this->config['percent_precision']);
        }
        $status = array_merge($data, $status);

        $response = $this->ms2->invokeEvent('msOnGetStatusCart', [
            'status' => $status,
            'cart' => $this,
        ]);
        if ($response['success']) {
            $status = $response['data']['status'];
        }
        if ($status['total_count'] > 0) {
            $this->storageHandler->set($this->cart);
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
}
