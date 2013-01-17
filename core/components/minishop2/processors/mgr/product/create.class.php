<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';

class msProductCreateProcessor extends modResourceCreateProcessor {
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
	public function prepareAlias() {
		parent::prepareAlias();

		foreach ($this->modx->error->errors as $k => $v) {
			if ($v['id'] == 'alias') {
				unset($this->modx->error->errors[$k]);
				$this->setProperty('alias', 'empty-resource-alias');
			}
		}

	}

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function beforeSave() {
		$this->object->set('show_in_tree', false);
		$this->object->set('hidemenu', true);

		return parent::beforeSave();
	}

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function afterSave() {
		if ($this->object->alias == 'empty-resource-alias') {
			$this->object->set('alias', $this->object->id);
			$this->object->save();
		}

		return parent::afterSave();
	}
}