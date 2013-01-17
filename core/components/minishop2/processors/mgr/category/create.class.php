<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';

class msCategoryCreateProcessor extends modResourceCreateProcessor {
	public $classKey = 'msCategory';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'mscategory_save';
	public $objectType = 'resource';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';
}