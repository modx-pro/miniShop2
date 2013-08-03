<?php

class msDiscountUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msDiscount';
	public $languageTopics = array('minishop2');
	public $permission = 'edit_document';

	public function beforeSet() {
// die(print_r($this->getProperties(), 1));
		if (
			$count = $this->modx->getCount('msDiscount', array(
				'id:<>' => $this->getProperty('id'),
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

	// public function afterSave() {
	// 	$delivery_id = $this->object->get('id');
	// 	$this->modx->exec("DELETE FROM {$this->modx->getTableName('msDeliveryMember')} WHERE `delivery_id` = {$delivery_id};");

	// 	/* @var msDeliveryMember $entry */
	// 	$payments = $this->getProperty('payments');
	// 	if (!empty($payments) && is_array($payments)) {
	// 		foreach ($payments as $payment => $v) {
	// 			if ($v == 1) {
	// 				$entry = $this->modx->newObject('msDeliveryMember');
	// 				$entry->fromArray(array(
	// 					'delivery_id' => $delivery_id
	// 					,'payment_id' => $payment
	// 				), '', true);
	// 				$entry->save();
	// 			}
	// 		}
	// 	}

	// 	return parent::afterSave();
	// }

}

return 'msDiscountUpdateProcessor';