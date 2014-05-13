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
		$array = $this->object->toArray();
		if ($address = $this->object->getOne('Address')) {
			$array = array_merge($array, $address->toArray('addr_'));
		}
		if ($profile = $this->object->getOne('UserProfile')) {
			$array['fullname'] = $profile->get('fullname');
		}
		else {
			$array['fullname'] = $this->modx->lexicon('no');
		}

		$array['createdon'] = $this->modx->miniShop2->formatDate($array['createdon']);
		$array['updatedon'] = $this->modx->miniShop2->formatDate($array['updatedon']);

		return $this->success('', $array);
	}
}

return 'msOrderGetProcessor';