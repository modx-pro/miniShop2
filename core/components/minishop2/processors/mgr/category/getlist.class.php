<?php

class msCategoryGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msCategory';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'ASC';


	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array(
			'class_key' => 'msCategory'
		));

		if ($query = $this->getProperty('query')) {
			$c->where(array('pagetitle:LIKE' => "%$query%"));
		}
		return $c;
	}


	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object) {
		return $object->toArray();
	}

}

return 'msCategoryGetListProcessor';