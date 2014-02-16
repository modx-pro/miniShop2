<?php

class msProductCategoryMemberProcessor extends modObjectCreateProcessor {
	public $classKey = 'msCategoryMember';
	public $permission = 'msproduct_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function process() {
		$pid = $this->getProperty('product_id');
		$cid = $this->getProperty('category_id');
		if ($pid > 0 && $cid > 0) {
			/* @var msCategoryMember $res */
			$res = $this->modx->getObject('msCategoryMember', array('category_id' => $cid, 'product_id' => $pid));
			if (!$res) {
				$res = $this->modx->newObject('msCategoryMember');
				$res->set('product_id', $pid);
				$res->set('category_id', $cid);
				$res->save();
			}
			else {
				$this->modx->exec("DELETE FROM {$this->modx->getTableName('msCategoryMember')} WHERE `product_id` = {$pid} AND `category_id` = {$cid};");
			}
		}

		return $this->success('');
	}

}

return 'msProductCategoryMemberProcessor';