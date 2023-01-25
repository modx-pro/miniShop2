<?php

/**
 * @property int id
 */
class msProductData extends xPDOSimpleObject
{
    public $source;
    /** @var modMediaSource $mediaSource */
    public $mediaSource;
    protected $optionKeys = null;

    /**
     *
     */
    public function prepareObject()
    {
        // prepare "array" fields
        foreach ($this->getArraysValues() as $name => $array) {
            $array = $this->prepareOptionValues($array);
            parent::set($name, $array);
        }

        if ($this->isNew()) {
            parent::set('source', $this->xpdo->getOption('ms2_product_source_default', null, 1));
        }

        parent::set('price', (float)parent::get('price'));
        parent::set('old_price', (float)parent::get('old_price'));
        parent::set('weight', (float)parent::get('weight'));
    }

    /**
     * All json fields of product are synchronized with msProduct Options
     *
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        $this->prepareObject();
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
    public static function loadOptions(xPDO $xpdo, $product)
    {
        $c = $xpdo->newQuery('msProductOption');
        $c->rightJoin('msOption', 'msOption', 'msProductOption.key=msOption.key');
        $c->leftJoin('modCategory', 'Category', 'Category.id=msOption.category');
        $c->where(['msProductOption.product_id' => $product]);
        $c->select($xpdo->getSelectColumns('msOption', 'msOption'));
        $c->select($xpdo->getSelectColumns('msProductOption', 'msProductOption', '', ['key'], true));
        $c->select('Category.category AS category_name');
        $data = [];
        if ($c->prepare() && $c->stmt->execute()) {
            while ($option = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                // If the option is repeated, its value will be an array
                if (isset($data[$option['key']])) {
                    $data[$option['key']][] = $option['value'];
                } else {
                    $data[$option['key']] = [$option['value']];
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

        $arrays = $this->getArraysValues();
        // Copy JSON fields to options
        $c = $this->xpdo->newQuery('msProductOption');
        $c->command('DELETE');
        $c->where([
            'product_id' => $id,
            'key:IN' => array_keys($arrays),
        ]);
        if ($c->prepare() && $c->stmt->execute()) {
            foreach ($arrays as $key => $array) {
                $array = $this->prepareOptionValues($array);
                if (is_array($array)) {
                    foreach ($array as $value) {
                        $add->execute([$key, $value]);
                    }
                }
            }
        }

        // Save given options
        $options = parent::get('options');
        if (is_array($options)) {
            $c = $this->xpdo->newQuery('msProductOption');
            $c->command('DELETE');
            $c->where([
                'product_id' => $id,
            ]);
            if ($category_keys = $this->getOptionKeys()) {
                $c->andCondition([
                    'key:NOT IN' => array_merge($category_keys, array_keys($arrays)),
                ], '', 1);
            }
            if ($given_keys = array_keys($options)) {
                $c->orCondition([
                    'key:IN' => $given_keys,
                ], '', 1);
            }
            if ($c->prepare()) {
                $c->stmt->execute();
            }
            foreach ($options as $key => $array) {
                $array = $this->prepareOptionValues($array);
                if (is_array($array)) {
                    foreach ($array as $value) {
                        $add->execute([$key, $value]);
                    }
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

            // Plain array with all product categories
            if (isset($categories[0])) {
                if (!parent::isNew()) {
                    $this->xpdo->removeCollection('msCategoryMember', ['product_id' => $id]);
                }
                foreach ($categories as $category) {
                    if ($category != $parent) {
                        $add->execute([$category]);
                    }
                }

                // Key-value array with categories to add of remove
            } else {
                foreach ($categories as $category => $selected) {
                    if (!$selected) {
                        $remove->execute([$category]);
                    } elseif ($category != $parent) {
                        $add->execute([$category]);
                    }
                }
            }
            $remove->execute([$parent]);
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
                            $add->execute([$link, $this->id, $id]);
                        } elseif ($type == 'slave') {
                            $add->execute([$link, $id, $this->id]);
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
        $categories = [];
        $q = $this->xpdo->newQuery('msCategoryMember', ['product_id' => parent::get('id')]);
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
        $c->where(['msCategoryOption.active' => 1]);
        if (!empty($categories[0])) {
            $c->where(['msCategoryOption.category_id:IN' => $categories]);
        }
        $c->groupby('msOption.id');

        return $c;
    }

    /**
     * @return array
     */
    public function getArraysValues()
    {
        $arrays = [];
        foreach ($this->_fieldMeta as $name => $field) {
            if (strtolower($field['phptype']) === 'json') {
                $arrays[$name] = parent::get($name);
            }
        }

        return $arrays;
    }

