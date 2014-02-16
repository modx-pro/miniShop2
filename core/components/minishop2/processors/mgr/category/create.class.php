<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';

class msCategoryCreateProcessor extends modResourceCreateProcessor {
	public $classKey = 'msCategory';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'mscategory_save';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';


	/** {@inheritDoc} */
	public function beforeSet() {
		$this->setProperties(array(
			'isfolder' => 1
		));
		return parent::beforeSet();
	}


	/** {@inheritDoc} */
	public function prepareAlias() {
		if ($this->workingContext->getOption('ms2_category_id_as_alias')) {
			$alias = 'empty-resource-alias';
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::prepareAlias();
		}
		return $alias;
	}


	/** {@inheritDoc} */
	public function afterSave() {
		if ($this->object->alias == 'empty-resource-alias') {
			$this->object->set('alias', $this->object->id);
			$this->object->save();
		}

		// Updating resourceMap before OnDocSaveForm event
		$results = $this->modx->cacheManager->generateContext($this->object->context_key);
		if (isset($results['resourceMap'])) {$this->modx->context->resourceMap = $results['resourceMap'];}
		if (isset($results['aliasMap'])) {$this->modx->context->aliasMap = $results['aliasMap'];}

		return parent::afterSave();
	}

}
