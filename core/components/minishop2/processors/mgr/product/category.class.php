<?php

class msProductCategoryMemberProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msCategoryMember';
    public $permission = 'msproduct_save';


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
     * @return array|string
     */
    public function process()
    {
        $pid = $this->getProperty('product_id');
        $cid = $this->getProperty('category_id');
        if ($pid > 0 && $cid > 0) {
            /** @var msCategoryMember $res */
            $res = $this->modx->getObject('msCategoryMember', array('category_id' => $cid, 'product_id' => $pid));
            if (!$res) {
                $res = $this->modx->newObject('msCategoryMember');
                $res->set('product_id', $pid);
                $res->set('category_id', $cid);
                $res->save();
            } else {
                $table = $this->modx->getTableName('msCategoryMember');
                $this->modx->exec("DELETE FROM {$table} WHERE `product_id` = {$pid} AND `category_id` = {$cid};");
            }
        }

        return $this->success('');
    }

}

return 'msProductCategoryMemberProcessor';