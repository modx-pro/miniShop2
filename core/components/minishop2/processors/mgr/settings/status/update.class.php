<?php

class msOrderStatusUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msOrderStatus';
	public $languageTopics = array('minishop2');
	public $permission = 'update_document';

}

return 'msOrderStatusUpdateProcessor';