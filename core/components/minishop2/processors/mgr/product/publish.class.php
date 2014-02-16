<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/publish.class.php';

class msProductPublishProcessor extends modResourcePublishProcessor {
	public $classKey = 'msProduct';

}

return 'msProductPublishProcessor';