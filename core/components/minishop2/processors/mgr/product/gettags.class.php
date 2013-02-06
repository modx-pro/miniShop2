<?php

class msProductGetTagsProcessor extends modObjectProcessor {

	public function process() {
		$query = $this->getProperty('query');

		$c = $this->modx->newQuery('msProductTag');
		$c->sortby('tag','ASC');
		$c->select('tag');
		$c->groupby('tag');
		$c->where("tag LIKE '%{$query}%'");
		if ($c->prepare() && $c->stmt->execute()) {
			$res = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else {$res = array();}

		$res = array_merge_recursive(array(array('tag' => $query)), $res);
		return $this->outputArray($res);
	}

}

return 'msProductGetTagsProcessor';