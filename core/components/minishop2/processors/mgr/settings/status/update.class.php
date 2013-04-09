<?php

class msOrderStatusUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msOrderStatus';
	public $languageTopics = array('minishop2');
	public $permission = 'edit_document';

	public function beforeSet() {
		if ($this->modx->getObject('msOrderStatus',array('name' => $this->getProperty('name'), 'id:!=' => $this->getProperty('id') ))) {
			$this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
		}
		return parent::beforeSet();
	}

}

return 'msOrderStatusUpdateProcessor';