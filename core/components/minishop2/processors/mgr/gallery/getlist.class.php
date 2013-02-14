<?php
/**
 * Get a list of Products Files
 *
 * @package minishop2
 * @subpackage processors
 */
class msProductFileGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msProductFile';
	public $defaultSortField = 'rank';
	public $defaultSortDirection  = 'ASC';
	public $languageTopics = array('default','minishop2:product');

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array('product_id' => $this->getProperty('product_id')));

		$parent = $this->getProperty('parent');
		if ($parent !== false) {
			$c->where(array('parent' => $parent));
		}

		if ($type = $this->getProperty('type')) {
			$c->where(array('type' => $type));
		}

		return $c;
	}


	public function prepareRow(xPDOObject $object) {
		/* @var msProductFile $object */
		$row = $object->toArray();
		$tmp = $object->getFirstThumbnail();
		$row['thumbnail'] = !empty($tmp['url']) ? $tmp['url'] : MODX_ASSETS_URL . 'components/minishop2/img/mgr/ms2_small.png';
/*
		$row['menu'] = array();

		$row['menu'][] = array(
			'text' => $this->modx->lexicon('ms2_gallery_image_update'),
			'handler' => 'this.updateImage',
		);
		$row['menu'][] = array(
			'text' => $this->modx->lexicon('ms2_gallery_image_generate_thumbs'),
			'handler' => 'this.generateThumbnails',
		);
		$row['menu'][] = '-';
		$row['menu'][] = array(
			'text' => $this->modx->lexicon('ms2_gallery_image_update'),
			'handler' => 'this.deleteImage',
		);
*/
		return $row;
	}


}

return 'msProductFileGetListProcessor';