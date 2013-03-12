<?php

class modResourceGetListProcessor extends modObjectGetListProcessor {
	/** @var string $defaultSortField The default field to sort by */
	public $defaultSortField = 'pagetitle';
	public $classKey = 'modResource';
	public $languageTopics = array('resource');

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