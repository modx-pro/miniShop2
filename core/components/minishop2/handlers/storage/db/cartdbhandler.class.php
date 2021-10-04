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
        $products = $this->msOrder->getMany('Products');
        foreach ($products as $product) {
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

        $msOrder = $this->getStorageOrder();
        if (!$msOrder) {
            $status = 999;
            $msOrder = $this->modx->newObject('msOrder');
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
            $msOrder->fromArray($orderData);

            // Adding address
            /** @var msOrderAddress $address */
            $address = $this->modx->newObject('msOrderAddress', [
                'user_id' => $this->modx->getLoginUserID($this->ctx),
                'createdon' => time(),
            ]);
            $msOrder->addOne($address);
        } else {
            $msOrder->set('updatedon', time());
            $orderProducts = $msOrder->getMany('Products');
            foreach ($orderProducts as $product) {
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
        $msOrder->addMany($products);

        $cartCost += $cartItem['price'] * $cartItem['count'];
        $weight += $cartItem['weight'];
        $cost = $cartCost;

        $msOrder->set('cost', $cost);
        $msOrder->set('cart_cost', $cartCost);
        $msOrder->set('weight', $weight);
        $msOrder->save();

        return $this->get();
    }

    public function change($key, $count)
    {
        $msOrder = $this->getStorageOrder();
        if (!$msOrder) {
            return [];
        }
        $cartCost = 0;
        $weight = 0;
        $products = $msOrder->getMany('Products');

        foreach ($products as $product) {
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
        $msOrder->set('cost', $cost);
        $msOrder->set('cart_cost', $cartCost);
        $msOrder->set('weight', $weight);
        $msOrder->set('updatedon', time());
        $msOrder->save();

        return $this->get();
    }

    public function remove($key)
    {
        $msOrder = $this->getStorageOrder();
        if (!$msOrder) {
            return [];
        }
        $cartCost = 0;
        $weight = 0;
        $products = $msOrder->getMany('Products');
        foreach ($products as $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                $product->remove();
            } else {
                $cartCost += $product->get('cost');
                $weight += $product->get('weight');
            }
        }
        $count = $this->modx->getCount('msOrderProduct', ['order_id' => $msOrder->get('id')]);
        if ($count === 0) {
            $msOrder->remove();
        } else {
            $cost = $cartCost;
            $msOrder->set('cost', $cost);
            $msOrder->set('cart_cost', $cartCost);
            $msOrder->set('weight', $weight);
            $msOrder->set('updatedon', time());
            $msOrder->save();
        }
        return $this->get();
    }

    public function clean($ctx = '')
    {
        $msOrder = $this->getStorageOrder();
        if ($msOrder) {
            $msOrder->remove();
        }

        return $this->get();
    }
}
