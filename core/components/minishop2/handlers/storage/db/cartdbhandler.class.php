<?php

require_once 'baseDBController.php';

class CartDBHandler extends BaseDBController
{
    public function __construct(modX $modx, miniShop2 $ms2)
    {
        parent::__construct($modx, $ms2);
    }

    public function get()
    {
        $output = [];

        $msOrder = $this->getStorageOrder();

        if (!$msOrder) {
            return $output;
        }

        //Необходимо для корректного перехода между сессиями гостя и логина
        if (isset($_SESSION['prev_session_id'])) {
            $msOrder->set('session_id', session_id());
            $msOrder->save();
            unset($_SESSION['prev_session_id']);
        }
        $this->msOrder = $msOrder;
        $this->products = $this->msOrder->getMany('Products');
        foreach ($this->products as $product) {
            $properties = $product->get('properties');
            $cartItem = [
                'id' => $product->get('product_id'),
                'price' => $product->get('price'),
                'old_price' => $properties['old_price'] ?? 0,
                'discount_price' => $properties['discount_price'] ?? 0,
                'discount_cost' => $properties['discount_cost'] ?? 0,
                'weight' => $product->get('weight'),
                'count' => $product->get('count'),
                'options' => $product->get('options'),
                'ctx' => $properties['ctx'],
                'key' => $properties['key'],
                'properties' => $properties
            ];
            $output[$properties['key']] = $cartItem;
        }

        return $output;
    }

    public function set($cart)
    {
        $this->msOrder = $this->getStorageOrder();
        if (!$this->msOrder) {
            $this->createMsOrder();
        }
        $this->products = $this->msOrder->getMany('Products');
        $this->removeOrderProductNotInCart($cart);
        foreach ($cart as $key => $cartItem) {
            $orderProductIsExists = $this->orderProductIsExists($key);
            if ($orderProductIsExists) {
                $this->updateOrderProduct($key, $cartItem);
            } else {
                $this->add($cartItem);
            }
        }
        $this->msOrder->save();
        $this->restrictOrder();

        return $this->get();
    }

    public function add($cartItem)
    {
        $this->msOrder = $this->getStorageOrder();
        if (!$this->msOrder) {
            $this->createMsOrder();
        }

        // Adding products
        $products = [];
        $msProduct = $this->modx->getObject(
            'msProduct',
            [
                'id' => $cartItem['id'],
                'class_key' => 'msProduct',
                'deleted' => 0,
            ]
        );
        if ($msProduct) {
            $name = $msProduct->get('pagetitle');
        } else {
            $name = '';
        }
        /** @var msOrderProduct $product */
        $product = $this->modx->newObject('msOrderProduct');

        $productData = [
            'product_id' => $cartItem['id'],
            'name' => $name,
            'count' => $cartItem['count'],
            'price' => $cartItem['price'],
            'weight' => $cartItem['weight'],
            'cost' => $cartItem['price'] * $cartItem['count'],
            'options' => $cartItem['options'],
            'properties' => $cartItem
        ];
        $product->fromArray(array_merge($cartItem, $productData));

        $products[] = $product;
        $this->msOrder->addMany($products);
        $this->msOrder->save();
        $this->products = $this->msOrder->getMany('Products');

        $this->restrictOrder();

        return $this->get();
    }

    public function change($key, $count)
    {
        if (empty($this->products)) {
            return $this->get();
        }

        foreach ($this->products as $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                $properties['count'] = $count;
                $price = $product->get('price');
                $product->set('properties', $properties);
                $product->set('count', $count);
                $product->set('cost', $price * $count);
                $product->save();
            }
        }
        $this->msOrder->save();
        $this->restrictOrder();
        return $this->get();
    }

    public function remove($key)
    {
        if (empty($this->products)) {
            return $this->get();
        }
        foreach ($this->products as $k => $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                $product->remove();
                unset($this->products[$k]);
            }
        }
        $count = $this->modx->getCount('msOrderProduct', ['order_id' => $this->msOrder->get('id')]);
        if ($count === 0) {
            $this->msOrder->remove();
            $this->products = null;
            $this->msOrder = null;
        } else {
            $this->msOrder->save();
            $this->restrictOrder();
        }
        return $this->get();
    }

    public function clean($ctx = '')
    {
        if ($this->msOrder) {
            $this->msOrder->remove();
        }

        return $this->get();
    }

    protected function removeOrderProductNotInCart($cart)
    {
        if (empty($this->products)) {
            return;
        }
        foreach ($this->products as $k => $product) {
            $properties = $product->get('properties');
            $key = $properties['key'];
            if (!isset($cart[$key])) {
                $product->remove();
                unset($this->products[$k]);
            }
        }
    }

    protected function orderProductIsExists($key)
    {
        if (empty($this->products)) {
            return false;
        }
        foreach ($this->products as $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                return true;
            }
        }

        return false;
    }

    protected function updateOrderProduct($key, $data)
    {
        /** @var msOrderProduct $product */
        foreach ($this->products as $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                $msOrderProductProperties = $data['properties'];
                unset($data['properties']);
                $msOrderProductProperties = array_merge($msOrderProductProperties, $data);
                $product->fromArray([
                    'price' => $data['price'],
                    'cost' => $data['cost'] ?? $data['price'] * $data['count'],
                    'count' => $data['count'],
                    'options' => $data['options'],
                    'properties' => $msOrderProductProperties,
                ]);
                $product->save();
            }
        }
    }

    protected function restrictOrder()
    {
        $cartCost = 0;
        $weight = 0;

        foreach ($this->products as $product) {
            $cartCost += $product->get('cost');
            $weight += $product->get('weight');
        }

        $delivery_cost = $this->msOrder->get('delivery_cost');
        $cost = $cartCost + $delivery_cost;
        $this->msOrder->set('cost', $cost);
        $this->msOrder->set('cart_cost', $cartCost);
        $this->msOrder->set('weight', $weight);
        $this->msOrder->set('updatedon', time());
        $this->msOrder->save();
    }

    protected function createMsOrder()
    {
        $status_draft = $this->modx->getOption('ms2_status_draft', null, 5);
        $this->msOrder = $this->modx->newObject('msOrder');
        $orderData = [
            'user_id' => $this->modx->getLoginUserID($this->ctx),
            'session_id' => session_id(),
            'createdon' => time(),
            'cost' => 0,
            'cart_cost' => 0,
            'weight' => 0,
            'status' => $status_draft,
            'context' => $this->ctx,
        ];
        $this->msOrder->fromArray($orderData);

        // Adding address
        /** @var msOrderAddress $address */
        $address = $this->modx->newObject('msOrderAddress', [
            'user_id' => $this->modx->getLoginUserID($this->ctx),
            'createdon' => time(),
        ]);
        $this->msOrder->addOne($address);
        $this->msOrder->save();
    }
}
