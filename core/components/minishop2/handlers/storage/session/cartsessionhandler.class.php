<?php

class CartSessionHandler
{
    protected $modx;
    protected $ctx = 'web';

    public function __construct(modX $modx)
    {
        $this->modx = $modx;
    }

    public function get()
    {
        return $_SESSION['minishop2']['cart'];
    }

    public function set($cart)
    {
        $_SESSION['minishop2']['cart'] = $cart;
        return $this->get();
    }

    public function add($cartItem)
    {
        $key = $cartItem['key'];
        $_SESSION['minishop2']['cart'][$key] = $cartItem;
        return $this->get();
    }

    public function change($key, $count)
    {
        $_SESSION['minishop2']['cart'][$key]['count'] = $count;
        return $this->get();
    }

    public function remove($key)
    {
        unset($_SESSION['minishop2']['cart'][$key]);
        return $this->get();
    }

    public function clean($ctx)
    {
        foreach ($_SESSION['minishop2']['cart'] as $key => $item) {
            if (empty($item['ctx']) || $item['ctx'] == $ctx) {
                unset($_SESSION['minishop2']['cart'][$key]);
            }
        }
        return $this->get();
    }

    public function setContext($ctx)
    {
        $this->ctx = $ctx;
    }
}
