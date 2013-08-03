<?php

class msDiscountCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msDiscount';
	public $languageTopics = array('minishop2');
	public $permission = 'new_document';
	// public $permission = '';

	public function beforeSet() {
// die(print_r($this->getProperties(), 1));
		if (
			$count = $this->modx->getCount('msDiscount', array(
				// 'id:<>' => $this->getProperty('id'),
				'product_id' => $this->getProperty('product_id')
			))
		) {
			$this->modx->error->addField('product_id', $this->modx->lexicon('ms2_err_ae'));
		}
		if ($this->hasErrors()) {
			return false;
		} else {
			$props = $this->getProperties();
			
			$this->setProperty('product_id', $props['product']);
			$this->unsetProperty('product');
			return true;
		}
	}

	// public function beforeSave() {
	// 	$this->object->fromArray(array(
	// 		'rank' => $this->modx->getCount('msDiscountCard')
	// 	));
	// 	return parent::beforeSave();
	// }

}

return 'msDiscountCreateProcessor';