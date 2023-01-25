<?php

class msOrderGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msOrder';
    public $languageTopics = ['default', 'minishop2:manager'];
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
        $c->leftJoin('msOrderAddress', 'Address');

        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            if (is_numeric($query)) {
                $c->andCondition([
                    'id' => $query,
                    'OR:Address.phone:LIKE' => "%{$query}%",
                    //'OR:User.id' => $query,
                ]);
            } else {
                $c->where([
                    'num:LIKE' => "{$query}%",
                    'OR:order_comment:LIKE' => "%{$query}%",
                    'OR:Address.comment:LIKE' => "%{$query}%",
                    'OR:User.username:LIKE' => "%{$query}%",
                    'OR:UserProfile.fullname:LIKE' => "%{$query}%",
                    'OR:UserProfile.email:LIKE' => "%{$query}%",
                    'OR:Address.phone:LIKE' => "%{$query}%",
                ]);
            }
        }
        if ($status = $this->getProperty('status')) {
            $c->where([
                'status' => $status,
            ]);
        }
        if ($customer = $this->getProperty('customer')) {
            $c->where([
                'user_id' => (int)$customer,
            ]);
        }
        if ($context = $this->getProperty('context')) {
            $c->where([
                'context' => $context,
            ]);
        }
        if ($date_start = $this->getProperty('date_start')) {
            $c->andCondition([
                'createdon:>=' => date('Y-m-d 00:00:00', strtotime($date_start)),
            ], null, 1);
        }
        if ($date_end = $this->getProperty('date_end')) {
            $c->andCondition([
                'createdon:<=' => date('Y-m-d 23:59:59', strtotime($date_end)),
            ], null, 1);
        }

        $this->query = clone $c;

        $c->select(
            $this->modx->getSelectColumns('msOrder', 'msOrder', '', ['status', 'delivery', 'payment'], true) . ',
            msOrder.status as status_id, msOrder.delivery as delivery_id, msOrder.payment as payment_id,
            UserProfile.fullname as customer, User.username as customer_username,
            Status.name as status, Status.color, Delivery.name as delivery, Payment.name as payment'
        );
        $c->groupby($this->classKey . '.id');

        return $c;
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $total = 0;
        $limit = (int)$this->getProperty('limit');
        $start = (int)$this->getProperty('start');

        $q = clone $c;
        $q->query['columns'] = ['SQL_CALC_FOUND_ROWS msOrder.id, fullname as customer'];
        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns(
            $sortClassKey,
            $this->getProperty('sortAlias', $sortClassKey),
            '',
            [$this->getProperty('sort')]
        );
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $q->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $q->limit($limit, $start);
        }

        $ids = [];
        if ($q->prepare() and $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $total = $this->modx->query('SELECT FOUND_ROWS()')->fetchColumn();
        }
        $ids = empty($ids) ? "(0)" : "(" . implode(',', $ids) . ")";
        $c->query['where'] = [
            [
                new xPDOQueryCondition(['sql' => 'msOrder.id IN ' . $ids, 'conjunction' => 'AND']),
            ]
        ];
        $c->sortby($sortKey, $this->getProperty('dir'));

        $this->setProperty('total', $total);

        return $c;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $c = $this->prepareQueryAfterCount($c);
        return [
            'results' => ($c->prepare() and $c->stmt->execute()) ? $c->stmt->fetchAll(PDO::FETCH_ASSOC) : [],
            'total' => (int)$this->getProperty('total'),
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = [];
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            $this->currentIndex++;
        }
        return $this->afterIteration($list);
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

        $data['actions'] = [
            [
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('ms2_menu_update'),
                'action' => 'updateOrder',
                'button' => true,
                'menu' => true,
            ],
            [
                'cls' => [
                    'menu' => 'red',
                    'button' => 'red',
                ],
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('ms2_menu_remove'),
                'multiple' => $this->modx->lexicon('ms2_menu_remove_multiple'),
                'action' => 'removeOrder',
                'button' => true,
                'menu' => true,
            ],
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
        ];

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
        $selected->query['columns'] = [];
        $selected->query['limit'] =
        $selected->query['offset'] = 0;
        $selected->where(['type' => 0]);
        $selected->select('SUM(msOrder.cost)');
        $selected->prepare();
        $selected->stmt->execute();

        $month = $this->modx->newQuery($this->classKey);
        $statuses = $this->modx->getOption('ms2_status_for_stat', null, '2,3');
        $statuses = array_map('trim', explode(',', $statuses));
        $month->where(['status:IN' => $statuses, 'type' => 0]);
        $month->where('createdon BETWEEN NOW() - INTERVAL 30 DAY AND NOW()');
        $month->select('SUM(msOrder.cost) as sum, COUNT(msOrder.id) as total');
        $month->prepare();
        $month->stmt->execute();
        $month = $month->stmt->fetch(PDO::FETCH_ASSOC);

        $data = [
            'success' => true,
            'results' => $array,
            'total' => $count,
            'num' => number_format($count, 0, '.', ' '),
            'sum' => number_format(round($selected->stmt->fetchColumn()), 0, '.', ' '),
            'month_sum' => number_format(round($month['sum']), 0, '.', ' '),
            'month_total' => number_format($month['total'], 0, '.', ' '),
        ];

        return json_encode($data);
    }
}

return 'msOrderGetListProcessor';
