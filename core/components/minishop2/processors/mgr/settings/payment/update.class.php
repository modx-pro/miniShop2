<?php

class msPaymentUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msPayment';
	public $languageTopics = array('minishop2');
	public $permission = 'update_document';

}

return 'msPaymentUpdateProcessor';