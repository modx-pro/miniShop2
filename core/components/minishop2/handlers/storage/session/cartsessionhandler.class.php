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
        return isset($_SESSION['minishop2']['cart']) && is_array($_SESSION['minishop2']['cart'])
            ? $_SESSION['minishop2']['cart']
            : [];
    }

    public function set($cart)
    {
        if (!isset($_SESSION['minishop2']) || !is_array($_SESSION['minishop2'])) {
            $_SESSION['minishop2'] = [];
        }
        $_SESSION['minishop2']['cart'] = is_array($cart) ? $cart : [];
        return $this->get();
    }

    public function add($cartItem)
    {
        $key = $cartItem['key'];
        if (!isset($_SESSION['minishop2']) || !is_array($_SESSION['minishop2'])) {
            $_SESSION['minishop2'] = [];
        }
        if (!isset($_SESSION['minishop2']['cart']) || !is_array($_SESSION['minishop2']['cart'])) {
            $_SESSION['minishop2']['cart'] = [];
        }
        $_SESSION['minishop2']['cart'][$key] = $cartItem;
        return $this->get();
    }

    public function change($key, $count)
    {
        if (isset($_SESSION['minishop2']['cart'][$key]) && is_array($_SESSION['minishop2']['cart'][$key])) {
            $_SESSION['minishop2']['cart'][$key]['count'] = $count;
        }
        return $this->get();
    }

    public function remove($key)
    {
        if (isset($_SESSION['minishop2']['cart'][$key])) {
            unset($_SESSION['minishop2']['cart'][$key]);
        }
        return $this->get();
    }

    public function clean($ctx)
    {
        if (!empty($_SESSION['minishop2']['cart']) && is_array($_SESSION['minishop2']['cart'])) {
            foreach ($_SESSION['minishop2']['cart'] as $key => $item) {
                if (empty($item['ctx']) || $item['ctx'] == $ctx) {
                    unset($_SESSION['minishop2']['cart'][$key]);
                }
            }
        }
        return $this->get();
    }

    public function setContext($ctx)
    {
        $this->ctx = $ctx;
    }
}
