<?php

class msOrderGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msOrder';
    public $languageTopics = array('default', 'minishop2:manager');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $permission = 'msorder_list';
    /** @var  miniShop2 $ms2 */
    protected $ms2;
    /** @var  xPDOQuery $query */
    protected $query;


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $this->ms2 = $this->modx->getService('miniShop2');

        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modUser', 'User');
        $c->leftJoin('modUserProfile', 'UserProfile');
        $c->leftJoin('msOrderStatus', 'Status');
        $c->leftJoin('msDelivery', 'Delivery');
        $c->leftJoin('msPayment', 'Payment');

        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            if (is_numeric($query)) {
                $c->andCondition(array(
                    'id' => $query,
                    //'OR:User.id' => $query,
                ));
            } else {
                $c->where(array(
                    'num:LIKE' => "{$query}%",
                    'OR:comment:LIKE' => "%{$query}%",
                    'OR:User.username:LIKE' => "%{$query}%",
                    'OR:UserProfile.fullname:LIKE' => "%{$query}%",
                    'OR:UserProfile.email:LIKE' => "%{$query}%",
                ));
            }
        }
        if ($status = $this->getProperty('status')) {
            $c->where(array(
                'status' => $status,
            ));
        }
        if ($customer = $this->getProperty('customer')) {
            $c->where(array(
                'user_id' => (int)$customer,
            ));
        }
        if ($date_start = $this->getProperty('date_start')) {
            $c->andCondition(array(
                'createdon:>=' => date('Y-m-d 00:00:00', strtotime($date_start)),
            ), null, 1);
        }
        if ($date_end = $this->getProperty('date_end')) {
            $c->andCondition(array(
                'createdon:<=' => date('Y-m-d 23:59:59', strtotime($date_end)),
            ), null, 1);
        }

        $this->query = $c;

        $c->select(
            $this->modx->getSelectColumns('msOrder', 'msOrder', '', array('status', 'delivery', 'payment'), true) . ',
            UserProfile.fullname as customer, User.username as customer_username,
            Status.name as status, Status.color, Delivery.name as delivery, Payment.name as payment'
        );

        return $c;
    }


    /**
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        if ($c->prepare() && $c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }


    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            $this->currentIndex++;
        }
        $list = $this->afterIteration($list);

        return $list;
    }


    /**
     * @param array $data
     *
     * @return array
     */
    public function prepareArray(array $data)
    {
        if (empty($data['customer'])) {
            $data['customer'] = $data['customer_username'];
        }

        $data['status'] = '<span style="color:#' . $data['color'] . ';">' . $data['status'] . '</span>';
        unset($data['color']);

        if (isset($data['cost'])) {
            $data['cost'] = $this->ms2->formatPrice($data['cost']);
        }
        if (isset($data['cart_cost'])) {
            $data['cart_cost'] = $this->ms2->formatPrice($data['cart_cost']);
        }
        if (isset($data['delivery_cost'])) {
            $data['delivery_cost'] = $this->ms2->formatPrice($data['delivery_cost']);
        }
        if (isset($data['weight'])) {
            $data['weight'] = $this->ms2->formatWeight($data['weight']);
        }

        $data['actions'] = array(
            array(
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('ms2_menu_update'),
                'action' => 'updateOrder',
                'button' => true,
                'menu' => true,
            ),
            array(
                'cls' => array(
                    'menu' => 'red',
                    'button' => 'red',
                ),
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('ms2_menu_remove'),
                'multiple' => $this->modx->lexicon('ms2_menu_remove_multiple'),
                'action' => 'removeOrder',
                'button' => true,
                'menu' => true,
            ),
            /*
            array(
                'cls' => '',
                'icon' => 'icon icon-cog actions-menu',
                'menu' => false,
                'button' => true,
                'action' => 'showMenu',
                'type' => 'menu',
            ),
            */
        );

        return $data;
    }


    /**
     * @param array $array
     * @param bool $count
     *
     * @return string
     */
    public function outputArray(array $array, $count = false)
    {
        if ($count === false) {
            $count = count($array);
        }

        $selected = $this->query;
        $selected->query['columns'] = array();
        $selected->query['limit'] =
        $selected->query['offset'] = 0;
        $selected->where(array('type' => 0));
        $selected->select('SUM(msOrder.cost)');
        $selected->prepare();
        $selected->stmt->execute();

        $month = $this->modx->newQuery($this->classKey);
        $month->where(array('status:IN' => array(2, 3), 'type' => 0));
        $month->where('createdon BETWEEN NOW() - INTERVAL 30 DAY AND NOW()');
        $month->select('SUM(msOrder.cost) as sum, COUNT(msOrder.id) as total');
        $month->prepare();
        $month->stmt->execute();
        $month = $month->stmt->fetch(PDO::FETCH_ASSOC);

        $data = array(
            'success' => true,
            'results' => $array,
            'total' => $count,
            'num' => number_format($count, 0, '.', ' '),
            'sum' => number_format(round($selected->stmt->fetchColumn()), 0, '.', ' '),
            'month_sum' => number_format(round($month['sum']), 0, '.', ' '),
            'month_total' => number_format($month['total'], 0, '.', ' '),
        );

        return json_encode($data);
    }

}

return 'msOrderGetListProcessor';
