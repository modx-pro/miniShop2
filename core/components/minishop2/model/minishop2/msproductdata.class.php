<?php

/**
 * @property int id
 */
class msProductData extends xPDOSimpleObject
{
    var $source;
    /** @var modMediaSource $mediaSource */
    public $mediaSource;
    protected $optionKeys = null;


    /**
     * All json fields of product are synchronized with msProduct Options
     *
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        $save = parent::save($cacheFlag);
        $this->saveProductCategories();
        $this->saveProductOptions();
        $this->saveProductLinks();

        return $save;
    }


    /**
     * @param xPDO $xpdo
     * @param $product
     *
     * @return array
     */
    public static function loadOptions(xPDO & $xpdo, $product)
    {
        $c = $xpdo->newQuery('msProductOption');
        $c->rightJoin('msOption', 'msOption', 'msProductOption.key=msOption.key');
        $c->leftJoin('modCategory', 'Category', 'Category.id=msOption.category');
        $c->where(array('msProductOption.product_id' => $product));

        $c->select($xpdo->getSelectColumns('msOption', 'msOption'));
        $c->select($xpdo->getSelectColumns('msProductOption', 'msProductOption', '', array('key'), true));
        $c->select('Category.category AS category_name');
        $data = array();
        if ($c->prepare() && $c->stmt->execute()) {
            while ($option = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                // If the option is repeated, its value will be an array
                if (isset($data[$option['key']])) {
                    if (!is_array($data[$option['key']])) {
                        $data[$option['key']] = array($data[$option['key']]);
                    }
                    $data[$option['key']][] = $option['value'];
                } else { // Single option will be a string
                    $data[$option['key']] = $option['value'];
                }

                foreach ($option as $key => $value) {
                    $data[$option['key'] . '.' . $key] = $value;
                }
            }
        }

        return $data;
    }


    /**
     *
     */
    protected function saveProductOptions()
    {
        $table = $this->xpdo->getTableName('msProductOption');
        $id = parent::get('id');
        $add = $this->xpdo->prepare("INSERT INTO {$table} (`product_id`, `key`, `value`) VALUES ({$id}, ?, ?)");

        $arrays = array();
        foreach ($this->_fieldMeta as $name => $field) {
            if (strtolower($field['phptype']) == 'json') {
                $arrays[$name] = parent::get($name);
            }
        }

        // Copy JSON fields to options
        $c = $this->xpdo->newQuery('msProductOption');
        $c->command('DELETE');
        $c->where(array(
            'product_id' => $id,
            'key:IN' => array_keys($arrays),
        ));
        if ($c->prepare() && $c->stmt->execute()) {
            foreach ($arrays as $key => $array) {
                if (is_array($array)) {
                    foreach ($array as $value) {
                        if ($value !== '') {
                            $add->execute(array($key, $value));
                        }
                    }
                }
            }
        }

        // Save given options
        $options = parent::get('options');
        if (is_array($options)) {
            $c = $this->xpdo->newQuery('msProductOption');
            $c->command('DELETE');
            $c->where(array(
                'product_id' => $id,
            ));
            if ($category_keys = $this->getOptionKeys()) {
                $c->andCondition(array(
                    'key:NOT IN' => array_merge($category_keys, array_keys($arrays)),
                ), '', 1);
            }
            if ($given_keys = array_keys($options)) {
                $c->orCondition(array(
                    'key:IN' => $given_keys,
                ), '', 1);
            }
            if ($c->prepare()) {
                $c->stmt->execute();
            }
            foreach ($options as $key => $array) {
                if (!is_array($array)) {
                    $array = array($array);
                }

                // fix duplicate, empty options
                $array = array_map('trim', $array);
                $array = array_keys(array_flip($array));
                $array = array_diff($array, array(''));
                sort($array);
                $this->set($key, $array);

                foreach ($array as $value) {
                    $add->execute(array($key, $value));
                }
            }
        }
    }


