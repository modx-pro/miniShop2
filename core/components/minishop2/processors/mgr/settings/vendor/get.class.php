<?php

class msVendorGetProcessor extends modObjectGetProcessor {
	public $classKey = 'msVendor';
	public $languageTopics = array('minishop2');
	public $permission = 'mssetting_view';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

}

return 'msVendorGetProcessor';