<?php

class msProductFileUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'msProductFile';
	public $ObjectKey = 'msProductFile';
	public $languageTopics = array('core:default','minishop2:product');
	public $permission = 'msproductfile_save';
	/* @var msProductFile $object */
	public $object;
	protected $old_name = null;


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function beforeSet() {
		if (!$this->getProperty('id')) {
			return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));
		}

		$this->old_name = $this->object->get('file');

		foreach (array('file', 'name') as $v) {
			$tmp = trim($this->getProperty($v));
			if (empty($tmp)) {
				$this->addFieldError($v, $this->modx->lexicon('field_required'));
			}
			else {
				if ($v == 'file') {
					$tmp2 = explode('.', $this->old_name);
					$extension = end($tmp2);
					$tmp = preg_replace('/\..*$/', '', $tmp) . '.' . $extension;
				}
				$this->setProperty($v, $tmp);
			}
		}

		return parent::beforeSet();
	}


	/** {@inheritDoc} */
	public function afterSave() {
		if ($this->old_name != $this->object->get('file')) {
			$this->object->rename($this->object->get('file'), $this->old_name);
		}

		$children = $this->object->getMany('Children');
		if (!empty($children)) {
			/* @var msProductFile $child */
			foreach ($children as $child) {
				$child->fromArray(array(
					'name' => $this->object->get('name'),
					'description' => $this->object->get('description'),
				));
				$child->save();
			}
		}

		/** @var msProduct $product */
		if ($product = $this->object->getOne('Product')) {
			$product->updateProductImage();
		}

		return parent::afterSave();
	}

}
return 'msProductFileUpdateProcessor';