<?php

class msOrderStatusRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msOrderStatus';
	public $languageTopics = array('minishop2');

	public function beforeRemove() {
		if (!$this->object->get('editable')) {
			return '';
		}
		return parent::beforeRemove();
	}

}
return 'msOrderStatusRemoveProcessor';