<?php

require_once MODX_CORE_PATH.'components/minishop2/processors/mgr/product/create.class.php';
require_once MODX_CORE_PATH.'components/minishop2/processors/mgr/product/update.class.php';


class msProduct extends modResource {
	public $showInContextMenu = false;
	public $allowChildrenResources = false;
	/* @var msProductData $data */
	protected $data = null;
	protected $dataFields = array();
	protected $dataRelated = array();
	/* @var msVendor $vendor */
	protected $vendor = null;
    protected $optionKeys = array();
    protected $options = null;


	/**
	 * {@inheritDoc}
	 * @return object|null
	 */
	public static function load(xPDO & $xpdo, $className, $criteria= null, $cacheFlag= true){
		if (!is_object($criteria)) {
			$criteria= $xpdo->getCriteria($className, $criteria, $cacheFlag);
		}
		$xpdo->addDerivativeCriteria($className, $criteria);
        /** @var msProduct $instance */
		$instance =  parent::load($xpdo, $className, $criteria, $cacheFlag);
        if ($instance) {
            $instance->optionKeys = $instance->getOptionKeys();
        }
        return $instance;
	}


	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public static function loadCollection(xPDO & $xpdo, $className, $criteria= null, $cacheFlag= true){
		if (!is_object($criteria)) {
			$criteria= $xpdo->getCriteria($className, $criteria, $cacheFlag);
		}
		$xpdo->addDerivativeCriteria($className, $criteria);
		return parent::loadCollection($xpdo, $className, $criteria, $cacheFlag);
	}


