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
		$save = $this->simpleSave($cacheFlag);

		$arrays = array();
		foreach ($this->_fieldMeta as $name => $field) {
			if (strtolower($field['phptype']) == 'json') {
				$tmp = $this->get($name);
				if (!empty($tmp) && is_array($tmp)) {
					$arrays[$name] = $tmp;
				}
			}
		}

		$id = $this->getPrimaryKey();
		$table = $this->xpdo->getTableName('msProductOption');
		$sql = 'DELETE FROM '.$table.' WHERE `product_id` = '.$id;
		$stmt = $this->xpdo->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();

        $options = $this->get('product_options');
        if (count($options) > 0) {
            foreach ($options as $key => $value) {
                if (!is_array($value)) {
                    $value = array($value);
                }
                $arrays[$key] = $value;
            }
        }

		if (!empty($arrays)) {
			$values = array();
			foreach ($arrays as $key => $tmp) {
				foreach ($tmp as $value) {
					if (!empty($value) || (is_array($options) && array_key_exists($key, $options))) {
						$values[] = '('.$id.',"'.$key.'","'.$value.'")';
					}
				}
			}
			if (!empty($values)) {
				$sql = 'INSERT INTO '.$table.' (`product_id`,`key`,`value`) VALUES ' . implode(',', $values);
				$stmt = $this->xpdo->prepare($sql);
				$stmt->execute();
				$stmt->closeCursor();
			}
		}

		return $save;
	}

    /**
     * Save data without custom logic
     * @param null $cacheFlag
     * @return bool
     */
    public function simpleSave($cacheFlag = null) {
        return parent::save($cacheFlag);
    }

    public static function loadOptions(xPDO & $xpdo, $product) {
        $c = $xpdo->newQuery('msProductOption');
        $c->rightJoin('msOption', 'msOption', 'msProductOption.key=msOption.key');
        $c->leftJoin('modCategory', 'Category', 'Category.id=msOption.category');
        $c->where(array('msProductOption.product_id' => $product));

        $c->select($xpdo->getSelectColumns('msOption','msOption'));
        $c->select($xpdo->getSelectColumns('msProductOption','msProductOption', '', array('key'), true));
        $c->select('`Category`.`category` AS `category_name`');
        $data = array();
        $tstart = microtime(true);
        if ($c->prepare() && $c->stmt->execute()) {
            $xpdo->queryTime += microtime(true) - $tstart;
            $xpdo->executedQueries++;
            while ($option = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (isset($data[$option['key']])) { // если опция повторяется, ее значение будет массивом
                    if (!is_array($data[$option['key']])) {
                        $data[$option['key']] = array($data[$option['key']]);
                    }
                    $data[$option['key']][] = $option['value'];
                } else { // одиночная опция останется строкой
                    $data[$option['key']] = $option['value'];
                }

                foreach ($option as $key => $value) {
                    $data[$option['key'].'.'.$key] = $value;
                }
            }
        }

        return $data;
    }

	/**
	 * {@inheritdoc}
	 *
	 */
	public function remove(array $ancestors= array ()) {
		$id = $this->get('id');
		$sql = 'DELETE FROM '.$this->xpdo->getTableName('msProductOption').' WHERE `product_id` = '.$id.';';
		$stmt = $this->xpdo->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();

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
				$sql .= 'UPDATE '.$table.' SET `rank` = '.$k.' WHERE `type` = "image" AND (`id` = '.$id.' OR `parent` = '.$id.');';
			}
			$sql .= 'ALTER TABLE '.$table.' ORDER BY `rank` ASC;';

			$stmt = $this->xpdo->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor();
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
		if ($this->simpleSave()) {
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


	/**
	 * {@inheritdoc}
	 */
	/*
	public function get($k, $format = null, $formatTemplate= null) {
		if (!is_array($k) && $k == 'price') {
			return $this->getPrice();
		}
		if (!is_array($k) && $k == 'old_price') {
			$tmp = parent::get('price');
			$price = $this->getPrice();
			return ($tmp != $price) ? $tmp : parent::get('old_price');
		}
		else if (!is_array($k) && $k == 'weight') {
			return $this->getWeight();
		}
		else {
			return parent::get($k, $format, $formatTemplate);
		}
	}
	*/


	/* Returns product price.
	 *
	 * @param mixed $data Any additional data for price modification
	 * @return integer $price Product price
	 * */
	public function getPrice($data = array()) {
		$price = parent::get('price');

		if (!empty($this->xpdo->getPrice)) {return $price;}
		$this->xpdo->getPrice = true;

		if (empty($data)) {$data = $this->toArray();}
		/** @var miniShop2 $miniShop2 */
		$miniShop2 = $this->xpdo->getService('minishop2');
		$params = array(
			//'product' => $this->getOne('Product'),
			'product' => $this,
			'data' => $data,
			'price' => $price
		);
		$response = $miniShop2->invokeEvent('msOnGetProductPrice', $params);
		if ($response['success']) {
			$price = $params['price'] = $response['data']['price'];
		}

		/* @var modSnippet $snippet */
		// Deprecated. Leaved for backward compatibility.
		if ($setting = $this->xpdo->getOption('ms2_price_snippet', null, false, true)) {
			if ($snippet = $this->xpdo->getObject('modSnippet', array('name' => $setting))) {
				$snippet->setCacheable(false);
				$price = $snippet->process($params);
			}
		}
		//--

		$this->xpdo->getPrice = false;
		return $price;
	}


	/* Returns product weight.
	 *
	 * @param mixed $data Any additional data for weight modification
	 * @return integer $weight Product weight
	 * */
	public function getWeight($data = array()) {
		$weight = parent::get('weight');

		if (!empty($this->xpdo->getWeight)) {return $weight;}
		$this->xpdo->getWeight = true;

		if (empty($data)) {$data = $this->toArray();}
		/** @var miniShop2 $miniShop2 */
		$miniShop2 = $this->xpdo->getService('minishop2');
		$params = array(
			//'product' => $this->getOne('Product'),
			'product' => $this,
			'data' => $data,
			'weight' => $weight
		);
		$response = $miniShop2->invokeEvent('msOnGetProductWeight', $params);
		if ($response['success']) {
			$weight = $params['weight'] = $response['data']['weight'];
		}

		/* @var modSnippet $snippet */
		// Deprecated. Leaved for backward compatibility.
		if ($setting = $this->xpdo->getOption('ms2_weight_snippet', null, false, true)) {
			if ($snippet = $this->xpdo->getObject('modSnippet', array('name' => $setting))) {
				$snippet->setCacheable(false);
				$weight = $snippet->process($params);
			}
		}
		//--

		$this->xpdo->getWeight = false;
		return $weight;
	}



}