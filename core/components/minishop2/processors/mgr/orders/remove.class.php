<?php

class msOrderRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msOrder';
	public $languageTopics = array('minishop2');
	public $beforeRemoveEvent = 'msOnBeforeRemoveOrder';
	public $afterRemoveEvent = 'msOnRemoveOrder';

}
return 'msOrderRemoveProcessor';