    /**
     * Additional product categories
     */
    protected function saveProductCategories()
    {
        $categories = parent::get('categories');
        if (is_string($categories)) {
            $categories = json_decode($categories, true);
        }
        if (is_array($categories)) {
            $id = parent::get('id');
            $parent = parent::get('parent');

            $table = $this->xpdo->getTableName('msCategoryMember');
            $remove = $this->xpdo->prepare("DELETE FROM {$table} WHERE product_id = $id AND category_id = ?;");
            $add = $this->xpdo->prepare("INSERT INTO {$table} (product_id, category_id) VALUES ($id, ?);");

            if (is_string($categories)) {
                $categories = json_decode($categories, true);
            }
            if (is_array($categories)) {
                // Plain array with all product categories
                if (isset($categories[0])) {
                    if (!parent::isNew()) {
                        $this->xpdo->removeCollection('msCategoryMember', array('product_id' => $id));
                    }
                    foreach ($categories as $category) {
                        if ($category != $parent) {
                            $add->execute(array($category));
                        }
                    }
                } // Key-value array with categories to add of remove
                else {
                    foreach ($categories as $category => $selected) {
                        if (!$selected) {
                            $remove->execute(array($category));
                        } elseif ($category != $parent) {
                            $add->execute(array($category));
                        }
                    }
                }
            }
            $remove->execute(array($parent));
        }
    }


    /**
     *
     */
    protected function saveProductLinks()
    {
        $links = parent::get('links');
        if (is_array($links)) {
            $table = $this->xpdo->getTableName('msProductLink');
            $add = $this->xpdo->prepare("INSERT INTO {$table} (link, master, slave) VALUES (?, ?, ?);");
            foreach ($links as $type => $values) {
                foreach ($values as $link => $ids) {
                    foreach ($ids as $id) {
                        if ($type == 'master') {
                            $add->execute(array($link, $this->id, $id));
                        } elseif ($type == 'slave') {
                            $add->execute(array($link, $id, $this->id));
                        }
                    }
                }
            }
        }
    }


    /**
     * @return xPDOQuery
     */
    public function prepareOptionListCriteria()
    {
        $categories = array();
        $q = $this->xpdo->newQuery('msCategoryMember', array('product_id' => parent::get('id')));
        $q->select('category_id');
        if ($q->prepare() && $q->stmt->execute()) {
            $categories = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        if ($product = $this->getOne('Product')) {
            $categories[] = $product->get('parent');
        } elseif (!empty($_GET['parent'])) {
            $categories[] = (int)$_GET['parent'];
        }
        $categories = array_unique($categories);

        $c = $this->xpdo->newQuery('msOption');
        $c->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.option_id = msOption.id');
        $c->leftJoin('modCategory', 'Category', 'Category.id = msOption.category');
        $c->sortby('msCategoryOption.rank');
        $c->where(array('msCategoryOption.active' => 1));
        if (!empty($categories[0])) {
            $c->where(array('msCategoryOption.category_id:IN' => $categories));
        }

        return $c;
    }


    /**
     * @param bool $force
     *
     * @return array
     */
    public function getOptionKeys($force = false)
    {
        if ($this->optionKeys === null || $force) {
            /** @var xPDOQuery $c */
            $c = $this->prepareOptionListCriteria();
            $c->groupby('msOption.id, msCategoryOption.rank');
            $c->select('msOption.key');

            $this->optionKeys = $c->prepare() && $c->stmt->execute()
                ? $c->stmt->fetchAll(PDO::FETCH_COLUMN)
                : array();
        }

        return $this->optionKeys;
    }


    /**
     * @return array
     */
    public function getOptionFields()
    {
        $fields = array();
        /** @var xPDOQuery $c */
        $c = $this->prepareOptionListCriteria();

        $c->select(array(
            $this->xpdo->getSelectColumns('msOption', 'msOption'),
            $this->xpdo->getSelectColumns('msCategoryOption', 'msCategoryOption', '',
                array('id', 'option_id', 'category_id'), true
            ),
            'Category.category AS category_name',
        ));

        $options = $this->xpdo->getIterator('msOption', $c);

        /** @var msOption $option */
        foreach ($options as $option) {
            $field = $option->toArray();
            $value = $option->getValue(parent::get('id'));
            $field['value'] = !is_null($value) ? $value : $field['value'];
            $field['ext_field'] = $option->getManagerField($field);
            $fields[] = $field;
        }

        return $fields;
    }


    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = array())
    {
        $this->xpdo->removeCollection('msProductOption', array('product_id' => $this->id));
        $this->xpdo->removeCollection('msCategoryMember', array('product_id' => $this->id));
        $this->xpdo->removeCollection('msProductLink', array('master' => $this->id, 'OR:slave:=' => $this->id));

        $files = $this->getMany('Files');
        /** @var msProductFile $file */
        foreach ($files as $file) {
            $file->remove();
        }

        return parent::remove($ancestors);
    }


