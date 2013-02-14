<?php

class msProductFileRemoveProcessor extends modObjectRemoveProcessor {
	public $classKey = 'msProductFile';
	public $languageTopics = array('minishop2:product');
	/* @var msProduct $product */
	public $product;


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