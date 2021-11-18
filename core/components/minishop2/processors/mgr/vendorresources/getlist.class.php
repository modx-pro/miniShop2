<?php

class msVendorResourcesGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = array('resource');
    public $defaultSortField = 'pagetitle';
    public $permission = 'view';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $where = ['published' => true];
        if ($vendors = $this->modx->getOption('ms2_vendor_parents')) {
            $vendors = array_map('trim', explode(',', $vendors));
            $where = array_merge($where, ['parent:IN' => $vendors]);
        }
        $c->where($where);
        return $c;
    }
}

return 'msVendorResourcesGetListProcessor';
