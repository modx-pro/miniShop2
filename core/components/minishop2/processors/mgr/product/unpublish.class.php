<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/unpublish.class.php';

class msProductUnPublishProcessor extends modResourceUnPublishProcessor {
	public $classKey = 'msProduct';

}

return 'msProductUnPublishProcessor';