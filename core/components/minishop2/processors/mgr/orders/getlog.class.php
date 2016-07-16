<?php

class msOrderLogGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msOrderLog';
    public $languageTopics = array('default', 'minishop2:manager');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $permission = 'msorder_view';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
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
        $type = $this->getProperty('type');
        if (!empty($type)) {
            $c->where(array('action' => $type));
        }
        $order_id = $this->getProperty('order_id');
        if (!empty($order_id)) {
            $c->where(array('order_id' => $order_id));
        }

        $c->leftJoin('modUser', 'modUser', '`msOrderLog`.`user_id` = `modUser`.`id`');
        $c->leftJoin('modUserProfile', 'modUserProfile', '`msOrderLog`.`user_id` = `modUserProfile`.`internalKey`');
        $exclude = array();
        $add_select = ' , `modUser`.`username`, `modUserProfile`.`fullname`';
        if ($type == 'status') {
            $c->leftJoin('msOrderStatus', 'msOrderStatus', '`msOrderLog`.`entry` = `msOrderStatus`.`id`');
            $exclude[] = 'entry';
            $add_select .= ', `msOrderStatus`.`name` as `entry`, `msOrderStatus`.`color`';
        }

        $select = $this->modx->getSelectColumns('msOrderLog', 'msOrderLog', '', $exclude, true);
        $select .= $add_select;

        $c->select($select);

        return $c;
    }


    /** {@inheritDoc} */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
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
        if (!empty($data['color'])) {
            $data['entry'] = '<span style="color:#' . $data['color'] . ';">' . $data['entry'] . '</span>';
        }

        return $data;
    }

}

return 'msOrderLogGetListProcessor';