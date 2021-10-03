<?php

class CartDBHandler
{
    protected $modx;
    protected $ctx = 'web';

    public function __construct(modX $modx)
    {
        $this->modx = $modx;
    }

    public function get()
    {
        $output = [];

        $msOrder = $this->getMsOrder();
        if (!$msOrder) {
            return $output;
        }
        $products = $msOrder->getMany('Products');
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

        return $this->get();
    }

    public function add($cartItem)
    {
        $cost = 0;
        $cartCost = 0;
        $weight = 0;

        $msOrder = $this->getMsOrder();
        if (!$msOrder) {
            $status = 0;
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
        $msOrder = $this->getMsOrder();
        if (!$msOrder) {
            return [];
        }
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
        }
        return $this->get();
    }

    public function remove($key)
    {
        $msOrder = $this->getMsOrder();
        if (!$msOrder) {
            return [];
        }
        $products = $msOrder->getMany('Products');
        foreach ($products as $product) {
            $properties = $product->get('properties');
            if ($key === $properties['key']) {
                $product->remove();
            }
        }
        $count = $this->modx->getCount('msOrderProduct', ['order_id' => $msOrder->get('id')]);
        if ($count === 0) {
            $msOrder->remove();
        }
        return $this->get();
    }

    public function clean($ctx)
    {
        $msOrder = $this->getMsOrder();
        if ($msOrder) {
            $msOrder->remove();
        }

        return $this->get();
    }

    public function setContext($ctx)
    {
        $this->ctx = $ctx;
    }

    private function getMsOrder()
    {
        $where = [];
        $user_id = $this->modx->getLoginUserID($this->ctx);
        if ($user_id > 0) {
            //TODO реализовать вопрос склеивания корзин анонима и залогиненного юзера
            $where['user_id'] = $user_id;
        } else {
            $where['session_id'] = session_id();
        }

        $q = $this->modx->newQuery('msOrder');
        $q->sortby('updatedon', 'DESC');
        $q->where($where);
        return $this->modx->getObject('msOrder', $q);
    }
}
