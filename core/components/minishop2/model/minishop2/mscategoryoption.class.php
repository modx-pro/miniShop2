<?php
class msCategoryOption extends xPDOObject {

    /**
     * {@inheritdoc}
     * Create option values for product in category
     */
    public function save($cacheFlag= null) {
        $save = parent::save();

        /** @var xPDOQuery $q */
        $q = $this->xpdo->newQuery('msProduct', array('parent' => $this->get('category_id')));
        $q->select('id');
        if ($q->prepare() && $q->stmt->execute()) {
            $products = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $rows = array();
            $key = $this->getOne('Option')->get('key');
            $value = $this->get('value');
            foreach ($products as $id) {
                $rows[] = "('{$id}','{$key}','{$value}')";
            }

            if (count($rows) > 0) {
                $sql = "INSERT INTO {$this->xpdo->getTableName('msProductOption')} (`product_id`,`key`,`value`) VALUES ";
                $sql .= implode(',', $rows);
                $sql .= " ON DUPLICATE KEY UPDATE `value` = '{$value}';";
                $this->xpdo->exec($sql);
            }
        }
        return $save;
    }
}