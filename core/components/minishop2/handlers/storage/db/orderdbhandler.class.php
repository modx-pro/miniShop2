<?php

require_once 'baseDBController.php';

class OrderDBHandler extends BaseDBController
{
    public function __construct(modX $modx, miniShop2 $ms2)
    {
        parent::__construct($modx, $ms2);
    }

    public function get()
    {
        $output = [];
        $msOrder = $this->getStorageOrder();
        if ($msOrder) {
            $this->msOrder = $msOrder;
            $this->address = $msOrder->getOne('Address');
            if ($this->address) {
                $output = $this->address->toArray();
                $excludeFields = ['id', 'createdon', 'updatedon', 'user_id', 'properties'];
                foreach ($excludeFields as $field) {
                    unset($output[$field]);
                }
                $output['delivery'] = $msOrder->get('delivery');
                $output['payment'] = $msOrder->get('payment');
            }
        }

        return $output;
    }

    public function add($key, $value = '')
    {
        if (empty($this->msOrder)) {
            return $this->get();
        }
        switch ($key) {
            case 'delivery':
            case 'payment':
                $this->msOrder->set($key, $value);
                break;
            default:
                $this->address->set($key, $value);
                $this->address->set('updatedon', time());
                $this->address->save();
        }
        $this->msOrder->set('updatedon', time());
        $this->msOrder->save();
        return $this->get();
    }

    public function remove($key)
    {
        if (empty($this->msOrder)) {
            return $this->get();
        }
        switch ($key) {
            case 'delivery':
            case 'payment':
                $this->msOrder->set($key);
                break;
            default:
                $this->address->set($key);
                $this->address->save();
        }
        $this->msOrder->set('updatedon', time());
        $this->msOrder->save();
        return $this->get();
    }

    public function clean()
    {
        if (empty($this->msOrder)) {
            return [];
        }
        $fields = $this->get();
        foreach ($fields as $key => $value) {
            switch ($key) {
                case 'delivery':
                case 'payment':
                    $this->msOrder->set($key);
                    break;
                default:
                    $this->address->set($key);
                    $this->address->save();
            }
        }
        $this->msOrder->set('updatedon', time());
        $this->msOrder->save();
        return [];
    }

    public function getForSubmit($data)
    {
        $msOrder = $this->getStorageOrder();
        $msOrder->fromArray([
            'user_id' => $data['user_id'],
            'updatedon' => time(),
            'num' => $data['num'],
            'delivery_cost' => $data['delivery_cost'],
            'cost' => $data['cart_cost'] + $data['delivery_cost'],
        ]);

        $msOrder->Address->fromArray([
            'user_id' => $data['user_id'],
            'updatedon' => time(),
        ]);
        $msOrder->save();
        return $msOrder;
    }

    public function setDeliveryCost($delivery_cost)
    {
        $cart_cost = $this->msOrder->get('cart_cost');
        $cost = $cart_cost + $delivery_cost;

        $this->msOrder->set('delivery_cost', $delivery_cost);
        $this->msOrder->set('cost', $cost);
        $this->msOrder->save();
    }
}
