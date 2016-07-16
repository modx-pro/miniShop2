<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/undelete.class.php';

class msProductUnDeleteProcessor extends modResourceUnDeleteProcessor
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

return 'msProductUnDeleteProcessor';