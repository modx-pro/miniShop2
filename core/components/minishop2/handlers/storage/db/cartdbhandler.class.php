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
        $this->msOrder = $msOrder;
        $this->products = $this->msOrder->getMany('Products');
        foreach ($this->products as $product) {
            $properties = $product->get('properties');
            $cartItem = [
                'id' => $product->get('product_id'),
                'price' => $product->get('price'),
                'old_price' => $properties['old_price'],
                'discount_price' => $properties['discount_price'],
                'discount_cost' => $properties['discount_cost'],
                'weight' => $product->get('weight'),
                'count' => $product->get('count'),
                'options' => $product->get('options'),
                'ctx' => $properties['ctx'],
                'key' => $properties['key'],
            ];
            $output[$properties['key']] = $cartItem;
        }

        return $output;
    }

    public function set($cart)
    {
        $this->clean();
        foreach ($cart as $cartItem) {
            $this->add($cartItem);
        }
        return $this->get();
    }

    public function add($cartItem)
    {
        $cost = 0;
        $cartCost = 0;
        $weight = 0;

        $this->msOrder = $this->getStorageOrder();
        if (!$this->msOrder) {
            $status = 999;
            $this->msOrder = $this->modx->newObject('msOrder');
            $orderData = [
                'user_id' => $this->modx->getLoginUserID($this->ctx),
                'session_id' => session_id(),
                'createdon' => time(),
                'cost' => $cost,
                'cart_cost' => $cartCost,
                'weight' => $weight,
                'status' => $status,
                'context' => $cartItem['ctx'],
            ];
            $this->msOrder->fromArray($orderData);

            // Adding address
            /** @var msOrderAddress $address */
            $address = $this->modx->newObject('msOrderAddress', [
                'user_id' => $this->modx->getLoginUserID($this->ctx),
                'createdon' => time(),
            ]);
            $this->msOrder->addOne($address);
        } else {
            $this->msOrder->set('updatedon', time());
            $this->products = $this->msOrder->getMany('Products');
            foreach ($this->products as $product) {
                $cartCost += $product->get('price') * $product->get('count');
                $weight += $product->get('weight');
            }
        }

        // Adding products
        $products = [];
        $msProduct = $this->modx->getObject(
            'msProduct',
            array(
                'id' => $cartItem['id'],
                'class_key' => 'msProduct',
                'deleted' => 0,
            )
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
            'cost' => $cartItem['price'] * $cartItem['count'],
            'properties' => $cartItem
        ];
        $product->fromArray(array_merge($cartItem, $productData));
        $products[] = $product;
        $this->msOrder->addMany($products);

        $cartCost += $cartItem['price'] * $cartItem['count'];
        $weight += $cartItem['weight'];
        $cost = $cartCost;

        $this->msOrder->set('cost', $cost);
        $this->msOrder->set('cart_cost', $cartCost);
        $this->msOrder->set('weight', $weight);
        $this->msOrder->save();

        return $this->get();
    }

    public function change($key, $count)
    {
        $cartCost = 0;
        $weight = 0;

        foreach ($this->products as $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                $properties['count'] = $count;
                $cost = $product->get('price') * $count;
                $weight = $product->get('weight') * $count;

                $product->set('count', $count);
                $product->set('cost', $cost);
                $product->set('weight', $weight);
                $product->set('properties', $properties);
                $product->save();
            }

            $cartCost += $product->get('cost');
            $weight += $product->get('weight');
        }

        $cost = $cartCost;
        $this->msOrder->set('cost', $cost);
        $this->msOrder->set('cart_cost', $cartCost);
        $this->msOrder->set('weight', $weight);
        $this->msOrder->set('updatedon', time());
        $this->msOrder->save();

        return $this->get();
    }

    public function remove($key)
    {
        $cartCost = 0;
        $weight = 0;
        foreach ($this->products as $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                $product->remove();
            } else {
                $cartCost += $product->get('cost');
                $weight += $product->get('weight');
            }
        }
        $count = $this->modx->getCount('msOrderProduct', ['order_id' => $this->msOrder->get('id')]);
        if ($count === 0) {
            $this->msOrder->remove();
        } else {
            $cost = $cartCost;
            $this->msOrder->set('cost', $cost);
            $this->msOrder->set('cart_cost', $cartCost);
            $this->msOrder->set('weight', $weight);
            $this->msOrder->set('updatedon', time());
            $this->msOrder->save();
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
}
