<?php

class OrderDBHandler
{
    protected $modx;
    protected $ctx = 'web';
    protected $ms2;
    /**
     * @var msOrder $msOrder
     */
    protected $msOrder;
    /**
     * @var msOrderAddress $address
     */
    protected $address;

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
        $msOrder->addOne($address);
        $msOrder->save();
        return $msOrder;
    }

    private function getStorageOrder()
    {
        $where = ['status' => 0];
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
        /** @var msOrder $msOrder */
        $msOrder = $this->modx->getObject('msOrder', $q);
        if (!$msOrder) {
            $this->modx->log(MODX::LOG_LEVEL_ERROR, 'msOrder not found with params' . print_r($where, 1));
        }

        return $msOrder;
    }
}
