<?php

class msOrderStatusGetProcessor extends modObjectGetProcessor {
	public $classKey = 'msOrderStatus';
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

return 'msOrderStatusGetProcessor';