    /**
     *
     */
    public function generateAllThumbnails()
    {
        $files = $this->xpdo->getIterator('msProductFile', array(
            'type' => 'image',
            'parent' => 0,
        ));

        /** @var msProductFile $file */
        foreach ($files as $file) {
            $file->generateThumbnails();
        }
    }


    /**
     * @param string $ctx
     *
     * @return bool|modMediaSource|null|object
     */
    public function initializeMediaSource($ctx = '')
    {
        if ($this->mediaSource = $this->xpdo->getObject('sources.modMediaSource', $this->get('source'))) {
            if (empty($ctx)) {
                $product = $this->getOne('Product');
                $ctx = $product->get('context_key');
            }
            $this->mediaSource->set('ctx', $ctx);
            $this->mediaSource->initialize();

            return $this->mediaSource;
        }

        return false;
    }


    /**
     *
     */
    public function rankProductImages()
    {
        // Check if need to update files ranks
        $c = $this->xpdo->newQuery('msProductFile', array(
            'product_id' => $this->get('id'),
            'parent' => 0,
        ));
        $c->groupby('rank');
        $c->select('COUNT(rank) as idx');
        $c->sortby('idx', 'DESC');
        $c->limit(1);
        if ($c->prepare() && $c->stmt->execute()) {
            if ($c->stmt->fetchColumn() == 1) {
                return;
            }
        }

        // Update ranks
        $c = $this->xpdo->newQuery('msProductFile', array(
            'product_id' => $this->get('id'),
            'parent' => 0,
        ));
        $c->select('id');
        $c->sortby('rank ASC, createdon', 'ASC');

        if ($c->prepare() && $c->stmt->execute()) {
            $table = $this->xpdo->getTableName('msProductFile');
            $update = $this->xpdo->prepare("UPDATE {$table} SET rank = ? WHERE (id = ? OR parent = ?)");
            $ids = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($ids as $k => $id) {
                $update->execute(array($k, $id, $id));
            }

            $alter = $this->xpdo->prepare("ALTER TABLE {$table} ORDER BY rank ASC");
            $alter->execute();
        }

    }


    /**
     * @return bool|mixed
     */
    public function updateProductImage()
    {
        $this->rankProductImages();
        $c = $this->xpdo->newQuery('msProductFile', array(
            'product_id' => $this->id,
            'parent' => 0,
            'type' => 'image',
            //'active' => true,
        ));
        $c->sortby('rank', 'ASC');
        $c->limit(1);
        /** @var msProductFile $file */
        $file = $this->xpdo->getObject('msProductFile', $c);
        if ($file) {
            $thumb = $file->getFirstThumbnail();
            $arr = array(
                'image' => $file->get('url'),
                'thumb' => !empty($thumb['url'])
                    ? $thumb['url']
                    : '',
            );
        } else {
            $arr = array(
                'image' => null,
                'thumb' => null,
            );
        }

        $this->fromArray($arr);
        if (parent::save()) {
            /** @var msProduct $product */
            if ($product = $this->getOne('Product')) {
                $product->clearCache();
            }
        }

        if ($this->xpdo->getOption('ms2gallery_sync_ms2')) {
            /** @var ms2Gallery $ms2Gallery */
            $ms2Gallery = $this->xpdo->getService('ms2gallery', 'ms2Gallery',
                MODX_CORE_PATH . 'components/ms2gallery/model/ms2gallery/');
            if ($ms2Gallery && method_exists($ms2Gallery, 'syncFiles')) {
                $ms2Gallery->syncFiles('ms2', $this->id, true);
            }
        }

        /** @var miniShop2 $miniShop2 */
        if (empty($arr['thumb']) && $miniShop2 = $this->xpdo->getService('miniShop2')) {
            $arr['thumb'] = $miniShop2->config['defaultThumb'];
        }

        return $arr['thumb'];
    }


