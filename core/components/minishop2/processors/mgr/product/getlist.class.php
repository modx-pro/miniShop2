<?php

class msProductGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msProduct';
    public $languageTopics = ['default', 'minishop2:product'];
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';
    public $parent = 0;

    protected $item_id = 0;
    protected $options = [];

    /**
     * @return bool
     */
    public function initialize()
    {
        if ($this->getProperty('combo') && !$this->getProperty('limit') && $id = (int)$this->getProperty('id')) {
            $this->item_id = $id;
        } else {
            $showOptions = (bool)$this->modx->getOption('ms2_category_show_options', null, true);
            if ($showOptions) {
                $grid_fields = $this->modx->getOption('ms2_category_grid_fields');
                $grid_fields = array_map('trim', explode(',', $grid_fields));
                $this->options = $this->modx->getIterator('msOption', ['key:IN' => $grid_fields]);
            }
        }
        if (!$this->getProperty('limit')) {
            $this->setProperty('limit', 20);
        }
        if ($this->getProperty('sort') === 'menuindex') {
            $this->setProperty('sort', 'msProduct.parent ' . $this->getProperty('dir') . ', msProduct.menuindex');
        }

        return parent::initialize();
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->where(['class_key' => 'msProduct']);
        $c->leftJoin('msProductData', 'Data', 'msProduct.id = Data.id');
        $c->leftJoin('msCategoryMember', 'Member', 'msProduct.id = Member.product_id');
        $c->leftJoin('msVendor', 'Vendor', 'Data.vendor = Vendor.id');
        $c->leftJoin('msCategory', 'Category', 'Category.id = msProduct.parent');
        if ($this->getProperty('combo')) {
            $c->select('msProduct.id,msProduct.pagetitle,msProduct.context_key');
        } else {
            $c->select($this->modx->getSelectColumns('msProduct', 'msProduct'));
            $c->select($this->modx->getSelectColumns('msProductData', 'Data', '', ['id'], true));
            $c->select($this->modx->getSelectColumns('msVendor', 'Vendor', 'vendor_', ['name']));
            $c->select($this->modx->getSelectColumns('msCategory', 'Category', 'category_', ['pagetitle']));
        }
        if ($this->item_id) {
            $c->where(['msProduct.id' => $this->item_id]);
            if ($parent = (int)$this->getProperty('parent')) {
                $this->parent = $parent;
            }
        } else {
            $query = trim($this->getProperty('query'));
            if (!empty($query)) {
                if (is_numeric($query)) {
                    $c->where([
                        'msProduct.id' => $query,
                        'OR:Data.article:=' => $query,
                    ]);
                } else {
                    $c->where([
                        'msProduct.pagetitle:LIKE' => "%{$query}%",
                        'OR:msProduct.longtitle:LIKE' => "%{$query}%",
                        'OR:msProduct.description:LIKE' => "%{$query}%",
                        'OR:msProduct.introtext:LIKE' => "%{$query}%",
                        'OR:Data.article:LIKE' => "%{$query}%",
                        'OR:Data.made_in:LIKE' => "%{$query}%",
                        'OR:Vendor.name:LIKE' => "%{$query}%",
                        'OR:Category.pagetitle:LIKE' => "%{$query}%",
                    ]);
                }
            }

            $parent = (int)$this->getProperty('parent');
            if (!empty($parent)) {
                $category = $this->modx->getObject('modResource', $parent);
                $this->parent = $parent;
                $parents = [$parent];

                $nested = $this->getProperty('nested', null);
                $nested = ($nested === null) && $this->modx->getOption(
                    'ms2_category_show_nested_products',
                    null,
                    true
                ) || (bool)$nested;
                if ($nested) {
                    $tmp = $this->modx->getChildIds($parent, 10, ['context' => $category->get('context_key')]);
                    foreach ($tmp as $v) {
                        $parents[] = $v;
                    }
                }
                $parents = '(' . implode(',', $parents) . ')';
                $c->query['where'][] = [
                    [
                        new xPDOQueryCondition(['sql' => 'msProduct.parent IN ' . $parents, 'conjunction' => 'OR']),
                        new xPDOQueryCondition(['sql' => 'Member.category_id IN ' . $parents, 'conjunction' => 'OR'])
                    ]
                ];
            }
        }

        $c->groupby($this->classKey . '.id');

        return $c;
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $total = 0;
        $limit = (int)$this->getProperty('limit');
        $start = (int)$this->getProperty('start');

        $q = clone $c;
        $q->query['columns'] = ['SQL_CALC_FOUND_ROWS msProduct.id'];
        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns(
            $sortClassKey,
            $this->getProperty('sortAlias', $sortClassKey),
            '',
            [$this->getProperty('sort')]
        );
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $q->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $q->limit($limit, $start);
        }

        $ids = [];
        if ($q->prepare() and $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $total = $this->modx->query('SELECT FOUND_ROWS()')->fetchColumn();
        }
        $ids = empty($ids) ? '(0)' : '(' . implode(',', $ids) . ')';
        $c->query['where'] = [
            [
                new xPDOQueryCondition(['sql' => 'msProduct.id IN ' . $ids, 'conjunction' => 'AND']),
            ]
        ];
        $c->sortby($sortKey, $this->getProperty('dir'));

        $this->setProperty('total', $total);

        return $c;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $c = $this->prepareQueryAfterCount($c);
        return [
            'results' => ($c->prepare() and $c->stmt->execute()) ? $c->stmt->fetchAll(PDO::FETCH_ASSOC) : [],
            'total' => (int)$this->getProperty('total'),
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = [];
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            $this->currentIndex++;
        }
        return $this->afterIteration($list);
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function prepareArray(array $array)
    {
        if ($this->getProperty('combo')) {
            $array['parents'] = [];
            $parents = $this->modx->getParentIds($array['id'], 2, [
                'context' => $array['context_key'],
            ]);
            if (empty($parents[count($parents) - 1])) {
                unset($parents[count($parents) - 1]);
            }
            if (!empty($parents) && is_array($parents)) {
                $q = $this->modx->newQuery('msCategory', ['id:IN' => $parents]);
                $q->select('id,pagetitle');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $key = array_search($row['id'], $parents);
                        if ($key !== false) {
                            $parents[$key] = $row;
                        }
                    }
                }
                $array['parents'] = array_reverse($parents);
            }
        } else {
            if ($array['parent'] != $this->parent) {
                $array['cls'] = 'multicategory';
                $array['category_name'] = $array['category_pagetitle'];
            } else {
                $array['cls'] = $array['category_name'] = '';
            }

            $array['price'] = round($array['price'], 2);
            $array['old_price'] = round($array['old_price'], 2);
            $array['weight'] = round($array['weight'], 3);

            $this->modx->getContext($array['context_key']);
            $array['preview_url'] = $this->modx->makeUrl($array['id'], $array['context_key']);

            // Options
            if ($this->options) {
                $this->options->rewind();
                if ($this->options->valid()) {
                    /** @var msOption $option */
                    foreach ($this->options as $option) {
                        $array['options-' . $option->get('key')] = $option->getRowValue($array['id']);
                    }
                }
            }

            $array['actions'] = [];

            // View
            if (!empty($array['preview_url'])) {
                $array['actions'][] = [
                    'cls' => '',
                    'icon' => 'icon icon-eye',
                    'title' => $this->modx->lexicon('ms2_product_view'),
                    'action' => 'viewProduct',
                    'button' => true,
                    'menu' => true,
                ];
            }
            //Regenerate image
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-refresh',
                'title' => $this->modx->lexicon('ms2_gallery_file_generate_thumbs'),
                'multiple' => $this->modx->lexicon('ms2_gallery_file_generate_thumbs'),
                'action' => 'generatePreview',
                'button' => true,
                'menu' => true,
            ];
            // Edit
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('ms2_product_edit'),
                'action' => 'editProduct',
                'button' => false,
                'menu' => true,
            ];
            // Duplicate
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-files-o',
                'title' => $this->modx->lexicon('ms2_product_duplicate'),
                'action' => 'duplicateProduct',
                'button' => false,
                'menu' => true,
            ];
            // Publish
            if (!$array['published']) {
                $array['actions'][] = [
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-green',
                    'title' => $this->modx->lexicon('ms2_product_publish'),
                    'multiple' => $this->modx->lexicon('ms2_product_publish'),
                    'action' => 'publishProduct',
                    'button' => true,
                    'menu' => true,
                ];
            } else {
                $array['actions'][] = [
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-gray',
                    'title' => $this->modx->lexicon('ms2_product_unpublish'),
                    'multiple' => $this->modx->lexicon('ms2_product_unpublish'),
                    'action' => 'unpublishProduct',
                    'button' => true,
                    'menu' => true,
                ];
            }
            // Show in tree
            if (!$array['show_in_tree']) {
                $array['actions'][] = [
                    'cls' => '',
                    'icon' => 'icon icon-plus',
                    'title' => $this->modx->lexicon('ms2_product_show_in_tree'),
                    'multiple' => $this->modx->lexicon('ms2_product_show_in_tree'),
                    'action' => 'showProduct',
                    'button' => false,
                    'menu' => true,
                ];
            } else {
                $array['actions'][] = [
                    'cls' => '',
                    'icon' => 'icon icon-minus',
                    'title' => $this->modx->lexicon('ms2_product_hide_in_tree'),
                    'multiple' => $this->modx->lexicon('ms2_product_hide_in_tree'),
                    'action' => 'hideProduct',
                    'button' => false,
                    'menu' => true,
                ];
            }
            // Delete
            if (!$array['deleted']) {
                $array['actions'][] = [
                    'cls' => '',
                    'icon' => 'icon icon-trash-o action-red',
                    'title' => $this->modx->lexicon('ms2_product_delete'),
                    'multiple' => $this->modx->lexicon('ms2_product_delete'),
                    'action' => 'deleteProduct',
                    'button' => false,
                    'menu' => true,
                ];
            } else {
                $array['actions'][] = [
                    'cls' => '',
                    'icon' => 'icon icon-undo action-green',
                    'title' => $this->modx->lexicon('ms2_product_undelete'),
                    'multiple' => $this->modx->lexicon('ms2_product_undelete'),
                    'action' => 'undeleteProduct',
                    'button' => true,
                    'menu' => true,
                ];
            }
            // Menu
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-cog actions-menu',
                'menu' => false,
                'button' => true,
                'action' => 'showMenu',
                'type' => 'menu',
            ];
        }

        return $array;
    }
}

return 'msProductGetListProcessor';
