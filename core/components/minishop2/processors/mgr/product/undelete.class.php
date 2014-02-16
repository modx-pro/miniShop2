<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/undelete.class.php';

class msProductUnDeleteProcessor extends modResourceUnDeleteProcessor {
	public $classKey = 'msProduct';

}

return 'msProductUnDeleteProcessor';