    /**
     * @param array|string $k
     * @param null $format
     * @param null $formatTemplate
     *
     * @return array|null
     */
    public function get($k, $format = null, $formatTemplate = null)
    {
        if (is_array($k)) {
            $array = array();
            foreach ($k as $v) {
                $array[$v] = isset($this->_fieldMeta[$v])
                    ? parent::get($v, $format, $formatTemplate)
                    : $this->get($v, $format, $formatTemplate);
            }

            return $array;
        } else {
            $value = null;
            switch ($k) {
                case 'categories':
                    $c = $this->xpdo->newQuery('msCategoryMember', array('product_id' => $this->id));
                    $c->select('category_id');
                    if ($c->prepare() && $c->stmt->execute()) {
                        $value = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
                    }
                    break;
                case 'options':
                    $c = $this->xpdo->newQuery('msProductOption', array('product_id' => $this->id));
                    $c->select('key,value');
                    $c->sortby('value');
                    if ($c->prepare() && $c->stmt->execute()) {
                        $value = array();
                        while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isset($value[$row['key']])) {
                                $value[$row['key']][] = $row['value'];
                            } else {
                                $value[$row['key']] = array($row['value']);
                            }
                        }
                    }
                    break;
                case 'links':
                    $value = array('master' => array(), 'slave' => array());
                    $c = $this->xpdo->newQuery('msProductLink', array('master' => $this->id));
                    $c->select('link,slave');
                    if ($c->prepare() && $c->stmt->execute()) {
                        while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isset($value['master'][$row['link']])) {
                                $value['master'][$row['link']][] = $row['slave'];
                            } else {
                                $value['master'][$row['link']] = array($row['slave']);
                            }
                        }
                    }

                    $c = $this->xpdo->newQuery('msProductLink', array('slave' => $this->id));
                    $c->select('link,master');
                    if ($c->prepare() && $c->stmt->execute()) {
                        while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isset($value['slave'][$row['link']])) {
                                $value['slave'][$row['link']][] = $row['master'];
                            } else {
                                $value['slave'][$row['link']] = array($row['master']);
                            }
                        }
                    }
                    break;
                default:
                    $value = parent::get($k, $format, $formatTemplate);
            }

            return $value;
        }
    }


    /**
     * Return product price
     *
     * @param array $data Any additional data for price modification
     *
     * @return mixed|string
     */
    public function getPrice($data = array())
    {
        $price = parent::get('price');
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $this->xpdo->getService('miniShop2')) {
            if (empty($data)) {
                $data = $this->toArray();
            }
            $params = array(
                'product' => $this,
                'data' => $data,
                'price' => $price,
            );
            $response = $miniShop2->invokeEvent('msOnGetProductPrice', $params);
            if ($response['success']) {
                $price = $params['price'] = $response['data']['price'];
            }
        }


        return $price;
    }


    /**
     * Return product weight.
     *
     * @param array $data Any additional data for weight modification
     *
     * @return mixed|string
     */
    public function getWeight($data = array())
    {
        $weight = parent::get('weight');
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $this->xpdo->getService('miniShop2')) {
            if (empty($data)) {
                $data = $this->toArray();
            }
            $params = array(
                'product' => $this,
                'data' => $data,
                'weight' => $weight,
            );
            $response = $miniShop2->invokeEvent('msOnGetProductWeight', $params);
            if ($response['success']) {
                $weight = $params['weight'] = $response['data']['weight'];
            }
        }

        return $weight;
    }
}
