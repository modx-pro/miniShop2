<?php
class msProductFileRemoveMultipleProcessor extends modObjectProcessor {
	public $classKey = 'msProductFile';
	public $languageTopics = array('minishop2:default');
	public $permission = 'msproductfile_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function process() {
		$ids = $this->getProperty('ids');
		if (empty($ids)) return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));
		$product_id = $this->getProperty('product_id');

		$separator = $this->getProperty('separator',',');
		$ids = explode($separator,$ids);

		foreach ($ids as $id) {
			/* @var msProductFile $file */
			if ($file = $this->modx->getObject('msProductFile', $id)) {
				$file->remove();
			}
		}

		$thumb = '';
		/* @var msProduct $product */
		if ($product = $this->modx->getObject('msProduct', $product_id)) {
			$thumb = $product->updateProductImage();
		}
		return $this->success($thumb);
	}

}
return 'msProductFileRemoveMultipleProcessor';