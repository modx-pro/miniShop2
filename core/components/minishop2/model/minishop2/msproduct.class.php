<?php

require_once MODX_CORE_PATH.'components/minishop2/processors/mgr/product/create.class.php';
require_once MODX_CORE_PATH.'components/minishop2/processors/mgr/product/update.class.php';


class msProduct extends modResource {
	public $showInContextMenu = false;
	public $allowChildrenResources = false;
	/* @var msProductData $data */
	private $data;
	private $dataFields = array();
	private $dataRelated = array();


	/**
	 * {@inheritdoc}
	 */
	function __construct(xPDO & $xpdo) {
		parent::__construct($xpdo);

		$fields = str_replace(array('`',' '), '', $this->xpdo->getSelectColumns('msProductData','', '', array('id'), true));
		$this->dataFields = explode(',', $fields);

		$aggregates = $this->xpdo->getAggregates('msProductData');
		/*
		foreach ($aggregates as $k => $v) {
			if (!in_array($v['class'], $this->dataRelated)) {
				$this->dataRelated[] = $v['class'];
			}
			if (!in_array($k, $this->dataRelated)) {
				$this->dataRelated[] = $k;
			}
		}
		*/
		$composites = $this->xpdo->getComposites('msProductData');
		/*
		foreach ($composites as $k => $v) {
			if (!in_array($v['class'], $this->dataRelated)) {
				$this->dataRelated[] = $v['class'];
			}
			if (!in_array($k, $this->dataRelated)) {
				$this->dataRelated[] = $k;
			}
		}
		*/
		$this->dataRelated = array_merge(array_keys($aggregates), array_keys($composites));
	}


	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'controllers/product/';
	}


	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public function getContextMenuText() {
		$this->xpdo->lexicon->load('minishop2:default');
		return array(
			'text_create' => $this->xpdo->lexicon('ms2_product'),
			'text_create_here' => $this->xpdo->lexicon('ms2_product_create_here'),
		);
	}


	/**
	 * {@inheritDoc}
	 * @return string
	 */
	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('minishop2:default');
		return $this->xpdo->lexicon('ms2_product_type');
	}


	/**
	 * {@inheritdoc}
	 */
	public function set($k, $v= null, $vType= '') {
		if (in_array($k, $this->dataFields)) {
			if (!is_object($this->data)) {$this->loadData();}
			if ($k == 'tags' && is_array($v)) {$v = implode(',',$v);}
			return $this->data->set($k, $v, $vType);
		}
		else {
			return parent::set($k, $v, $vType);
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function save($cacheFlag= null) {
		$res = parent::save($cacheFlag);
		if (!is_object($this->data)) {$this->loadData();}

		$this->data->set('id', parent::get('id'));
		$this->data->save($cacheFlag);

		return $res;
	}


	/**
	 * {@inheritdoc}
	 */
	public function get($k, $format = null, $formatTemplate= null) {
		$data = array();
		if (is_array($k)) {
			$k = array_merge($k, $this->dataFields);
			if (!is_object($this->data)) {$this->loadData();}
			$arr = array_intersect($k, $this->dataFields);
			foreach ($arr as $v) {
				$key = array_search($v, $k);
				unset($k[$key]);
				$data[$v] = $this->data->get($v);
			}
			$res = parent::get($k, $format, $formatTemplate);
			return array_merge($res, $data);
		}
		else if (in_array($k, $this->dataFields)) {
			if (!is_object($this->data)) {$this->loadData();}
			return $this->data->get($k);
		}
		else {
			return parent::get($k, $format, $formatTemplate);
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
		$array = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);

		if (!is_object($this->data)) {$this->loadData();}
		return array_merge($array, $this->data->toArray());
	}


	/*
	 * Load product data
	 *
	 * @return boolean
	 * */
	public function loadData() {
		if (!is_object($this->data) || !($this->data instanceof msProductData)) {
			$this->set('data', null);
			if (!$data = $this->xpdo->getObject('msProductData', parent::get('id'))) {
				$data = $this->xpdo->newObject('msProductData');
			}
			$this->data = $data;
		}
	}


	/**
	 * {@inheritdoc}
	 * */
	public function & getOne($alias, $criteria= null, $cacheFlag= true) {
		if (in_array($alias, $this->dataRelated)) {
			if (!is_object($this->data)) {$this->loadData();}
			return $this->data->getOne($alias, $criteria, $cacheFlag);
		}
		else {
			return parent::getOne($alias, $criteria, $cacheFlag);
		}
	}


	/**
	 * {@inheritdoc}
	 * */
	public function addOne(& $obj, $alias= '') {
		if (empty ($alias)) {
			if ($obj->_alias == $obj->_class) {
				$aliases = $this->_getAliases($obj->_class, 1);
				if (!empty($aliases)) {
					$obj->_alias = reset($aliases);
				}
			}
			$alias= $obj->_alias;
		}
		if (in_array($alias, $this->dataRelated)) {
			if (!is_object($this->data)) {$this->loadData();}
			return $this->data->addOne($obj, $alias);
		}
		else {
			return parent::addOne($obj, $alias);
		}
	}


	/**
	 * {@inheritdoc}
	 * */
	public function & getMany($alias, $criteria= null, $cacheFlag= false) {
		if (in_array($alias, $this->dataRelated)) {
			if (!is_object($this->data)) {$this->loadData();}
			return $this->data->getMany($alias, $criteria, $cacheFlag);
		}
		else {
			return parent::getMany($alias, $criteria, $cacheFlag);
		}
	}


	/**
	 * {@inheritdoc}
	 * */
	public function addMany(& $obj, $alias= '') {
		if (empty ($alias)) {
			if ($obj->_alias == $obj->_class) {
				$aliases = $this->_getAliases($obj->_class, 1);
				if (!empty($aliases)) {
					$obj->_alias = reset($aliases);
				}
			}
			$alias= $obj->_alias;
		}
		if (in_array($alias, $this->dataRelated)) {
			if (!is_object($this->data)) {$this->loadData();}
			return $this->data->addMany($obj, $alias);
		}
		else {
			return parent::addMany($obj, $alias);
		}
	}


	/*
	 * Returns all fields names including fields from msProductData
	 *
	 * @return array
	 * */
	public function getFieldsNames() {
		return array_keys($this->toArray());
	}


	/**
	 * Clearing cache of this resource
	 * @param string $context Key of context for clearing
	 * @return void
	 */
	public function clearCache($context = null) {
		if (empty($context)) {
			$context = $this->context_key;
		}
		$this->_contextKey = $context;

		/** @var xPDOFileCache $cache */
		$cache = $this->xpdo->cacheManager->getCacheProvider($this->xpdo->getOption('cache_resource_key', null, 'resource'));
		$key = $this->getCacheKey();
		$cache->delete($key, array('deleteTop' => true));
		$cache->delete($key);
	}


	/*
	 * Returns array with all neighborhood products
	 *
	 * @return array $arr Array with neighborhood from left and right
	 * */
	public function getNeighborhood() {
		$arr = array();

		$q = $this->xpdo->newQuery('msProduct', array('parent' => $this->parent, 'class_key' => 'msProduct'));
		$q->sortby('id','ASC');
		$q->select('id');
		if ($q->prepare() && $q->stmt->execute()) {
			$ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
			$current = array_search($this->id, $ids);

			$right = $left = array();
			foreach ($ids as $k => $v) {
				if ($k > $current) {
					$right[] = $v;
				}
				else if ($k < $current) {
					$left[] = $v;
				}
			}

			$arr = array(
				'left' => array_reverse($left)
				,'right' => $right
			);
		}
		return $arr;
	}

}