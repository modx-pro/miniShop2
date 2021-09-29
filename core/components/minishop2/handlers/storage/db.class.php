<?php

class DB
{
    protected $modx;

    public function __construct(modX $modx)
    {
        $this->modx = $modx;
    }

    public function get()
    {
        return [];
    }

    public function set($cart)
    {

        return $this->get();
    }

    public function add($cartItem)
    {
        $msOrder = $this->modx->newObject('msOrder');
        $orderData = [
            'user_id' => 'user_id',
            'session_id' => session_id()
        ];
        $msOrder->fromArray($orderData);

        return $this->get();
    }

    public function change($key, $count)
    {

        return $this->get();
    }

    public function remove($key)
    {

        return $this->get();
    }

    public function clean($ctx)
    {

        return $this->get();
    }
}
