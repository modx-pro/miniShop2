<?php

class modChunkGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'modChunk';
	public $languageTopics = array('chunk');


	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$query = $this->getProperty('query');
		if (!empty($query)) {
			$c->where(array('name:LIKE' => '%'.$query.'%'));
		}
		return $c;
	}

}
return 'modChunkGetListProcessor';