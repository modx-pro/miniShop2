<?php

class msDiscountGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msDiscount';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'asc';

	// public function prepareQueryBeforeCount(xPDOQuery $c) {
	// 	if ($this->getProperty('combo')) {
	// 		$c->select('id,name');
	// 		$c->where(array('active' => 1));
	// 	}
	// 	return $c;
	// }

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		$array['product'] = $array['product_id'];
		unset($array['product_id']);
		return $array;
	}

}

return 'msDiscountGetListProcessor';