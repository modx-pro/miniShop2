<?php

class msDeliveryRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msDelivery';
	public $languageTopics = array('minishop2');

	public function beforeRemove() {
		if (!$this->object->get('editable')) {
			return '';
		}
		return parent::beforeRemove();
	}

}
return 'msDeliveryRemoveProcessor';