	/**
	 * {@inheritdoc}
	 */
	function __construct(xPDO & $xpdo) {
		parent::__construct($xpdo);

		$fields = $this->xpdo->getFieldMeta('msProductData'); unset($fields['id']);
		$this->dataFields = array_keys($fields);

		$aggregates = $this->xpdo->getAggregates('msProductData');
		$composites = $this->xpdo->getComposites('msProductData');
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

			$fieldType= $this->data->_getPHPType($k);
			if ($fieldType == 'float') {
				return $this->data->_setRaw($k, $v);
			}
			else {
				return $this->data->set($k, $v, $vType);
			}
		}
		else {
			return parent::set($k, $v, $vType);
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function _setRaw($key, $val) {
		if (in_array($key, $this->dataFields)) {
			if (!is_object($this->data)) {$this->loadData();}
			return $this->data->_setRaw($key, $val);
		}
		else {
			return parent::_setRaw($key, $val);
		}
	}

	/**
	 * {@inheritdoc}
	 */

    public function save($cacheFlag= null) {
        $res = parent::save($cacheFlag);
        if (!is_object($this->data)) {$this->loadData();}

        $this->setProductOptions($this->data);
        $this->data->set('id', parent::get('id'));
        $this->data->save($cacheFlag);

        return $res;
    }

    /**
     * Pass product options from Product to ProductData
     * @param msProductData $data
     */
    public function setProductOptions(msProductData &$data) {
        $productOptions = array();

        $this->optionKeys = $this->getOptionKeys();
        foreach ($this->optionKeys as $option) {
            $productOptions[$option] = $this->get($option);
        }

        $data->set('product_options', $productOptions);
    }

    /**
     * Prepare criteria for a list of available options of current product
     * @return xPDOQuery
     */
    public function prepareOptionListCriteria() {
        $q = $this->xpdo->newQuery('msCategoryMember', array('product_id' => $this->get('id')));
        $q->select('category_id');
        if ($q->prepare() && $q->stmt->execute()){
            $categories = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        $categories[] = $this->get('parent');
        $categories = array_unique($categories);
        $c = $this->xpdo->newQuery('msOption');
        $c->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.option_id=msOption.id');
        $c->leftJoin('modCategory', 'Category', 'Category.id=msOption.category');
        $c->where(array(
            'msCategoryOption.active' => 1,
            'msCategoryOption.category_id:IN' => $categories,
        ));
        $c->sortby('msCategoryOption.rank');

        return $c;
    }

    /**
     * Return array of option keys for product by its category
     * @return array
     */
    public function getOptionKeys() {
        /** @var xPDOQuery $c */
        $c = $this->prepareOptionListCriteria();

        $c->select('msOption.key');
        if ($c->prepare() && $c->stmt->execute()){
            return $c->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return array();
    }

    /**
     * Return array of option fields for product by its category
     * @return array
     */
    public function getOptionFields() {
        $fields = array();
        /** @var xPDOQuery $c */
        $c = $this->prepareOptionListCriteria();

        $c->select(array(
            $this->xpdo->getSelectColumns('msOption', 'msOption'),
            $this->xpdo->getSelectColumns('msCategoryOption', 'msCategoryOption', '', array('id', 'option_id', 'category_id'), true),
            '`Category`.`category` AS `category_name`',
        ));

        $options = $this->xpdo->getIterator('msOption', $c);

        /** @var msOption $option */
        foreach ($options as $option) {
            $field = $option->toArray();
            $value = $option->getValue($this->get('id'));
            $field['value'] = !is_null($value) ? $value : $field['value'];
            $field['ext_field'] = $option->getManagerField($field);
            $fields[] = $field;
        }
        return $fields;
    }


	/**
	 * {@inheritdoc}
	 */
	public function get($k, $format = null, $formatTemplate= null) {
		if (is_array($k)) {
			$tmp = array();
			foreach ($k as $v) {
				if (strpos($v, 'vendor_') !== false || strpos($v, 'vendor.') !== false || in_array($v, $this->dataFields)) {
					$tmp[$v] = $this->get($v, $format, $formatTemplate);
				}
				elseif (array_key_exists($v, $this->_fields)) {
					$tmp[$v] = parent::get($v, $format, $formatTemplate);
				}
			}
			return $tmp;
		}
		elseif (strpos($k, 'vendor_') !== false || strpos($k, 'vendor.') !== false) {
			if ($this->vendor === null) {$this->loadVendor();}
			return $this->vendor->get(substr($k, 7), $format, $formatTemplate);
		}
		elseif (in_array($k, $this->dataFields)) {
			if ($this->data === null) {$this->loadData();}
			return $this->data->get($k, $format, $formatTemplate);
		}
		elseif (in_array($k, $this->optionKeys) ||
            (($optFields = explode('.', $k)) && in_array($optFields[0], $this->optionKeys))) {
            if (isset($this->$k)) {
                return $this->$k;
            }
            $this->loadOptions();
            $value = isset($this->options[$k]) ? $this->options[$k] : null;
            return $value;
		} else {
            return parent::get($k, $format, $formatTemplate);
        }
	}


	/**
	 * {@inheritdoc}
	 */
	public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
		$array = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);

		if ($this->data === null) {$this->loadData();}
		if ($this->vendor === null) {$this->loadVendor();}
        $this->loadOptions();

		return array_merge($array, $this->data->toArray(), $this->vendor->toArray('vendor.'), $this->options);
	}


	/**
	 * Loads product data
	 */
	public function loadData() {
		if (!is_object($this->data) || !($this->data instanceof msProductData)) {
			if (!$this->data = $this->getOne('Data')) {
				$this->data = $this->xpdo->newObject('msProductData');
			}
		}
		return $this->data;
	}


	/**
	 * Loads product vendor
	 */
	public function loadVendor() {
		if (!is_object($this->vendor) || !($this->vendor instanceof msVendor)) {
			if (!$this->vendor = $this->getOne('Vendor')) {
				$this->vendor = $this->xpdo->newObject('msVendor');
			}
		}
		return $this->vendor;
	}

    /**
     * Loads product options
     */
    public function loadOptions() {
        if ($this->options === null) {
            $this->loadData();
            $this->options = $this->xpdo->call('msProductData', 'loadOptions', array(&$this->xpdo, $this->data->get('id')));
        }
        return $this->options;
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
	 * Returns names of fields for msProduct and msProductData
	 *
	 * @return array
	 * */
	public function getDataFieldsNames() {
		if (!is_object($this->data)) {$this->loadData();}
		return array_keys($this->data->_fieldMeta);
	}
	public function getResourceFieldsNames() {
		return array_keys($this->_fieldMeta);
	}
	public function getAllFieldsNames() {
		return array_merge($this->getResourceFieldsNames(), $this->getDataFieldsNames());
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


	/**
	 * {@inheritdoc}
	 */
	public function duplicate(array $options = array()) {
		if (!($this->xpdo instanceof modX)) return false;

		/* duplicate resource */
		$prefixDuplicate = !empty($options['prefixDuplicate']) ? true : false;
		$newName = !empty($options['newName']) ? $options['newName'] : ($prefixDuplicate ? $this->xpdo->lexicon('duplicate_of', array('name' => $this->get('pagetitle'))) : $this->get('pagetitle'));
		/** @var msProduct $newResource */
		$newResource = $this->xpdo->newObject($this->get('class_key'));
		$newResource->fromArray($this->toArray());
		$newResource->set('pagetitle', $newName);
		$newResource->set('image', null);
		$newResource->set('thumb', null);

		/* do published status preserving */
		$publishedMode = $this->getOption('publishedMode',$options,'preserve');
		switch ($publishedMode) {
			case 'unpublish':
				$newResource->set('published',false);
				$newResource->set('publishedon',0);
				$newResource->set('publishedby',0);
				break;
			case 'publish':
				$newResource->set('published',true);
				$newResource->set('publishedon',time());
				$newResource->set('publishedby',$this->xpdo->user->get('id'));
				break;
			case 'preserve':
			default:
				$newResource->set('published',$this->get('published'));
				$newResource->set('publishedon',$this->get('publishedon'));
				$newResource->set('publishedby',$this->get('publishedby'));
				break;
		}

		/* allow overrides for every item */
		if (!empty($options['overrides']) && is_array($options['overrides'])) {
			$newResource->fromArray($options['overrides']);
		}
		$newResource->set('id',0);

		/* make sure children get assigned to new parent */
		$newResource->set('parent',isset($options['parent']) ? $options['parent'] : $this->get('parent'));
		$newResource->set('createdby',$this->xpdo->user->get('id'));
		$newResource->set('createdon',time());
		$newResource->set('editedby',0);
		$newResource->set('editedon',0);

		/* get new alias */
		$alias = $newResource->cleanAlias($newName);
		if ($this->xpdo->getOption('friendly_urls', $options, false)) {
			/* auto assign alias */
			$aliasPath = $newResource->getAliasPath($newName);
			$dupeContext = $this->xpdo->getOption('global_duplicate_uri_check', $options, false) ? '' : $newResource->get('context_key');
			if ($newResource->isDuplicateAlias($aliasPath, $dupeContext)) {
				$alias = '';
				if ($newResource->get('uri_override')) {
					$newResource->set('uri_override', false);
				}
			}
		}
		$newResource->set('alias',$alias);

		/* set new menuindex */
		$childrenCount = $this->xpdo->getCount('modResource',array('parent' => $this->get('parent')));
		$newResource->set('menuindex',$childrenCount);

		/* save resource */
		if (!$newResource->save()) {
			return $this->xpdo->lexicon('resource_err_duplicate');
		}

		$tvds = $this->getMany('TemplateVarResources');
		/** @var modTemplateVarResource $oldTemplateVarResource */
		foreach ($tvds as $oldTemplateVarResource) {
			/** @var modTemplateVarResource $newTemplateVarResource */
			$newTemplateVarResource = $this->xpdo->newObject('modTemplateVarResource');
			$newTemplateVarResource->set('contentid',$newResource->get('id'));
			$newTemplateVarResource->set('tmplvarid',$oldTemplateVarResource->get('tmplvarid'));
			$newTemplateVarResource->set('value',$oldTemplateVarResource->get('value'));
			$newTemplateVarResource->save();
		}

		$groups = $this->getMany('ResourceGroupResources');
		/** @var modResourceGroupResource $oldResourceGroupResource */
		foreach ($groups as $oldResourceGroupResource) {
			/** @var modResourceGroupResource $newResourceGroupResource */
			$newResourceGroupResource = $this->xpdo->newObject('modResourceGroupResource');
			$newResourceGroupResource->set('document_group',$oldResourceGroupResource->get('document_group'));
			$newResourceGroupResource->set('document',$newResource->get('id'));
			$newResourceGroupResource->save();
		}

		$categories = $this->getMany('Categories');
		/** @var msCategoryMember $oldCategoryMember */
		foreach ($categories as $oldCategoryMember) {
			/** @var msCategoryMember $newCategoryMember */
			$newCategoryMember = $this->xpdo->newObject('msCategoryMember');
			$newCategoryMember->set('category_id',$oldCategoryMember->get('category_id'));
			$newCategoryMember->set('product_id',$newResource->get('id'));
			$newCategoryMember->save();
		}

		return $newResource;
	}


	/*
	 * Returns array with all neighborhood products
	 *
	 * @return array $arr Array with neighborhood from left and right
	 * */
	public function getNeighborhood() {
		$arr = array();

		$q = $this->xpdo->newQuery('msProduct', array('parent' => $this->parent, 'class_key' => 'msProduct'));
		$q->sortby('menuindex','ASC');
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


	/**
	 * {@inheritdoc}
	 */
	public function process() {
		/* @var msProductData $data */
		if ($data = $this->getOne('Data')) {
			/* @var miniShop2 $miniShop2 */
			$miniShop2 = $this->xpdo->getService('minishop2');
			$pls = $data->toArray();
			$tmp = $pls['price'];
			$pls['price'] = $this->getPrice($pls);
			if ($pls['price'] != $tmp) {
				$pls['old_price'] = $tmp;
			}
			$pls['price'] = $miniShop2->formatPrice($pls['price']);
			$pls['old_price'] = $miniShop2->formatPrice($pls['old_price']);
			$pls['weight'] = $miniShop2->formatWeight($this->getWeight($pls));
			unset($pls['id']);

			$this->xpdo->setPlaceholders($pls);

            $this->loadOptions();
            $this->xpdo->setPlaceholders($this->options);
		}
		/* @var msVendor $vendor */
		if ($vendor = $this->getOne('Vendor')) {
			$this->xpdo->setPlaceholders($vendor->toArray('vendor.'));
		}
		$this->xpdo->lexicon->load('minishop2:default');
		$this->xpdo->lexicon->load('minishop2:cart');
		$this->xpdo->lexicon->load('minishop2:product');
		return parent::process();
	}

	public function generateAllThumbnails() {
		$this->loadData()->generateAllThumbnails();
	}

	public function initializeMediaSource() {
		return $this->loadData()->initializeMediaSource($this->get('context_key'));
	}

	public function updateProductImage() {
		return $this->loadData()->updateProductImage();
	}

	public function getPrice($data = array()) {
		return $this->loadData()->getPrice($data);
	}

	public function getWeight($data = array()) {
		return $this->loadData()->getWeight($data);
	}
}