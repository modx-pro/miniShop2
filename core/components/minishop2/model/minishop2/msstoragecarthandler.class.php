<?php

class msStorageCartHandler
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

    public function add($data)
    {
        if (empty($this->storage_user_id)) {
            return false;
        }
        $msStorageCart = $this->modx->getObject('msStorageCart', ['user_id' => $this->storage_user_id]);
        if (!$msStorageCart) {
            $cart = [
                'user_id' => $this->storage_user_id,
                'createdon' => time()
            ];
            $msStorageCart = $this->modx->newObject('msStorageCart', $cart);
            $msStorageCart->save();
        }
        //TODO Здесь вероятно уместно запустить обновление корзины, если она существует.
        $data['cart_id'] = $msStorageCart->get('id');
        $data['user_id'] = $this->storage_user_id;
        $data['createdon'] = time();
        //key is reserved word for DB, rename it to product_key
        $data['product_key'] = $data['key'];
        unset($data['key']);
        //id is reserved field for DB, rename it to product_id
        $data['product_id'] = $data['id'];
        unset($data['id']);
        $msStorageCartItem = $this->modx->newObject('msStorageCartItem', $data);
        $msStorageCartItem->save();
    }

    public function remove($key)
    {
        if (empty($this->storage_user_id)) {
            return false;
        }

        $msStorageCartItem = $this->modx->getObject(
            'msStorageCartItem',
            ['product_key' => $key, 'user_id' => $this->storage_user_id]
        );
        if ($msStorageCartItem) {
            $cart_id = $msStorageCartItem->get('cart_id');
            $msStorageCartItem->remove();

            $count = $this->modx->getCount('msStorageCartItem', ['cart_id' => $cart_id]);
            if ($count === 0) {
                $this->clean($cart_id);
            } else {
                $msStorageCart = $this->modx->getObject('msStorageCart', ['id' => $cart_id]);
                if ($msStorageCart) {
                    $msStorageCart->set('updatedon', time());
                    $msStorageCart->save();
                }
            }
        }
    }

    public function change($key, $count)
    {
        if (empty($this->storage_user_id)) {
            return false;
        }

        $msStorageCartItem = $this->modx->getObject(
            'msStorageCartItem',
            ['product_key' => $key, 'user_id' => $this->storage_user_id]
        );
        if ($msStorageCartItem) {
            $msStorageCartItem->set('count', $count);
            $msStorageCartItem->set('updatedon', time());
            $msStorageCartItem->save();

            $msStorageCart = $msStorageCartItem->getOne('Cart');
            if ($msStorageCart) {
                $msStorageCart->set('updatedon', time());
                $msStorageCart->save();
            }
        }
    }

    public function clean($cart_id = 0)
    {
        if (empty($this->storage_user_id)) {
            return false;
        }

        if ($cart_id > 0) {
            $this->cleanById($cart_id);
        } else {
            $this->cleanAll();
        }
    }

    public function get()
    {
        $cart = [];
        if (empty($this->storage_user_id)) {
            return $cart;
        }

        $msStorageCart = $this->modx->getObject('msStorageCart', ['user_id' => $this->storage_user_id]);
        if ($msStorageCart) {
            $items = $msStorageCart->getMany('Items');
            if (is_array($items) && count($items) > 0) {
                foreach ($items as $item) {
                    $key = $item->get('product_key');
                    $cart[$key] = $item->toArray();
                    $cart[$key]['key'] = $key;
                    unset($cart[$key]['product_key']);

                    unset($cart[$key]['id']);
                    $cart[$key]['id'] = $item->get('product_id');
                    unset($cart[$key]['product_id']);
                }
            }
        }
        return $cart;
    }

    public function set($cart)
    {
        if (!empty($cart)) {
            $this->clean();
            foreach ($cart as $cartItem) {
                $this->add($cartItem);
            }
        }

        return $this->get();
    }

    private function cleanById($cart_id)
    {
        $msStorageCart = $this->modx->getObject('msStorageCart', ['id' => $cart_id]);
        if ($msStorageCart) {
            $msStorageCart->remove();
            $this->modx->removeCollection('msStorageCartItem', ['cart_id' => $cart_id]);
        } else {
            $this->modx->log(MODX::LOG_LEVEL_ERROR, "cart with id {$cart_id} not found");
        }
    }

    private function cleanAll()
    {
        $msStorageCarts = $this->modx->getIterator('msStorageCart', ['user_id' => $this->storage_user_id]);
        $msStorageCarts->rewind();
        if ($msStorageCarts->valid()) {
            foreach ($msStorageCarts as $cart) {
                $cart_id = $cart->get('id');
                $cart->remove();
                $this->modx->removeCollection('msStorageCartItem', ['cart_id' => $cart_id]);
            }
        }
    }
}
