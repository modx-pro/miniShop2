<?php
/**
 * Get a list of Products
 *
 * @package minishop2
 * @subpackage processors
 */
class msProductGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msProduct';
	public $languageTopics = array('default','minishop2:product');
	public $defaultSortField = 'menuindex';
	public $defaultSortDirection  = 'ASC';
	public $parent = 0;


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->getProperty('limit')) {$this->setProperty('limit', 20);}

		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array('class_key' => 'msProduct'));
		$c->leftJoin('msProductData','Data', 'msProduct.id = Data.id');
		$c->leftJoin('msCategoryMember','Member', 'msProduct.id = Member.product_id');
		$c->leftJoin('msVendor','Vendor', 'Data.vendor = Vendor.id');
		$c->leftJoin('msCategory','Category', 'Category.id = msProduct.parent');
		if ($this->getProperty('combo')) {
			$c->select('msProduct.id,msProduct.pagetitle,msProduct.context_key');
		}
		else {
			$c->select($this->modx->getSelectColumns('msProduct','msProduct'));
			$c->select($this->modx->getSelectColumns('msProductData','Data', '', array('id'), true));
			$c->select($this->modx->getSelectColumns('msVendor','Vendor', 'vendor_', array('name')));
			$c->select($this->modx->getSelectColumns('msCategory','Category', 'category_', array('pagetitle')));
		}
		$c->select('Member.rank');
		if ($query = $this->getProperty('query',null)) {
			$queryWhere = array(
				'msProduct.id' => $query
				,'OR:msProduct.pagetitle:LIKE' => '%'.$query.'%'
				,'OR:description:LIKE' => '%'.$query.'%'
				,'OR:introtext:LIKE' => '%'.$query.'%'
				,'OR:Data.article:LIKE' =>  '%'.$query.'%'
				,'OR:Data.vendor:LIKE' =>  '%'.$query.'%'
				,'OR:Data.made_in:LIKE' =>  '%'.$query.'%'
				,'OR:Vendor.name:LIKE' =>  '%'.$query.'%'
				,'OR:Category.pagetitle:LIKE' =>  '%'.$query.'%'
			);
			$c->where($queryWhere);
		}
		$parent = $this->getProperty('parent');
		if (!empty($parent)) {
			$category = $this->modx->getObject('modResource', $this->getProperty('parent'));
			$this->parent = $parent;
			$parents = array($parent);
			if ($this->modx->getOption('ms2_category_show_nested_products', null, true)) {
				$tmp = $this->modx->getChildIds($parent, 10, array('context' => $category->get('context_key')));
				foreach ($tmp as $v) {
					$parents[] = $v;
				}
			}
			$c->orCondition(array('parent:IN' => $parents, 'Member.category_id:IN' => $parents), '', 1);
		}

		return $c;
	}


	/** {@inheritDoc} */
	public function prepareQueryAfterCount(xPDOQuery $c) {
		$c->groupby($this->classKey.'.id');
		return $c;
	}


	/** {@inheritDoc} */
	public function getData() {
		$data = array();
		$limit = intval($this->getProperty('limit'));
		$start = intval($this->getProperty('start'));

		/* query for chunks */
		$c = $this->modx->newQuery($this->classKey);
		$c = $this->prepareQueryBeforeCount($c);
		$data['total'] = $this->modx->getCount($this->classKey,$c);
		$c = $this->prepareQueryAfterCount($c);

		$sortClassKey = $this->getSortClassKey();
		$sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($this->getProperty('sort')));
		if (empty($sortKey)) $sortKey = $this->getProperty('sort');
		$c->sortby($sortKey,$this->getProperty('dir'));
		if ($limit > 0) {
			$c->limit($limit,$start);
		}

		if ($c->prepare() && $c->stmt->execute()) {
			$data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		return $data;
	}


	/** {@inheritDoc} */
	public function iterate(array $data) {
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


	/** {@inheritDoc} */
	public function prepareArray(array $resourceArray) {
		if ($this->getProperty('combo')) {
			$resourceArray['parents'] = array();
			$parents = $this->modx->getParentIds($resourceArray['id'], 2, array('context' => $resourceArray['context_key']));
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
				$resourceArray['parents'] = array_reverse($parents);
			}
		}
		else {
			if ($resourceArray['parent'] != $this->parent) {
				$resourceArray['cls'] = 'multicategory';
				$resourceArray['category_name'] = $resourceArray['category_pagetitle'];
			}
			else {
				$resourceArray['cls'] = $resourceArray['category_name'] = '';
			}

			$resourceArray['price'] = round($resourceArray['price'],2);
			$resourceArray['old_price'] = round($resourceArray['old_price'],2);
			$resourceArray['weight'] = round($resourceArray['weight'],3);

			$this->modx->getContext($resourceArray['context_key']);
			$resourceArray['preview_url'] = $this->modx->makeUrl($resourceArray['id'],$resourceArray['context_key']);

			$resourceArray['actions'] = array();

			$resourceArray['actions'][] = array(
				'className' => 'edit',
				'text' => $this->modx->lexicon('ms2_product_edit'),
			);

			$resourceArray['actions'][] = array(
				'className' => 'view',
				'text' => $this->modx->lexicon('ms2_product_view'),
			);
			if (!empty($resourceArray['deleted'])) {
				$resourceArray['actions'][] = array(
					'className' => 'undelete green',
					'text' => $this->modx->lexicon('ms2_product_undelete'),
				);
			} else {
				$resourceArray['actions'][] = array(
					'className' => 'delete',
					'text' => $this->modx->lexicon('ms2_product_delete'),
				);
			}
			if (!empty($resourceArray['published'])) {
				$resourceArray['actions'][] = array(
					'className' => 'unpublish',
					'text' => $this->modx->lexicon('ms2_product_unpublish'),
				);
			} else {
				$resourceArray['actions'][] = array(
					'className' => 'publish orange',
					'text' => $this->modx->lexicon('ms2_product_publish'),
				);
			}
		}

		return $resourceArray;
	}

}

return 'msProductGetListProcessor';