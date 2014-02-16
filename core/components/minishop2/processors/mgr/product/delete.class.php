<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/delete.class.php';

class msProductDeleteProcessor extends modResourceDeleteProcessor {
	public $classKey = 'msProduct';

}

return 'msProductDeleteProcessor';