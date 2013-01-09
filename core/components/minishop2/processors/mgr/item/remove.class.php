<?php
/**
 * Remove an Item.
 * 
 * @package minishop2
 * @subpackage processors
 */
class miniShop2ItemRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'miniShop2Item';
	public $languageTopics = array('minishop2');

}
return 'miniShop2ItemRemoveProcessor';