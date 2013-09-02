<?php

require_once MODX_CORE_PATH.'components/minishop2/processors/mgr/category/create.class.php';
require_once MODX_CORE_PATH.'components/minishop2/processors/mgr/category/update.class.php';


class msCategory extends modResource {
	public $showInContextMenu = true;
	public $allowChildrenResources = false;


	/**
	 * {@inheritDoc}
	 * @return object|null
	 */
	public static function load(xPDO & $xpdo, $className, $criteria= null, $cacheFlag= true){
		if (!is_object($criteria)) {
			$criteria= $xpdo->getCriteria($className, $criteria, $cacheFlag);
		}
		$xpdo->addDerivativeCriteria($className, $criteria);
		return parent::load($xpdo, $className, $criteria, $cacheFlag);
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
	 * {@inheritDoc}
	 * @return mixed
	 */
	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'controllers/category/';
	}


	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public function getContextMenuText() {
		$this->xpdo->lexicon->load('minishop2:default');
		return array(
			'text_create' => $this->xpdo->lexicon('ms2_category'),
			'text_create_here' => $this->xpdo->lexicon('ms2_category_create_here'),
		);
	}


	/**
	 * {@inheritDoc}
	 * @return string
	 */
	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('minishop2:default');
		return $this->xpdo->lexicon('ms2_category_type');
	}


	/**
	 * @param array $node
	 * @return array
	 */
	public function prepareTreeNode(array $node = array()) {
		$this->xpdo->lexicon->load('minishop2:default');
		$menu = array();

		$idNote = $this->xpdo->hasPermission('tree_show_resource_ids') ? ' <span dir="ltr">('.$this->id.')</span>' : '';
		$menu[] = array(
			'text' => '<b>'.$this->get('pagetitle').'</b>'.$idNote,
			'handler' => 'Ext.emptyFn',
		);
		$menu[] = '-';
		$menu[] = array(
			'text' => $this->xpdo->lexicon('ms2_category_manage'),
			'handler' => 'this.editResource',
		);
		$menu[] = array(
			'text' => $this->xpdo->lexicon('create')
			,'handler' => 'Ext.emptyFn'
			,'menu' => array('items' => array(
				array(
					'text' => $this->xpdo->lexicon('ms2_product_create_here')
					,'handler' => 'function(itm,e) { var tree = Ext.getCmp("modx-resource-tree"); itm.classKey = "msProduct"; tree.createResourceHere(itm,e); }'
				)
				,array(
					'text' => $this->xpdo->lexicon('ms2_category_create_here')
					,'handler' => 'function(itm,e) { var tree = Ext.getCmp("modx-resource-tree"); itm.classKey = "msCategory"; tree.createResourceHere(itm,e); }'
				)
			))
		);

		$menu[] = '-';
		$menu[] = array(
			'text' => $this->xpdo->lexicon('ms2_category_duplicate'),
			'handler' => 'function(itm,e) {itm.classKey = "msCategory"; this.duplicateResource(itm,e); }',
		);

		if ($this->get('published')) {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('ms2_category_unpublish'),
				'handler' => 'this.unpublishDocument',
			);
		} else {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('ms2_category_publish'),
				'handler' => 'this.publishDocument',
			);
		}
		if ($this->get('deleted')) {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('ms2_category_undelete'),
				'handler' => 'this.undeleteDocument',
			);
		} else {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('ms2_category_delete'),
				'handler' => 'this.deleteDocument',
			);

		}
		$menu[] = '-';
		$menu[] = array(
			'text' => $this->xpdo->lexicon('ms2_category_view'),
			'handler' => 'this.preview',
		);

		$node['menu'] = array('items' => $menu);
		$node['hasChildren'] = true;
		return $node;
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

		$q = $this->xpdo->newQuery('msCategory', array('parent' => $this->parent, 'class_key' => 'msCategory'));
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
}