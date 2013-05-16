<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';

class msProductCreateProcessor extends modResourceCreateProcessor {
	public $classKey = 'msProduct';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'msproduct_save';
	public $objectType = 'resource';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';
	/* @var msProduct $object */
	public $object;

	/**
	 * {@inheritDoc}
	 * @return * @return string|mixed
	 */
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

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function beforeSet() {
		$this->setDefaultProperties(array(
			'show_in_tree' => $this->modx->getOption('ms2_product_show_in_tree_default', null, false)
			,'hidemenu' => $this->modx->getOption('hidemenu_default', null, true)
		));

		return parent::beforeSet();
	}


	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function beforeSave() {
		$this->object->set('isfolder', 0);

		return parent::beforeSave();
	}


	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
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

	/**
	 * {@inheritDoc}
	 */
	public function clearCache() {
		$clear = parent::clearCache();
		/* @var msCategory $category */
		$category = $this->object->getOne('Category');
		$category->clearCache();

		return $clear;
	}

}

return 'msProductCreateProcessor';