<?php

class msDeliveryGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msDelivery';
	public $defaultSortField = 'rank';
	public $defaultSortDirection  = 'asc';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();

		$payments = $object->getMany('Payments');
		$enabled = array();
		foreach ($payments as $payment) {
			$enabled[$payment->get('payment_id')] = 1;
		}

		$array['payments'] = $enabled;

		return $array;
	}

}

return 'msDeliveryGetListProcessor';