    /**
     * @param null $values
     *
     * @return array|null
     */
    public function prepareOptionValues($values = null)
    {
        if ($values) {
            if (!is_array($values)) {
                $values = [$values];
            }
            // fix duplicate, empty option values
            $values = array_map('trim', $values);
            $values = array_keys(array_flip($values));
            $values = array_diff($values, ['']);
            //sort($values);

            if (empty($values)) {
                $values = null;
            }
        }

        return $values;
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

            $c->groupby('msOption.id');
            $c->select('msOption.key');

            $this->optionKeys = $c->prepare() && $c->stmt->execute()
                ? $c->stmt->fetchAll(PDO::FETCH_COLUMN)
                : [];
        }

        return $this->optionKeys;
    }

    /**
     * @return array
     */
    public function getOptionFields()
    {
        $fields = [];
        /** @var xPDOQuery $c */
        $c = $this->prepareOptionListCriteria();

        $c->select([
            $this->xpdo->getSelectColumns('msOption', 'msOption'),
            $this->xpdo->getSelectColumns(
                'msCategoryOption',
                'msCategoryOption',
                '',
                ['id', 'option_id', 'category_id'],
                true
            ),
            'Category.category AS category_name',
        ]);

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
    public function remove(array $ancestors = [])
    {
        $this->xpdo->removeCollection('msProductOption', ['product_id' => $this->id]);
        $this->xpdo->removeCollection('msCategoryMember', ['product_id' => $this->id]);
        $this->xpdo->removeCollection('msProductLink', ['master' => $this->id, 'OR:slave:=' => $this->id]);

        $files = $this->getMany('Files', ['parent' => 0]);
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
        $files = $this->xpdo->getIterator('msProductFile', [
            'type' => 'image',
            'parent' => 0,
        ]);

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
        if ($this->mediaSource = $this->xpdo->getObject('sources.modMediaSource', ['id' => $this->get('source')])) {
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
        // Check if you need to update files ranks
        $c = $this->xpdo->newQuery('msProductFile', [
            'product_id' => $this->get('id'),
            'parent' => 0,
        ]);
        $c->select('MAX(`rank`) + 1 as max');
        $c->select('COUNT(id) as total');
        $c->having('max <> total');
        if ($c->prepare() && $c->stmt->execute()) {
            if (!$c->stmt->rowCount()) {
                return;
            }
        }

        // Update ranks
        $c = $this->xpdo->newQuery('msProductFile', [
            'product_id' => $this->get('id'),
            'parent' => 0,
        ]);
        $c->select('id');
        $c->sortby('`rank` ASC, createdon', 'ASC');

        if ($c->prepare() && $c->stmt->execute()) {
            $table = $this->xpdo->getTableName('msProductFile');
            $update = $this->xpdo->prepare("UPDATE {$table} SET `rank` = ? WHERE (id = ? OR parent = ?)");
            $ids = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($ids as $k => $id) {
                $update->execute([$k, $id, $id]);
            }

            $alter = $this->xpdo->prepare("ALTER TABLE {$table} ORDER BY `rank` ASC");
            $alter->execute();
        }
    }

    /**
     * @return bool|mixed
     */
    public function updateProductImage()
    {
        $this->rankProductImages();
        $c = $this->xpdo->newQuery('msProductFile', [
            'product_id' => $this->id,
            'parent' => 0,
            'type' => 'image',
            //'active' => true,
        ]);
        $c->sortby('`rank`', 'ASC');
        $c->limit(1);
        /** @var msProductFile $file */
        $file = $this->xpdo->getObject('msProductFile', $c);
        if ($file) {
            $thumb = $file->getFirstThumbnail();
            $arr = [
                'image' => $file->get('url'),
                'thumb' => !empty($thumb['url'])
                    ? $thumb['url']
                    : '',
            ];
        } else {
            $arr = [
                'image' => null,
                'thumb' => null,
            ];
        }

        $this->fromArray($arr);
        if (parent::save()) {
            /** @var msProduct $product */
            if ($product = $this->getOne('Product')) {
                $product->clearCache();
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
            $array = [];
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
                    $c = $this->xpdo->newQuery('msCategoryMember', ['product_id' => $this->id]);
                    $c->select('category_id');
                    if ($c->prepare() && $c->stmt->execute()) {
                        $value = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
                    }
                    break;
                case 'options':
                    $c = $this->xpdo->newQuery('msProductOption', ['product_id' => $this->id]);
                    $c->select('key,value');
                    $c->sortby('value');
                    if ($c->prepare() && $c->stmt->execute()) {
                        $value = [];
                        while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isset($value[$row['key']])) {
                                $value[$row['key']][] = $row['value'];
                            } else {
                                $value[$row['key']] = [$row['value']];
                            }
                        }
                    }
                    break;
                case 'links':
                    $value = ['master' => [], 'slave' => []];
                    $c = $this->xpdo->newQuery('msProductLink', ['master' => $this->id]);
                    $c->select('link,slave');
                    if ($c->prepare() && $c->stmt->execute()) {
                        while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isset($value['master'][$row['link']])) {
                                $value['master'][$row['link']][] = $row['slave'];
                            } else {
                                $value['master'][$row['link']] = [$row['slave']];
                            }
                        }
                    }

                    $c = $this->xpdo->newQuery('msProductLink', ['slave' => $this->id]);
                    $c->select('link,master');
                    if ($c->prepare() && $c->stmt->execute()) {
                        while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isset($value['slave'][$row['link']])) {
                                $value['slave'][$row['link']][] = $row['master'];
                            } else {
                                $value['slave'][$row['link']] = [$row['master']];
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
    public function getPrice($data = [])
    {
        $price = parent::get('price');
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $this->xpdo->getService('miniShop2')) {
            if (empty($data)) {
                $data = $this->toArray();
            }
            $params = [
                'product' => $this,
                'data' => $data,
                'price' => $price,
            ];
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
    public function getWeight($data = [])
    {
        $weight = parent::get('weight');
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $this->xpdo->getService('miniShop2')) {
            if (empty($data)) {
                $data = $this->toArray();
            }
            $params = [
                'product' => $this,
                'data' => $data,
                'weight' => $weight,
            ];
            $response = $miniShop2->invokeEvent('msOnGetProductWeight', $params);
            if ($response['success']) {
                $weight = $params['weight'] = $response['data']['weight'];
            }
        }

        return $weight;
    }

    /* Returns prepared product fields.
    *
    * @return array $result Prepared fields of product.
    * */
    public function modifyFields($data = [])
    {
        if (empty($data)) {
            $data = $this->toArray();
        }
        /** @var miniShop2 $miniShop2 */
        $miniShop2 = $this->xpdo->getService('minishop2');
        $params = [
            'product' => $this,
            'data' => $data,
        ];
        $response = $miniShop2->invokeEvent('msOnGetProductFields', $params);
        if ($response['success']) {
            unset($response['data']['product']);
            $data = array_merge($data, $response['data']);
        }

        return $data;
    }
}
