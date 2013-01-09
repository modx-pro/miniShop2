<?php
/**
 * Update an Item
 * 
 * @package minishop2
 * @subpackage processors
 */
class miniShop2ItemUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'miniShop2Item';
	public $languageTopics = array('minishop2');
	public $permission = 'update_document';
}

return 'miniShop2ItemUpdateProcessor';