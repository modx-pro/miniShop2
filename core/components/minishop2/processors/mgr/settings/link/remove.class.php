<?php

class msLinkRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msLink';
	public $languageTopics = array('minishop2');

	public function beforeRemove() {
		return parent::beforeRemove();
	}

}
return 'msLinkRemoveProcessor';