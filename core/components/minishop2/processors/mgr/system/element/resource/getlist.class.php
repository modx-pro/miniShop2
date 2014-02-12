<?php

class modResourceGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'modResource';
	public $languageTopics = array('resource');
	public $defaultSortField = 'pagetitle';


	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($this->getProperty('combo')) {
			$c->select('id,pagetitle');
		}
		$query = $this->getProperty('query');
		if (!empty($query)) {
			$c->where(array('pagetitle:LIKE' => '%'.$query.'%'));
		}
		return $c;
	}


	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object) {
		if ($this->getProperty('combo')) {
			$array = array(
				'id' => $object->get('id')
				,'pagetitle' => '('.$object->id.') ' . $object->get('pagetitle')
			);
		}
		else {
			$array = $object->toArray();
		}
		return $array;
	}

}
return 'modResourceGetListProcessor';