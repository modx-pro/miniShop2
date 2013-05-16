<?php

class msOrderUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msOrder';
	public $languageTopics = array('minishop2:default');
	public $permission = 'msorder_save';
	public $beforeSaveEvent = 'msOnBeforeUpdateOrder';
	public $afterSaveEvent = 'msOnUpdateOrder';
	protected $status;
	protected $delivery;
	protected $payment;

	public function beforeSet() {
		foreach (array('status','delivery','payment') as $v) {
			$this->$v = $this->object->get($v);
			if (!$this->getProperty($v) ) {
				$this->addFieldError($v, $this->modx->lexicon('ms2_err_ns'));
			}
		}

		if ($status = $this->object->getOne('Status')) {
			if ($status->get('final')) {
				return $this->modx->lexicon('ms2_err_status_final');
			}
		}

		return parent::beforeSet();
	}

	public function beforeSave() {
		if ($this->object->get('status') != $this->status) {
			$change_status = $this->modx->miniShop2->changeOrderStatus($this->object->get('id'), $this->object->get('status'));
			if ($change_status !== true) {
				return $change_status;
			}
		}
		$this->object->set('updatedon', time());
		return parent::beforeSave();
	}

	public function afterSave() {
		if ($address = $this->object->getOne('Address')) {
			foreach ($this->getProperties() as $k => $v) {
				if (strpos($k, 'addr_') !== false) {
					$address->set(substr($k, 5), $v);
				}
			}
			$address->save();
		}
	}

}

return 'msOrderUpdateProcessor';