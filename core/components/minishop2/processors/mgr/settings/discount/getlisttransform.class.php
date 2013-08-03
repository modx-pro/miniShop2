<?php

class msDiscountGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msDiscount';
	public $languageTopics = array('minishop2:manager');
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'asc';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($this->getProperty('combo')) {
			$c->select('id,discount,discount_type');
		}
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		$array['discount'] .= ($array['discount_type'] == 'summ')
			? ' '. $this->modx->lexicon('ms2_frontend_currency')
			: $this->modx->lexicon('ms2_discount_type_'. $array['discount_type']);
		
		unset($array['discount_type']);
		return $array;
	}

}

return 'msDiscountGetListProcessor';