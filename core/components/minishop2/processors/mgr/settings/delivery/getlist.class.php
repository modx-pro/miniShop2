<?php

class msDeliveryGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msDelivery';
	public $defaultSortField = 'rank';
	public $defaultSortDirection  = 'asc';
	public $permission = 'mssetting_list';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($this->getProperty('combo')) {
			$c->select('id,name');
			$c->where(array('active' => 1));
		}
		return $c;
	}


	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();

		$payments = $object->getMany('Payments');
		$enabled = array();
		/** @var msPayment $payment */
		foreach ($payments as $payment) {
			$enabled[$payment->payment_id] = 1;
		}

		$array['payments'] = $enabled;

		return $array;
	}

}

return 'msDeliveryGetListProcessor';