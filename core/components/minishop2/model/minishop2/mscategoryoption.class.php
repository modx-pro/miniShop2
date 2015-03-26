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
            $value = $this->get('value');
            $key = $this->getOne('Option')->get('key');
            foreach ($products as $id) {
                $po = $this->xpdo->getObject('msProductOption', array('key' => $key, 'product_id' => $id));
                // дефолтные значения применяются только к тем товарам, у которых их еще нет
                if (!$po) {
                    /* @TODO вызывать метод msOption для поддержки множественных типов  */
                    $po = $this->xpdo->newObject('msProductOption');
                    $po->set('product_id', $id);
                    $po->set('key', $key);
                    $po->set('value', $value);
                    $po->save();
                }
            }
        }
        return $save;
    }

    /**
     * {@inheritdoc}
     * Delete option values for product in category while remove option from category
     */
    public function remove(array $ancestors= array ()) {
        $q = $this->xpdo->newQuery('msProduct', array('parent' => $this->get('category_id')));
        $q->select('id');
        if ($q->prepare() && $q->stmt->execute()) {
            $products = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $products = implode(',', $products);
            $key = $this->getOne('Option')->get('key');
            $key = $this->xpdo->quote($key);
            if (count($products) > 0) {
                $sql = "DELETE FROM {$this->xpdo->getTableName('msProductOption')} WHERE `product_id` IN ({$products}) AND `key`={$key};";
                $stmt = $this->xpdo->prepare($sql);
                $stmt->execute();
                $stmt->closeCursor();
            }
        }

        return parent::remove($ancestors);
    }
}