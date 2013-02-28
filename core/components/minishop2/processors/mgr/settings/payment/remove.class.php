<?php

class msPaymentRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msPayment';
	public $languageTopics = array('minishop2');

}
return 'msPaymentRemoveProcessor';