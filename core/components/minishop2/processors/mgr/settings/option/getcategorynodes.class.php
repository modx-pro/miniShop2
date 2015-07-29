<?php

require_once MODX_CORE_PATH.'model/modx/processors/resource/getnodes.class.php';

class msOptionCategoryGetNodesProcessor  extends modResourceGetNodesProcessor {
    protected $pid;
    protected $parent_id;

    /** @var null|msOption  */
    public $option = null;
    public $optionCategories = null;

    protected $map = array();

    /** {@inheritDoc} */
    public function initialize() {
        $initialize = parent::initialize();
        $this->pid = $this->getProperty('currentResource');
        if ($res = $this->modx->getObject('msProduct', $this->pid)) {
            $this->parent_id = $res->get('parent');
        }

        $this->prepareMap();

        return $initialize;
    }

    public function prepareMap() {
        /** @TODO добавить кеширование карты */
        $q = $this->modx->newQuery('msCategory');
        $q = $this->modx->addDerivativeCriteria('msCategory', $q);
        $q->select('id');
        $ids = array();
        if ($q->prepare() && $q->stmt->execute()){
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        foreach ($ids as $cat) {
            $this->map[] = $cat;
            /** @var modResource $parent */
            $parent = $this->modx->getObject('modResource', $cat);
            while($parent = $parent->getOne('Parent')) {
                if (in_array($parent->get('id'), $this->map)) {
                    break;
                }
                $this->map[] = $parent->get('id');
            }
        }
        $this->map = array_unique($this->map);

    }

    public function prepare() {
        $this->option = $this->modx->getObject('msOption', $this->getProperty('option', 0));
        $this->optionCategories = $this->option ? $this->option->getMany('OptionCategories') : array();
        parent::prepare();
    }


    /** {@inheritDoc} */
    public function getResourceQuery() {
        $resourceColumns = array(
            'id'
        ,'pagetitle'
        ,'longtitle'
        ,'alias'
        ,'description'
        ,'parent'
        ,'published'
        ,'deleted'
        ,'isfolder'
        ,'menuindex'
        ,'menutitle'
        ,'hidemenu'
        ,'class_key'
        ,'context_key'
        );
        $this->itemClass= 'modResource';
        $c= $this->modx->newQuery($this->itemClass);
        $c->leftJoin('modResource', 'Child', array('modResource.id = Child.parent'));
     //   $c->leftJoin('msCategoryMember', 'Member', array('modResource.id = Member.category_id AND Member.product_id = '.$this->pid));
        $c->select($this->modx->getSelectColumns('modResource', 'modResource', '', $resourceColumns));
        $c->select(array(
            'childrenCount' => 'COUNT(Child.id)',
     //       'member' => 'category_id'
        ));
        $c->where(array(
            'context_key' => $this->contextKey
          //  ,'show_in_tree' => true
         //   ,'isfolder' => true
        ));
        if (empty($this->startNode) && !empty($this->defaultRootId)) {
            $c->where(array(
                'id:IN' => explode(',', $this->defaultRootId),
                'parent:NOT IN' => explode(',', $this->defaultRootId),
            ));
        } else {
            $c->where(array(
                'parent' => $this->startNode,
            ));
        }
        $c->groupby($this->modx->getSelectColumns('modResource', 'modResource', '', $resourceColumns), '');
        $c->sortby('modResource.'.$this->getProperty('sortBy'),$this->getProperty('sortDir'));
        $c->prepare();
        return $c;
    }


    /** {@inheritDoc} */
    public function prepareContextNode(modContext $context) {
        $context->prepare();
        return array(
            'text' => $context->get('key')
            ,'id' => $context->get('key') . '_0'
            ,'pk' => $context->get('key')
            ,'ctx' => $context->get('key')
            ,'leaf' => false
            ,'cls' => 'icon-context'
            ,'iconCls' => $this->modx->getOption('mgr_tree_icon_context', null, 'tree-context')
            ,'qtip' => $context->get('description') != '' ? strip_tags($context->get('description')) : ''
            ,'type' => 'modContext'
        );
    }


    /** {@inheritDoc} */
    public function prepareResourceNode(modResource $resource) {

        // show only categories and their parents
        if (!in_array($resource->get('id'), $this->map)) {
            return false;
        }

        $qtipField = $this->getProperty('qtipField');
        $nodeField = $this->getProperty('nodeField');

        //$hasChildren = (int)$resource->get('childrenCount') > 0 && $resource->get('hide_children_in_tree') == 0 ? true : false;
        $hasChildren = (int)$resource->get('childrenCount') > 0;
        //show only categories or not empty folders
        if (!$hasChildren && !($resource instanceof msCategory)) {
            return false;
        }

        // Assign an icon class based on the class_key
        $class = $iconCls = array();
        $classKey = strtolower($resource->get('class_key'));
        if (substr($classKey, 0, 3) == 'mod') {
            $classKey = substr($classKey, 3);
        }
        $classKeyIcon = $this->modx->getOption('mgr_tree_icon_' . $classKey, null, 'tree-resource');
        $iconCls[] = $classKeyIcon;

        $class[] = 'icon-'.strtolower(str_replace('mod','',$resource->get('class_key')));
        if (!$resource->isfolder) {
            $class[] = 'x-tree-node-leaf icon-resource';
        }
        if (!$resource->get('published')) $class[] = 'unpublished';
        if ($resource->get('deleted')) $class[] = 'deleted';
        if ($resource->get('hidemenu')) $class[] = 'hidemenu';
        if ($hasChildren) {
            $class[] = 'haschildren';
            $iconCls[] = $this->modx->getOption('mgr_tree_icon_folder', null, 'tree-folder');
            $iconCls[] = 'parent-resource';
        }

        $qtip = '';
        if (!empty($qtipField)) {
            $qtip = '<b>'.strip_tags($resource->$qtipField).'</b>';
        } else {
            if ($resource->longtitle != '') {
                $qtip = '<b>'.strip_tags($resource->longtitle).'</b><br />';
            }
            if ($resource->description != '') {
                $qtip = '<i>'.strip_tags($resource->description).'</i>';
            }
        }

        $idNote = $this->modx->hasPermission('tree_show_resource_ids') ? ' <span dir="ltr">('.$resource->id.')</span>' : '';
        $itemArray = array(
            'text' => strip_tags($resource->$nodeField).$idNote,
            'id' => $resource->context_key . '_'.$resource->id,
            'pk' => $resource->id,
            'cls' => implode(' ',$class),
            'iconCls' => implode(' ',$iconCls),
            'type' => 'modResource',
            'classKey' => $resource->class_key,
            'ctx' => $resource->context_key,
            'hide_children_in_tree' => $resource->hide_children_in_tree,
            'qtip' => $qtip,
            'checked' => $this->getChecked($resource),//!empty($resource->member) || $resource->id == $this->parent_id ? true : false,
            'disabled' =>  $resource->id == $this->parent_id ? true : false
        );
        if (!$hasChildren) {
            $itemArray['hasChildren'] = false;
            $itemArray['children'] = array();
            $itemArray['expanded'] = true;
        } else {
            $itemArray['hasChildren'] = true;
        }

        if ($itemArray['classKey'] != 'msCategory') {
            unset($itemArray['checked']);
        }

        return $itemArray;
    }

    /**
     * @param modResource $resource
     * @return bool
     */
    public function getChecked($resource) {
        /** @var modResource $cat */
        foreach ($this->optionCategories as $key => $cat) {
            if ($resource->get('id') == $cat->get('category_id')) {
                return true;
            }
        }
        return false;
    }

}

return 'msOptionCategoryGetNodesProcessor';