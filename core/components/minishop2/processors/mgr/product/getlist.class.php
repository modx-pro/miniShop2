<?php
/**
 * Get a list of Products
 *
 * @package minishop2
 * @subpackage processors
 */
class msProductGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msProduct';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $languageTopics = array('default','minishop2:product');
	public $renderers = '';
	/** @var modAction $editAction */
	public $editAction;
	public $parent = 0;

	public function initialize() {
		$this->editAction = $this->modx->getObject('modAction',array(
			'namespace' => 'core',
			'controller' => 'resource/update',
		));
		return parent::initialize();
	}

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin('msProductData','Data', 'msProduct.id = Data.id');
		$c->leftJoin('msCategoryMember','Category', 'msProduct.id = Category.product_id');
		if ($query = $this->getProperty('query',null)) {
			$queryWhere = array(
				'pagetitle:LIKE' => '%'.$query.'%'
				,'OR:description:LIKE' => '%'.$query.'%'
				,'OR:introtext:LIKE' => '%'.$query.'%'
				,'OR:Data.article:LIKE' =>  '%'.$query.'%'
				,'OR:Data.vendor:LIKE' =>  '%'.$query.'%'
				,'OR:Data.made_in:LIKE' =>  '%'.$query.'%'
			);
			$c->where($queryWhere);
		}
		$parent = $this->getProperty('parent');
		$c->where(array('class_key' => 'msProduct'));
		if (!empty($parent)) {
			$this->parent = $parent;
			$c->orCondition(array('parent' => $parent, 'Category.category_id' => $parent), '', 1);
		}

		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$resourceArray = parent::prepareRow($object);

		$resourceArray['cls'] = $object->get('parent') != $this->parent ? 'multicategory' : '';
		$resourceArray['action_edit'] = '?a='.$this->editAction->get('id').'&action=post/update&id='.$resourceArray['id'];

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

		return $resourceArray;
	}

}

return 'msProductGetListProcessor';