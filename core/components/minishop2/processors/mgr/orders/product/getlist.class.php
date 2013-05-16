<?php

class msProductGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msOrderProduct';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'ASC';
	public $languageTopics = array('minishop2:product');

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($order_id = $this->getProperty('order_id')) {
			$c->where(array('order_id' => $order_id));
		}

		$c->leftJoin('msProduct','msProduct', '`msOrderProduct`.`product_id` = `msProduct`.`id`');
		$c->leftJoin('msProductData','msProductData', '`msOrderProduct`.`product_id` = `msProductData`.`id`');
		$c->select(
			$this->modx->getSelectColumns('msOrderProduct','msOrderProduct')
			.', `msProduct`.`pagetitle`'
			.', `msProductData`.`article`'
		);
		if ($query = $this->getProperty('query',null)) {
			$c->where(array(
				'msProduct.pagetitle:LIKE' => '%'.$query.'%'
				,'OR:msProduct.description:LIKE' => '%'.$query.'%'
				,'OR:msProduct.introtext:LIKE' => '%'.$query.'%'
				,'OR:msProductData.article:LIKE' =>  '%'.$query.'%'
				,'OR:msProductData.vendor:LIKE' =>  '%'.$query.'%'
				,'OR:msProductData.made_in:LIKE' =>  '%'.$query.'%'
			));
		}

		return $c;
	}

}

return 'msProductGetListProcessor';