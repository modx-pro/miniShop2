<?php

class msOrderDeleteMultipleProcessor extends modObjectProcessor {
	public $permission = 'msorder_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function process() {
		$ids = $this->getProperty('ids',null);
		if (empty($ids)) {
			return $this->success();
		}
		$ids = is_array($ids) ? $ids : explode(',',$ids);

		foreach ($ids as $id) {
			if (!empty($id)) {
				$this->modx->runProcessor('remove'
					,array('id' => $id)
					,array('processors_path' => dirname(__FILE__).'/'));
			}
		}
		return $this->success();
	}

}
return 'msOrderDeleteMultipleProcessor';