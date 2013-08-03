<?php

class msDiscountCardCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msDiscountCardCard';
	public $languageTopics = array('minishop2');
	public $permission = 'new_document';
	// public $permission = '';

	public function beforeSet() {
		if ($this->modx->getObject('msDiscountCardCard',array('uid' => $this->getProperty('uid')))) {
			$this->modx->error->addField('uid', $this->modx->lexicon('ms2_err_ae'));
		}
		return !$this->hasErrors();
	}

	// public function beforeSave() {
	// 	$this->object->fromArray(array(
	// 		'rank' => $this->modx->getCount('msDiscountCardCard')
	// 	));
	// 	return parent::beforeSave();
	// }

}

return 'msDiscountCardCreateProcessor';