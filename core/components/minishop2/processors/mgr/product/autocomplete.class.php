<?php

class msProductAutocompleteProcessor extends modObjectProcessor {

	public function process() {
		$name = $this->getProperty('name');
		$query = $this->getProperty('query');

		if (!$name) {
			return $this->failure('ms2_product_autocomplete_err_noname');
		}

		if (!$query) {
			return $this->failure('ms2_product_autocomplete_err_noquery');
		}

		$c = $this->modx->newQuery('msProduct', array('class_key' => 'msProduct'));
		$c->leftJoin('msProductData', 'Data', 'Data.id = msProduct.id');
		$c->sortby($name,'ASC');
		$c->select($name);
		$c->groupby($name);
		$c->where("$name LIKE '%{$query}%'");
		if ($c->prepare() && $c->stmt->execute()) {
			$res = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else {$res = array();}

		return $this->outputArray($res);
	}

}

return 'msProductAutocompleteProcessor';