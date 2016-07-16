<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/publish.class.php';

class msProductPublishProcessor extends modResourcePublishProcessor
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

return 'msProductPublishProcessor';