<?php

class msLinkCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msLink';
	public $languageTopics = array('minishop2');
	public $permission = 'mssetting_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	public function beforeSet() {
		if ($this->modx->getObject('msLink',array('name' => $this->getProperty('name')))) {
			$this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
		}
		return !$this->hasErrors();
	}

}

return 'msLinkCreateProcessor';