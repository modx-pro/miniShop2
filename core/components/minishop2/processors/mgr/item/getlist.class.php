<?php
/**
 * Get a list of Items
 *
 * @package minishop2
 * @subpackage processors
 */
class miniShop2ItemGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'miniShop2Item';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $renderers = '';
	
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		return $array;
	}
	
}

return 'miniShop2ItemGetListProcessor';