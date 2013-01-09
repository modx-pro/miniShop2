<?php
/**
 * Get an Item
 * 
 * @package minishop2
 * @subpackage processors
 */
class miniShop2ItemGetProcessor extends modObjectGetProcessor {
	public $classKey = 'miniShop2Item';
	public $languageTopics = array('minishop2:default');
	public $objectType = 'minishop2';
}

return 'miniShop2ItemGetProcessor';