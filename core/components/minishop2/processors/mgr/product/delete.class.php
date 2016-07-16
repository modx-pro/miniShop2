<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/delete.class.php';

class msProductDeleteProcessor extends modResourceDeleteProcessor
{
    public $permission = 'msproduct_delete';


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return empty($this->permission) || $this->modx->hasPermission($this->permission);
    }

}

return 'msProductDeleteProcessor';