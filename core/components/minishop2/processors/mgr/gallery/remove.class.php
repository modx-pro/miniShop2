<?php

class msProductFileRemoveProcessor extends modObjectRemoveProcessor {
	public $classKey = 'msProductFile';
	public $ObjectKey = 'msProductFile';
	public $languageTopics = array('minishop2:product');
	public $permission = 'msproductfile_save';
	/* @var msProduct $product */
	public $product;


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	public function beforeRemove() {
		$this->product = $this->object->getOne('Product');
		return parent::beforeRemove();
	}


	public function cleanup() {
		$thumb = $this->product->updateProductImage();
		return $this->success($thumb);
	}
}

return 'msProductFileRemoveProcessor';