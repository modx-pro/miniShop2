<?php

class msProductFileUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msProductFile';
	public $languageTopics = array('core:default','minishop2:product');


	public function beforeSet() {
		if (!$this->getProperty('id')) {
			return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));
		}

		if (!$this->getProperty('name')) {
			return $this->addFieldError('name', $this->modx->lexicon('field_required'));
		}

		return true;
	}

}
return 'msProductFileUpdateProcessor';