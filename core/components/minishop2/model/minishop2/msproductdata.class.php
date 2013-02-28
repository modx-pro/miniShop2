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

		$sql = "DELETE FROM {$table} WHERE `product_id` = '{$id}';";
		foreach ($arrays as $key => $v) {
			foreach ($v as $value) {
				if (!empty($value)) {
					$sql .= "INSERT INTO {$table} (`product_id`,`key`,`value`) VALUES ('{$id}','{$key}','{$value}');";
				}
			}
		}
		$this->xpdo->exec($sql);

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
				$sql .= "UPDATE {$table} SET `rank` = '{$k}' WHERE `id` = '{$id}';";
			}
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
			return $arr['thumb'];
		}
		else {
			return false;
		}
	}


	public function getPrice() {
		return $this->get('price');
	}


	public function getWeight() {
		return $this->get('weight');
	}



}