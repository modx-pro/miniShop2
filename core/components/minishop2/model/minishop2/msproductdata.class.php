<?php
class msProductData extends xPDOSimpleObject {
	var $source;
	/* @var modMediaSource $mediaSource */
	public $mediaSource;

	/**
	 * {@inheritdoc}
	 *
	 * All json fields of product are synchronized with msProduct Options
	 *
	 */
	public function save($cacheFlag= null) {
		$save = parent::save();
		$id = $this->get('id');
		$table = $this->xpdo->getTableName('msProductOption');

		$arrays = array();
		foreach ($this->_fieldMeta as $name => $field) {
			if (strtolower($field['phptype']) == 'json') {
				$tmp = $this->get($name);
				if (!empty($tmp) && is_array($tmp)) {
					$arrays[$name] = $tmp;
				}
			}
		}

		$sql1 = "DELETE FROM {$table} WHERE `product_id` = '{$id}';";
		$sql2 = "INSERT INTO {$table} (`product_id`,`key`,`value`) VALUES ";
		$values = array();
		foreach ($arrays as $key => $v) {
			foreach ($v as $value) {
				if (!empty($value)) {
					$values[] = "('{$id}','{$key}','{$value}')";
				}
			}
		}
		if (!empty($values)) {
			$sql2 .= implode(', ', $values);
			$this->xpdo->exec($sql1.$sql2);
		}
		else {
			$this->xpdo->exec($sql1);
		}

		return $save;
	}


	/**
	 * {@inheritdoc}
	 *
	 */
	public function remove(array $ancestors= array ()) {
		$id = $this->get('id');
		$table = $this->xpdo->getTableName('msCategoryMember');
		$sql = "DELETE FROM {$table} WHERE `product_id` = '$id';";
		$table = $this->xpdo->getTableName('msProductOption');
		$sql .= "DELETE FROM {$table} WHERE `product_id` = '$id';";
		$this->xpdo->exec($sql);

		return parent::remove();
	}

	public function generateAllThumbnails() {
		$files = $this->xpdo->getCollection('msProductFile', array(
			'type' => 'image'
			,'parent' => 0
		));

		/* @var msProductFile $file */
		foreach ($files as $file) {
			$res = $file->generateThumbnails();
			if ($res !== true) {
				$this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Could not generate thumbnail for product file with id = '.$file->get('id').'. '.$res);
			}
		}
	}


	public function initializeMediaSource($ctx = '') {
		if  ($this->mediaSource = $this->xpdo->getObject('sources.modMediaSource', $this->get('source'))) {
			if (empty($ctx)) {
				$product = $this->getOne('Product');
				$ctx = $product->get('context_key');
			}

			$this->mediaSource->set('ctx', $ctx);
			$this->mediaSource->initialize();

			return $this->mediaSource;
		}
		else {
			return false;
		}
	}


	public function rankProductImages() {
		$q = $this->xpdo->newQuery('msProductFile', array('product_id' => $this->get('id'), 'parent' => 0, 'type' => 'image'));
		$q->select('id');
		$q->sortby('rank ASC, createdon', 'ASC');

		if ($q->prepare() && $q->stmt->execute()) {
			$ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
			$sql = '';
			$table = $this->xpdo->getTableName('msProductFile');
			foreach ($ids as $k => $id) {
				$sql .= "UPDATE {$table} SET `rank` = '{$k}' WHERE `type` = 'image' AND (`id` = '{$id}' OR `parent` = '{$id}');";
			}
			$sql .= "ALTER TABLE {$table} ORDER BY `rank` ASC;";
			$this->xpdo->exec($sql);
		}
	}


	public function updateProductImage() {
		$this->rankProductImages();
		/* @var msProductFile $file*/
		if ($file = $this->xpdo->getObject('msProductFile', array('product_id' => $this->get('id'), 'parent' => 0, 'rank' => 0, 'type' => 'image'))) {
			$thumb = $file->getFirstThumbnail();
			$arr = array(
				'image' => $file->get('url')
				,'thumb' => !empty($thumb['url']) ? $thumb['url'] : ''
			);
		}
		else {
			$arr = array(
				'image' => null
				,'thumb' => null
			);
		}

		$this->fromArray($arr);
		if ($this->save()) {
			/* @var msProduct $product */
			if ($product = $this->getOne('Product')) {
				$product->clearCache();
			}
			return $arr['thumb'];
		}
		else {
			return false;
		}
	}


	/* Returns product price.
	 *
	 * @param mixed $data Any additional data for price modification
	 * @return integer $price Product price
	 * */
	public function getPrice($data = array()) {
		$price = 0;
		if ($setting = $this->xpdo->getOption('ms2_price_snippet', null, false, true)) {
			/* @var modSnippet $snippet */
			if ($snippet = $this->xpdo->getObject('modSnippet', array('name' => $setting))) {
				$snippet->setCacheable(false);
				$price = $snippet->process(array('product' => $this->getOne('Product'), 'data' => $data));
			}
		}
		else {
			$price = $this->get('price');
		}
		return $price;
	}


	/* Returns product weight.
	 *
	 * @param mixed $data Any additional data for weight modification
	 * @return integer $weight Product weight
	 * */
	public function getWeight($data = array()) {
		$weight = 0;
		if ($setting = $this->xpdo->getOption('ms2_weight_snippet', null, false, true)) {
			/* @var modSnippet $snippet */
			if ($snippet = $this->xpdo->getObject('modSnippet', array('name' => $setting))) {
				$snippet->setCacheable(false);
				$weight = $snippet->process(array('product' => $this->getOne('Product'), 'data' => $data));
			}
		}
		else {
			$weight = $this->get('weight');
		}
		return $weight;
	}



}