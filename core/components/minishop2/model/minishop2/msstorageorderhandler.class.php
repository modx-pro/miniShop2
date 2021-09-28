<?php

class msStorageOrderHandler
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    protected $storage_user_id;


    /**
     * msStorageHandler constructor.
     *
     * @param miniShop2 $ms2
     */
    public function __construct(miniShop2 $ms2)
    {
        $this->ms2 = $ms2;
        $this->modx = $ms2->modx;
    }

    public function initialize($ctx)
    {
        $user_id = $this->modx->getLoginUserID($ctx);
        if ($user_id > 0) {
            $this->storage_user_id = $user_id;
        } else {
            $this->storage_user_id = $_COOKIE['PHPSESSID'];
        }
        if (!empty($this->storage_user_id)) {
            $cart = $this->get();
        }

        if (empty($cart) || !is_array($cart)) {
            $cart = [];
        }

        return $cart;
    }

    public function get()
    {
        $order = [];
        if (empty($this->storage_user_id)) {
            return $order;
        }
        $msStorageOrder = $this->modx->getObject('msStorageOrder', ['user_id' => $this->storage_user_id]);
        if ($msStorageOrder) {
            $order = $msStorageOrder->toArray();
            unset($order['id']);
        }
        return $order;
    }

    public function add($key, $value)
    {
        if (empty($this->storage_user_id)) {
            return false;
        }
        $msStorageOrder = $this->modx->getObject('msStorageOrder', ['user_id' => $this->storage_user_id]);
        if (!$msStorageOrder) {
            $order = [
                'user_id' => $this->storage_user_id,
                'createdon' => time(),
                $key => $value
            ];
            $msStorageOrder = $this->modx->newObject('msStorageOrder', $order);
        } else {
            $msStorageOrder->set('publishedon', time());
            $msStorageOrder->set($key, $value);
        }
        $msStorageOrder->save();
    }

    public function remove($key)
    {
        if (empty($this->storage_user_id)) {
            return false;
        }
        /** @var msStorageOrder $msStorageOrder */
        $msStorageOrder = $this->modx->getObject('msStorageOrder', ['user_id' => $this->storage_user_id]);
        if ($msStorageOrder) {
            $msStorageOrder->set('publishedon', time());
            $msStorageOrder->set($key);
            $msStorageOrder->save();
        }
    }

    public function clean()
    {
        if (empty($this->storage_user_id)) {
            return false;
        }
        $this->modx->removeObject('msStorageOrder', ['user_id' => $this->storage_user_id]);
    }
}
