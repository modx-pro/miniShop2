<?php

class msDeliveryCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msDelivery';
	public $languageTopics = array('minishop2');
	public $permission = 'mssetting_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function beforeSet() {
		if ($this->modx->getObject('msDelivery',array('name' => $this->getProperty('name')))) {
			$this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
		}
		if ($price = $this->getProperty('price')) {
			$price = preg_replace(array('/[^0-9%\-,\.]/','/,/'), array('', '.'), $price);
			if (strpos($price, '%') !== false) {
				$price = str_replace('%', '', $price) . '%';
			}
			if (strpos($price, '-') !== false) {
				$price = '-' . str_replace('-', '', $price);
			}
			if (empty($price)) {$price = '0';}
			$this->setProperty('price', $price);
		}
		return !$this->hasErrors();
	}


	/** {@inheritDoc} */
	public function beforeSave() {
		$this->object->fromArray(array(
			'rank' => $this->modx->getCount('msDelivery')
		));
		return parent::beforeSave();
	}


	/** {@inheritDoc} */
	public function afterSave() {
		$delivery_id = $this->object->get('id');
		/* @var msDeliveryMember $entry */
		$payments = $this->getProperty('payments');
		if (!empty($payments) && is_array($payments)) {
			foreach ($payments as $payment => $v) {
				if ($v == 1) {
					$entry = $this->modx->newObject('msDeliveryMember');
					$entry->fromArray(array(
						'delivery_id' => $delivery_id,
						'payment_id' => $payment,
					), '', true);
					$entry->save();
				}
			}
		}

		return parent::afterSave();
	}

}

return 'msDeliveryCreateProcessor';