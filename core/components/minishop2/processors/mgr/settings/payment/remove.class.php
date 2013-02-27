<?php

class msPaymentRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msPayment';
	public $languageTopics = array('minishop2');

	public function beforeRemove() {
		if (!$this->object->get('editable')) {
			return '';
		}
		return parent::beforeRemove();
	}

}
return 'msPaymentRemoveProcessor';