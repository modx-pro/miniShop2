<?php

class msVendorRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msVendor';
	public $languageTopics = array('minishop2');

	public function beforeRemove() {
		/* TODO to think about checking of goods of this vendor  */
		return parent::beforeRemove();
	}

}
return 'msVendorRemoveProcessor';