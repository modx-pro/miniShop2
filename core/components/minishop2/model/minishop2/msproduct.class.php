<?php

/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/product/create.class.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/product/update.class.php';

class msProduct extends modResource
{
    public $showInContextMenu = true;
    public $allowChildrenResources = false;
    /** @var msProductData $data */
    protected $Data;
    protected $dataRelated = array();
    /** @var msVendor $Vendor */
    protected $Vendor;
    protected $options = null;
    protected $_originalFieldMeta;


    /**
     * msProduct constructor.
     *
     * @param xPDO $xpdo
     */
    function __construct(xPDO & $xpdo)
    {
        parent::__construct($xpdo);
        parent::set('class_key', 'msProduct');
        $this->_originalFieldMeta = $this->_fieldMeta;

        $aggregates = $this->xpdo->getAggregates('msProductData');
        $composites = $this->xpdo->getComposites('msProductData');
        $this->dataRelated = array_merge(array_keys($aggregates), array_keys($composites));
    }


    /**
     * @param xPDO $xpdo
     * @param string $className
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return msProduct
     */
    public static function load(xPDO & $xpdo, $className, $criteria = null, $cacheFlag = true)
    {
        if (!is_object($criteria)) {
            $criteria = $xpdo->getCriteria($className, $criteria, $cacheFlag);
        }
        /** @noinspection PhpParamsInspection */
        $xpdo->addDerivativeCriteria($className, $criteria);

        return parent::load($xpdo, $className, $criteria, $cacheFlag);
    }


    /**
     * @param xPDO $xpdo
     * @param string $className
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return array
     */
    public static function loadCollection(xPDO & $xpdo, $className, $criteria = null, $cacheFlag = true)
    {
        if (!is_object($criteria)) {
            $criteria = $xpdo->getCriteria($className, $criteria, $cacheFlag);
        }
        /** @noinspection PhpParamsInspection */
        $xpdo->addDerivativeCriteria($className, $criteria);

        return parent::loadCollection($xpdo, $className, $criteria, $cacheFlag);
    }


    /**
     * @param xPDO $modx
     *
     * @return string
     */
    public static function getControllerPath(xPDO &$modx)
    {
        return $modx->getOption('minishop2.core_path', null,
            $modx->getOption('core_path') . 'components/minishop2/') . 'controllers/product/';
    }


    /**
     * @return array
     */
    public function getContextMenuText()
    {
        $this->xpdo->lexicon->load('minishop2:default');

        return array(
            'text_create' => $this->xpdo->lexicon('ms2_product'),
            'text_create_here' => $this->xpdo->lexicon('ms2_product_create_here'),
        );
    }


    /**
     * @return null|string
     */
    public function getResourceTypeName()
    {
        $this->xpdo->lexicon->load('minishop2:default');

        return $this->xpdo->lexicon('ms2_product_type');
    }


    /**
     * @param string $k
     * @param null $v
     * @param string $vType
     *
     * @return bool
     */
    public function set($k, $v = null, $vType = '')
    {
        return isset($this->_originalFieldMeta[$k])
            ? parent::set($k, $v, $vType)
            : $this->loadData()->set($k, $v, $vType);
    }


    /**
     * @param string $key
     * @param mixed $val
     *
     * @return bool
     */
    protected function _setRaw($key, $val)
    {
        return isset($this->_originalFieldMeta[$key])
            ? parent::_setRaw($key, $val)
            : $this->loadData()->_setRaw($key, $val);
    }


