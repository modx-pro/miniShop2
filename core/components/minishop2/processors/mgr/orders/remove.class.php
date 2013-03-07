<?php

class msOrderRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msOrder';
	public $languageTopics = array('minishop2');

}
return 'msOrderRemoveProcessor';