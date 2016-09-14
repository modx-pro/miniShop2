<?php

class msProductGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msProduct';
    public $languageTopics = array('default', 'minishop2:product');
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';
    public $parent = 0;
    protected $item_id = 0;


    /**
     * @return bool
     */
    public function initialize()
    {
        if ($this->getProperty('combo') && !$this->getProperty('limit') && $id = (int)$this->getProperty('id')) {
            $this->item_id = $id;
        }
        if (!$this->getProperty('limit')) {
            $this->setProperty('limit', 20);
        }
        if ($this->getProperty('sort') == 'menuindex') {
            $this->setProperty('sort', 'parent ' . $this->getProperty('dir') . ', menuindex');
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
        $c->where(array('class_key' => 'msProduct'));
        $c->leftJoin('msProductData', 'Data', 'msProduct.id = Data.id');
        $c->leftJoin('msCategoryMember', 'Member', 'msProduct.id = Member.product_id');
        $c->leftJoin('msVendor', 'Vendor', 'Data.vendor = Vendor.id');
        $c->leftJoin('msCategory', 'Category', 'Category.id = msProduct.parent');
        if ($this->getProperty('combo')) {
            $c->select('msProduct.id,msProduct.pagetitle,msProduct.context_key');
        } else {
            $c->select($this->modx->getSelectColumns('msProduct', 'msProduct'));
            $c->select($this->modx->getSelectColumns('msProductData', 'Data', '', array('id'), true));
            $c->select($this->modx->getSelectColumns('msVendor', 'Vendor', 'vendor_', array('name')));
            $c->select($this->modx->getSelectColumns('msCategory', 'Category', 'category_', array('pagetitle')));
        }
        if ($this->item_id) {
            $c->where(array('msProduct.id' => $this->item_id));
            if ($parent = (int)$this->getProperty('parent')) {
                $this->parent = $parent;
            }
        } else {
            $query = trim($this->getProperty('query'));
            if (!empty($query)) {
                if (is_numeric($query)) {
                    $c->where(array(
                        'msProduct.id' => $query,
                        'OR:Data.article:=' => $query,
                    ));
                } else {
                    $c->where(array(
                        'msProduct.pagetitle:LIKE' => "%{$query}%",
                        'OR:msProduct.longtitle:LIKE' => "%{$query}%",
                        'OR:msProduct.description:LIKE' => "%{$query}%",
                        'OR:msProduct.introtext:LIKE' => "%{$query}%",
                        'OR:Data.article:LIKE' => "%{$query}%",
                        'OR:Data.made_in:LIKE' => "%{$query}%",
                        'OR:Vendor.name:LIKE' => "%{$query}%",
                        'OR:Category.pagetitle:LIKE' => "%{$query}%",
                    ));
                }
            }

            $parent = (int)$this->getProperty('parent');
            if (!empty($parent)) {
                $category = $this->modx->getObject('modResource', $this->getProperty('parent'));
                $this->parent = $parent;
                $parents = array($parent);

                $nested = $this->getProperty('nested', null);
                $nested = $nested === null && $this->modx->getOption('ms2_category_show_nested_products', null, true)
                    ? true
                    : (bool)$nested;
                if ($nested) {
                    $tmp = $this->modx->getChildIds($parent, 10, array('context' => $category->get('context_key')));
                    foreach ($tmp as $v) {
                        $parents[] = $v;
                    }
                }
                $c->orCondition(array('parent:IN' => $parents, 'Member.category_id:IN' => $parents), '', 1);
            }
        }

        return $c;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $c->groupby($this->classKey . '.id');

        return $c;
    }


    /**
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        if ($c->prepare() && $c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }


    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            $this->currentIndex++;
        }
        $list = $this->afterIteration($list);

        return $list;
    }


    /**
     * @param array $array
     *
     * @return array
     */
    public function prepareArray(array $array)
    {
        if ($this->getProperty('combo')) {
            $array['parents'] = array();
            $parents = $this->modx->getParentIds($array['id'], 2, array(
                'context' => $array['context_key'],
            ));
            if (empty($parents[count($parents) - 1])) {
                unset($parents[count($parents) - 1]);
            }
            if (!empty($parents) && is_array($parents)) {
                $q = $this->modx->newQuery('msCategory', array('id:IN' => $parents));
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

            $array['actions'] = array();

            // View
            if (!empty($array['preview_url'])) {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-eye',
                    'title' => $this->modx->lexicon('ms2_product_view'),
                    'action' => 'viewProduct',
                    'button' => true,
                    'menu' => true,
                );
            }
            // Edit
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('ms2_product_edit'),
                'action' => 'editProduct',
                'button' => false,
                'menu' => true,
            );
            // Duplicate
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-files-o',
                'title' => $this->modx->lexicon('ms2_product_duplicate'),
                'action' => 'duplicateProduct',
                'button' => false,
                'menu' => true,
            );
            // Publish
            if (!$array['published']) {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-green',
                    'title' => $this->modx->lexicon('ms2_product_publish'),
                    'multiple' => $this->modx->lexicon('ms2_product_publish'),
                    'action' => 'publishProduct',
                    'button' => true,
                    'menu' => true,
                );
            } else {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-gray',
                    'title' => $this->modx->lexicon('ms2_product_unpublish'),
                    'multiple' => $this->modx->lexicon('ms2_product_unpublish'),
                    'action' => 'unpublishProduct',
                    'button' => true,
                    'menu' => true,
                );
            }
            // Show in tree
            if (!$array['show_in_tree']) {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-plus',
                    'title' => $this->modx->lexicon('ms2_product_show_in_tree'),
                    'multiple' => $this->modx->lexicon('ms2_product_show_in_tree'),
                    'action' => 'showProduct',
                    'button' => false,
                    'menu' => true,
                );
            } else {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-minus',
                    'title' => $this->modx->lexicon('ms2_product_hide_in_tree'),
                    'multiple' => $this->modx->lexicon('ms2_product_hide_in_tree'),
                    'action' => 'hideProduct',
                    'button' => false,
                    'menu' => true,
                );
            }
            // Delete
            if (!$array['deleted']) {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-trash-o action-red',
                    'title' => $this->modx->lexicon('ms2_product_delete'),
                    'multiple' => $this->modx->lexicon('ms2_product_delete'),
                    'action' => 'deleteProduct',
                    'button' => false,
                    'menu' => true,
                );
            } else {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-undo action-green',
                    'title' => $this->modx->lexicon('ms2_product_undelete'),
                    'multiple' => $this->modx->lexicon('ms2_product_undelete'),
                    'action' => 'undeleteProduct',
                    'button' => true,
                    'menu' => true,
                );
            }
            // Menu
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-cog actions-menu',
                'menu' => false,
                'button' => true,
                'action' => 'showMenu',
                'type' => 'menu',
            );
        }

        return $array;
    }

}

return 'msProductGetListProcessor';