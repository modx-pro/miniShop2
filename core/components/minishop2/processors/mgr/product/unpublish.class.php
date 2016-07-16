<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/unpublish.class.php';

class msProductUnPublishProcessor extends modResourceUnPublishProcessor
{
    public $permission = 'msproduct_publish';


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return empty($this->permission) || $this->modx->hasPermission($this->permission);
    }
}

return 'msProductUnPublishProcessor';