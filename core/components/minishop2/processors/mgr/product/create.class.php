<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';

class msProductCreateProcessor extends modResourceCreateProcessor {
	public $classKey = 'msProduct';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'msproduct_save';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';
	/* @var msProduct $object */
	public $object;


	/** {@inheritDoc} */
	public function prepareAlias() {
		if ($this->workingContext->getOption('ms2_product_id_as_alias')) {
			$alias = 'empty-resource-alias';
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::prepareAlias();
		}
		return $alias;
	}


	/** {@inheritDoc} */
	public function beforeSet() {
		$this->setDefaultProperties(array(
			'show_in_tree' => $this->modx->getOption('ms2_product_show_in_tree_default', null, false)
			,'hidemenu' => $this->modx->getOption('hidemenu_default', null, true)
			,'source' => $this->modx->getOption('ms2_product_source_default', null, 1)
			,'template' => $this->modx->getOption('ms2_template_product_default', null, $this->modx->getOption('default_template'))
		));

		return parent::beforeSet();
	}


	/** {@inheritDoc} */
	public function beforeSave() {
		$this->object->set('isfolder', 0);

		return parent::beforeSave();
	}


	/** {@inheritDoc} */
	public function afterSave() {
		if ($this->object->alias == 'empty-resource-alias') {
			$this->object->set('alias', $this->object->id);
			$this->object->save();
		}

		// Updating resourceMap before OnDocSaveForm event
		$results = $this->modx->cacheManager->generateContext($this->object->context_key);
		$this->modx->context->resourceMap = $results['resourceMap'];
		$this->modx->context->aliasMap = $results['aliasMap'];

		return parent::afterSave();
	}


	/** {@inheritDoc} */
	public function clearCache() {
		$clear = parent::clearCache();
		/* @var msCategory $category */
		if ($category = $this->object->getOne('Category')) {
			$category->clearCache();
		}

		return $clear;
	}

}

return 'msProductCreateProcessor';