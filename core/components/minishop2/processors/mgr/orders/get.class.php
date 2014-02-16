<?php

class msOrderGetProcessor extends modObjectGetProcessor {
	public $classKey = 'msOrder';
	public $languageTopics = array('minishop2:default');
	public $permission = 'msorder_view';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function cleanup() {
		$order = $this->object->toArray();
		$address = $this->object->getOne('Address')->toArray('addr_');
		$profile = $this->object->getOne('UserProfile');

		$array = array_merge($order, $address, array('fullname' => $profile->get('fullname')));

		$array['createdon'] = $this->modx->miniShop2->formatDate($array['createdon']);
		$array['updatedon'] = $this->modx->miniShop2->formatDate($array['updatedon']);

		return $this->success('', $array);
	}
}

return 'msOrderGetProcessor';