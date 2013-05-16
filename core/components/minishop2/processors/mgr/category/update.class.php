<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class msCategoryUpdateProcessor extends modResourceUpdateProcessor {
	public $classKey = 'msCategory';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'mscategory_save';
	public $objectType = 'resource';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';

	/**
	 * {@inheritDoc}
	 * @return string|mixed
	 */
	public function checkFriendlyAlias() {
		if ($this->workingContext->getOption('ms2_category_id_as_alias')) {
			$alias = $this->object->id;
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::checkFriendlyAlias();
		}
		return $alias;
	}

	public function beforeSet() {
		$this->setProperties(array(
			'isfolder' => 1
		));
		return parent::beforeSet();
	}
}