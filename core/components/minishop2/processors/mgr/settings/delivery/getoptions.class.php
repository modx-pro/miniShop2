<?php

class msDeliveryGetOptionsProcessor extends modObjectProcessor {

	public function process() {
		$query = $this->getProperty('query');
		$key = $this->getProperty('key');

		$c = $this->modx->newQuery('msProductOption');
		$c->sortby('value','ASC');
		$c->select('value');
		$c->groupby('value');
		$c->where("`key` = '{$key}' AND `value` LIKE '%{$query}%'");
		$found = 0;
		if ($c->prepare() && $c->stmt->execute()) {
			$res = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $v) {
				if ($v['value'] == $query) {$found = 1;}
			}
		}
		else {$res = array();}

		if (!$found) {
			$res = array_merge_recursive(array(array('value' => $query)), $res);
		}

		return $this->outputArray($res);
	}

}

return 'msDeliveryGetOptionsProcessor';