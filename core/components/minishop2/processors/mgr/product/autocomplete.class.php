<?php

class msProductAutocompleteProcessor extends modObjectProcessor {


	/** {@inheritDoc} */
	public function process() {
		$name = trim($this->getProperty('name'));
		$query = trim($this->getProperty('query'));

		if (!$name) {
			return $this->failure('ms2_product_autocomplete_err_noname');
		}

		$res = array();
		if (!empty($query)) {
			$c = $this->modx->newQuery('msProduct', array('class_key' => 'msProduct'));
			$c->leftJoin('msProductData', 'Data', 'Data.id = msProduct.id');
			$c->sortby($name,'ASC');
			$c->select($name);
			$c->groupby($name);
			$c->where("$name LIKE '%{$query}%'");
			$found = 0;
			if ($c->prepare() && $c->stmt->execute()) {
				$res = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($res as $v) {
					if ($v[$name] == $query) {$found = 1;}
				}
			}
			else {$res = array();}

			if (!$found) {
				$res = array_merge_recursive(array(array($name => $query)), $res);
			}
		}

		return $this->outputArray($res);
	}

}

return 'msProductAutocompleteProcessor';