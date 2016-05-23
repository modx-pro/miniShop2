<?php
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/category/create.class.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/category/update.class.php';

class msCategory extends modResource
{
    public $showInContextMenu = true;


    /**
     * msCategory constructor.
     *
     * @param xPDO $xpdo
     */
    function __construct(xPDO & $xpdo)
    {
        parent:: __construct($xpdo);
        $this->set('class_key', 'msCategory');
    }


    /**
     * @param xPDO $xpdo
     * @param string $className
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return modAccessibleObject|null|object
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
            $modx->getOption('core_path') . 'components/minishop2/') . 'controllers/category/';
    }


    /**
     * @return array
     */
    public function getContextMenuText()
    {
        $this->xpdo->lexicon->load('minishop2:default');

        return array(
            'text_create' => $this->xpdo->lexicon('ms2_category'),
            'text_create_here' => $this->xpdo->lexicon('ms2_category_create_here'),
        );
    }


    /**
     * @return null|string
     */
    public function getResourceTypeName()
    {
        $this->xpdo->lexicon->load('minishop2:default');

        return $this->xpdo->lexicon('ms2_category_type');
    }


    /**
     * @param array $node
     *
     * @return array
     */
    public function prepareTreeNode(array $node = array())
    {
        $classes = array_map('trim', explode(' ', $node['cls']));
        $remove = array('pnew_modStaticResource', 'pnew_modSymLink', 'pnew_modWebLink', 'pnew_modDocument');
        $node['cls'] = implode(' ', array_diff($classes, $remove));
        $node['hasChildren'] = true;
        $node['expanded'] = false;

        return $node;
    }


    /**
     * @param array $options
     *
     * @return mixed
     */
    public function duplicate(array $options = array())
    {
        $category = parent::duplicate($options);

        $options = $this->getMany('CategoryOptions');
        /** @var msCategoryOption $option */
        foreach ($options as $option) {
            $option->set('category_id', $category->get('id'));

            /** @var msCategoryOption $new */
            $new = $this->xpdo->newObject('msCategoryOption');
            $new->fromArray($option->toArray(), '', true, true);
            $new->save();
        }

        return $category;
    }


    /**
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        if (!$this->isNew() && parent::get('class_key') != 'msCategory') {
            parent::set('hide_children_in_tree', false);
            // Show children
            $c = $this->xpdo->newQuery('msProduct');
            $c->command('UPDATE');
            $c->where(array(
                'parent' => $this->id,
                'class_key' => 'msProduct',
            ));
            $c->set(array(
                'show_in_tree' => true,
            ));
            $c->prepare();
            $c->stmt->execute();
        }

        return parent::save($cacheFlag);
    }


    /**
     * Returns array with all neighborhood products
     *
     * @return array $arr Array with neighborhood from left and right
     */
    public function getNeighborhood()
    {
        $arr = array();

        $c = $this->xpdo->newQuery('msCategory', array('parent' => $this->parent, 'class_key' => 'msCategory'));
        $c->sortby('menuindex', 'ASC');
        $c->select('id');
        if ($c->prepare() && $c->stmt->execute()) {
            $ids = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
            $current = array_search($this->id, $ids);

            $right = $left = array();
            foreach ($ids as $k => $v) {
                if ($k > $current) {
                    $right[] = $v;
                } else {
                    if ($k < $current) {
                        $left[] = $v;
                    }
                }
            }

            $arr = array(
                'left' => array_reverse($left),
                'right' => $right,
            );
        }

        return $arr;
    }
}