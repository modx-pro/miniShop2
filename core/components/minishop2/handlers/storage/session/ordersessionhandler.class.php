<?php

class OrderSessionHandler
{
    protected $modx;
    protected $ctx = 'web';
    protected $ms2;

    public function __construct(modX $modx, miniShop2 $ms2)
    {
        $this->modx = $modx;
        $this->ms2 = $ms2;
    }

    public function setContext($ctx)
    {
        $this->ctx = $ctx;
    }

    public function get()
    {
        return isset($_SESSION['minishop2']['order']) && is_array($_SESSION['minishop2']['order'])
            ? $_SESSION['minishop2']['order']
            : [];
    }

    public function add($key, $value = '')
    {
        if (!isset($_SESSION['minishop2']) || !is_array($_SESSION['minishop2'])) {
            $_SESSION['minishop2'] = [];
        }
        if (!isset($_SESSION['minishop2']['order']) || !is_array($_SESSION['minishop2']['order'])) {
            $_SESSION['minishop2']['order'] = [];
        }
        $_SESSION['minishop2']['order'][$key] = $value;
        return $this->get();
    }

    public function remove($key)
    {
        if (isset($_SESSION['minishop2']['order'][$key])) {
            unset($_SESSION['minishop2']['order'][$key]);
        }
        return $this->get();
    }

    public function clean()
    {
        if (isset($_SESSION['minishop2']['order'])) {
            unset($_SESSION['minishop2']['order']);
        }
        return $this->get();
    }

    public function getForSubmit($data)
    {
        $order = (array)$this->get();
        $createdon = date('Y-m-d H:i:s');
        /** @var msOrder $msOrder */
        $msOrder = $this->modx->newObject('msOrder');

        $orderData = array_merge($order, $data, [
            'createdon' => $createdon,
            'weight' => $data['cart_status']['total_weight'],
            'cost' => $data['cart_cost'] + $data['delivery_cost'],
            'status' => 0,
            'context' => $this->ctx,
        ]);
        $msOrder->fromArray($orderData);

        // Adding address
        /** @var msOrderAddress $address */
        $address = $this->modx->newObject('msOrderAddress');
        $address->fromArray(
            array_merge($order, [
                'user_id' => $data['user_id'],
                'createdon' => $createdon,
            ])
        );
        $msOrder->addOne($address);

        // Adding products
        $cart = $this->ms2->cart->get();
        $products = [];
        foreach ($cart as $v) {
            if ($tmp = $this->modx->getObject('msProduct', ['id' => $v['id']])) {
                $name = $tmp->get('pagetitle');
            } else {
                $name = '';
            }
            /** @var msOrderProduct $product */
            $product = $this->modx->newObject('msOrderProduct');
            $product->fromArray(
                array_merge($v, [
                    'product_id' => $v['id'],
                    'name' => $name,
                    'cost' => $v['price'] * $v['count'],
                ])
            );
            $products[] = $product;
        }
        $msOrder->addMany($products);
        return $msOrder;
    }
}
