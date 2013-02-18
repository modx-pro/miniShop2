<?php
class msProductData extends xPDOSimpleObject {
	var $source;
	/* @var modMediaSource $mediaSource */
	public $mediaSource;

	/**
	 * {@inheritdoc}
	 */
	public function save($cacheFlag= null) {
		$save = parent::save();

		$id = $this->get('id');
		$tags = $this->get('tags');

		if (!empty($tags)) {
			$table = $this->xpdo->getTableName('msProductTag');
			$string = implode(',', $tags);

			$sql = "DELETE FROM {$table} WHERE `product_id` = '{$id}' AND `tag` NOT IN ('$string');";
			foreach ($tags as $tag) {
				$sql .= "INSERT INTO {$table} (`product_id`,`tag`) VALUES ('{$id}','{$tag}') ON DUPLICATE KEY UPDATE `tag` = '{$string}';";
			}

			$this->xpdo->exec($sql);
		}

		return $save;
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
			$i = 0;
			$table = $this->xpdo->getTableName('msProductFile');
			foreach ($ids as $id) {
				$this->xpdo->exec("UPDATE {$table} SET `rank` = '{$i}' WHERE `id` = '{$id}';");
				$i++;
			}
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
		$price = $this->get('new_price');
		if (!$price) {
			 $price = $this->get('price');
		}
		return $price;
	}


	public function getWeight() {
		return $this->get('weight');
	}



}