    /**
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        if (!$this->isNew() && parent::get('class_key') != 'msProduct') {
            $this->loadData()->remove();
            parent::set('show_in_tree', true);
        } else {
            $this->loadData();
        }

        return parent::save($cacheFlag);
    }


    /**
     * @param array|string $k
     * @param null $format
     * @param null $formatTemplate
     *
     * @return array|mixed|null|xPDOObject
     */
    public function get($k, $format = null, $formatTemplate = null)
    {

        if (is_array($k)) {
            $array = array();
            foreach ($k as $v) {
                $array[$v] = isset($this->_originalFieldMeta[$v])
                    ? parent::get($v, $format, $formatTemplate)
                    : $this->get($v, $format, $formatTemplate);
            }

            return $array;
        } elseif (isset($this->_originalFieldMeta[$k])) {
            return parent::get($k, $format, $formatTemplate);
        } elseif (strpos($k, 'vendor_') !== false || strpos($k, 'vendor.') !== false) {
            return $this->loadVendor()->get(substr($k, 7), $format, $formatTemplate);
        } elseif (isset($this->loadData()->_fields[$k])) {
            return $this->loadData()->get($k, $format, $formatTemplate);
        } elseif (
            in_array($k, $this->loadData()->getOptionKeys()) ||
            (($optFields = explode('.', $k)) && in_array($optFields[0], $this->loadData()->getOptionKeys()))
        ) {
            if (isset($this->$k)) {
                return $this->$k;
            }
            $this->loadOptions();
            $value = isset($this->options[$k])
                ? $this->options[$k]
                : null;

            return $value;
        } else {
            return parent::get($k, $format, $formatTemplate);
        }
    }


    /**
     * @param string $keyPrefix
     * @param bool $rawValues
     * @param bool $excludeLazy
     * @param bool $includeRelated
     *
     * @return array
     */
    public function toArray($keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
    {
        $original = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
        $additional = array_merge(
            $this->loadData()->toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated),
            $this->loadOptions(),
            $this->loadVendor()->toArray($keyPrefix . 'vendor.', $rawValues, $excludeLazy, $includeRelated)
        );
        $intersect = array_keys(array_intersect_key($original, $additional));
        foreach ($intersect as $key) {
            unset($additional[$key]);
        }

        return array_merge($original, $additional);
    }


    /**
     * @return msProductData|null|object|xPDOObject
     */
    public function loadData()
    {
        if (!is_object($this->Data) || !($this->Data instanceof msProductData)) {
            if (!$this->Data = $this->getOne('Data')) {
                $this->Data = $this->xpdo->newObject('msProductData');
                parent::addOne($this->Data);
            }
        }

        return $this->Data;
    }


    /**
     * Loads product vendor
     */
    public function loadVendor()
    {
        if (!is_object($this->Vendor) || !($this->Vendor instanceof msVendor)) {
            if (!$this->Vendor = $this->getOne('Vendor')) {
                $this->Vendor = $this->xpdo->newObject('msVendor');
            }
        }

        return $this->Vendor;
    }


    /**
     * Loads product options
     */
    public function loadOptions()
    {
        if ($this->options === null) {
            $this->options = $this->xpdo->call('msProductData', 'loadOptions', array(
                &$this->xpdo,
                $this->loadData()->get('id'),
            ));
        }

        return $this->options;
    }


    /**
     * @param string $alias
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return null|xPDOObject
     */
    public function & getOne($alias, $criteria = null, $cacheFlag = true)
    {
        $object = in_array($alias, $this->dataRelated)
            ? $this->loadData()->getOne($alias, $criteria, $cacheFlag)
            : parent::getOne($alias, $criteria, $cacheFlag);

        return $object;
    }


    /**
     * @param xPDOObject $obj
     * @param string $alias
     *
     * @return bool
     */
    public function addOne(& $obj, $alias = '')
    {
        if (empty ($alias)) {
            if ($obj->_alias == $obj->_class) {
                $aliases = $this->_getAliases($obj->_class, 1);
                if (!empty($aliases)) {
                    $obj->_alias = reset($aliases);
                }
            }
            $alias = $obj->_alias;
        }

        return in_array($alias, $this->dataRelated)
            ? $this->loadData()->addOne($obj, $alias)
            : parent::addOne($obj, $alias);
    }


