<?php

class BaseDBController
{
    protected $modx;
    protected $ctx = 'web';
    protected $ms2;
    /**
     * @var msOrder $msOrder
     */
    protected $msOrder;
    protected $products;
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

    protected function getStorageOrder()
    {
        $status_draft = $this->modx->getOption('ms2_status_draft', null, 5);
        $where = ['status' => $status_draft];
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
        return $this->modx->getObject('msOrder', $q);
    }
}
