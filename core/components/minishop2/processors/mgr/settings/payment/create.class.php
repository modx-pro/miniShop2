<?php

class msPaymentCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msPayment';
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
		if ($this->modx->getObject('msPayment',array('name' => $this->getProperty('name')))) {
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
			'rank' => $this->modx->getCount('msPayment')
		));
		return parent::beforeSave();
	}

}

return 'msPaymentCreateProcessor';