    /**
     * @param string $alias
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return array
     */
    public function & getMany($alias, $criteria = null, $cacheFlag = false)
    {
        $objects = in_array($alias, $this->dataRelated)
            ? $this->loadData()->getMany($alias, $criteria, $cacheFlag)
            : parent::getMany($alias, $criteria, $cacheFlag);

        return $objects;
    }


    /**
     * @param mixed $obj
     * @param string $alias
     *
     * @return bool
     */
    public function addMany(& $obj, $alias = '')
    {
        if (empty ($alias)) {
            if ($obj->_alias == $obj->_class) {
                $aliases = $this->_getAliases($obj->_class, 1);
                if (!empty($aliases)) {
                    $obj->_alias = reset($aliases);
                }
            }
            $alias = $obj->_alias;
        }

        return in_array($alias, $this->dataRelated)
            ? $this->loadData()->addMany($obj, $alias)
            : parent::addMany($obj, $alias);
    }


    /**
     * Returns names of fields for msProduct and msProductData
     *
     * @return array
     */
    public function getDataFieldsNames()
    {
        return array_keys($this->loadData()->_fieldMeta);
    }


    /**
     * @return array
     */
    public function getResourceFieldsNames()
    {
        return array_keys($this->_originalFieldMeta);
    }


    /**
     * @return array
     */
    public function getAllFieldsNames()
    {
        return array_merge($this->getResourceFieldsNames(), $this->getDataFieldsNames());
    }


    /**
     * @param array $options
     *
     * @return msProduct
     */
    public function duplicate(array $options = array())
    {
        parent::set('categories', $this->loadData()->get('categories'));
        parent::set('options', $this->loadData()->get('options'));
        parent::set('links', $this->loadData()->get('links'));

        /** @var msProduct $new */
        return parent::duplicate($options);
    }


    /**
     * Returns array with all neighborhood products
     *
     * @return array $arr Array with neighborhood from left and right
     */
    public function getNeighborhood()
    {
        $arr = array();

        $q = $this->xpdo->newQuery('msProduct', array('parent' => $this->parent, 'class_key' => 'msProduct'));
        $q->sortby('menuindex', 'ASC');
        $q->select('id');
        if ($q->prepare() && $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $current = array_search($this->id, $ids);

            $right = $left = array();
            foreach ($ids as $k => $v) {
                if ($k > $current) {
                    $right[] = $v;
                } elseif ($k < $current) {
                    $left[] = $v;
                }
            }

            $arr = array(
                'left' => array_reverse($left),
                'right' => $right,
            );
        }

        return $arr;
    }


    /**
     * @return string
     */
    public function process()
    {
        /** @var msProductData $data */
        if ($data = $this->getOne('Data')) {
            /** @var miniShop2 $miniShop2 */
            $miniShop2 = $this->xpdo->getService('miniShop2');
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
        /** @var msVendor $vendor */
        if ($vendor = $this->getOne('Vendor')) {
            $this->xpdo->setPlaceholders($vendor->toArray('vendor.'));
        }
        $this->xpdo->lexicon->load('minishop2:default');
        $this->xpdo->lexicon->load('minishop2:cart');
        $this->xpdo->lexicon->load('minishop2:product');

        return parent::process();
    }


    /**
     *
     */
    public function generateAllThumbnails()
    {
        $this->loadData()->generateAllThumbnails();
    }


    /**
     * @return bool|modMediaSource|null|object
     */
    public function initializeMediaSource()
    {
        return $this->loadData()->initializeMediaSource(parent::get('context_key'));
    }


    /**
     * @return bool|mixed
     */
    public function updateProductImage()
    {
        return $this->loadData()->updateProductImage();
    }


    /**
     * @param array $data
     *
     * @return mixed|string
     */
    public function getPrice($data = array())
    {
        return $this->loadData()->getPrice($data);
    }


    /**
     * @param array $data
     *
     * @return mixed|string
     */
    public function getWeight($data = array())
    {
        return $this->loadData()->getWeight($data);
    }
}