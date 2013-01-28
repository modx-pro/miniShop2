<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class msProductUpdateProcessor extends modResourceUpdateProcessor {
	public $classKey = 'msProduct';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'msproduct_save';
	public $objectType = 'resource';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function checkFriendlyAlias() {
		parent::checkFriendlyAlias();
		foreach ($this->modx->error->errors as $k => $v) {
			if ($v['id'] == 'alias') {
				unset($this->modx->error->errors[$k]);
				$this->setProperty('alias', $this->object->id);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function beforeSave() {
		//$this->object->set('show_in_tree', false);
		//$this->object->set('hidemenu', true);

		return parent::beforeSave();
	}
}