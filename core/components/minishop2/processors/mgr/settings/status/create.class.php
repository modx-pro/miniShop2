<?php

class msOrderStatusCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msOrderStatus';
	public $languageTopics = array('minishop2');
	public $permission = 'new_document';

	public function beforeSet() {
		if ($this->modx->getObject('msOrderStatus',array('name' => $this->getProperty('name')))) {
			$this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
		}
		return !$this->hasErrors();
	}

	public function beforeSave() {
		$this->object->fromArray(array(
			'rank' => $this->modx->getCount('msOrderStatus')
			,'editable' => true
		));
		return parent::beforeSave();
	}

}

return 'msOrderStatusCreateProcessor';