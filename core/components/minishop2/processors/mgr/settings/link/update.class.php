<?php

class msLinkUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msLink';
	public $languageTopics = array('minishop2');
	public $permission = 'edit_document';

	public function beforeSet() {
		if ($this->modx->getObject('msLink',array('name' => $this->getProperty('name'), 'id:!=' => $this->getProperty('id') ))) {
			$this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
		}
		return parent::beforeSet();
	}

}

return 'msLinkUpdateProcessor';