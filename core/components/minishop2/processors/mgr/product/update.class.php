<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class msProductUpdateProcessor extends modResourceUpdateProcessor {
	public $classKey = 'msProduct';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'msproduct_save';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';
	/** @var msProduct $object */
	public $object;


	/** {inheritDoc} */
	public function initialize() {
		$primaryKey = $this->getProperty($this->primaryKeyField,false);
		if (empty($primaryKey)) return $this->modx->lexicon($this->classKey.'_err_ns');

		if (!$this->modx->getCount($this->classKey, array('id' => $primaryKey, 'class_key' => $this->classKey)) && $res = $this->modx->getObject('modResource', $primaryKey)) {
			$res->set('class_key', $this->classKey);
			$res->save();
		}

		return parent::initialize();
	}


	/** {inheritDoc} */
	public function handleCheckBoxes() {
		parent::handleCheckBoxes();
		$this->setCheckbox('new');
		$this->setCheckbox('popular');
		$this->setCheckbox('favorite');
	}


	/** {inheritDoc} */
	public function checkPublishedOn() {
		$published = $this->getProperty('published',null);
		if ($published !== null && $published != $this->object->get('published')) {
			if (empty($published)) { /* if unpublishing */
				$this->setProperty('publishedon',0);
				$this->setProperty('publishedby',0);
			} else { /* if publishing */
				$publishedOn = $this->getProperty('publishedon',null);
				$this->setProperty('publishedon',!empty($publishedOn) ? strtotime($publishedOn) : time());
				$this->setProperty('publishedby',$this->modx->user->get('id'));
			}
		} else { /* if no change, unset publishedon/publishedby */
			if (empty($published)) { /* allow changing of publishedon date if resource is published */
				$this->unsetProperty('publishedon');
				$this->unsetProperty('publishedby');
			}
		}
		return $this->getProperty('publishedon');
	}


	/** {inheritDoc} */
	public function checkFriendlyAlias() {
		if ($this->workingContext->getOption('ms2_product_id_as_alias')) {
			$alias = $this->object->id;
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::checkFriendlyAlias();
		}
		return $alias;
	}


	/** {inheritDoc} */
	public function beforeSave() {
		$this->object->set('isfolder', 0);

		return parent::beforeSave();
	}


	/** {inheritDoc} */
	public function fixParents() {
		if (!empty($this->oldParent) && !($this->oldParent instanceof msCategory)) {
			$oldParentChildrenCount = $this->modx->getCount('modResource', array('parent' => $this->oldParent->get('id')));
			if ($oldParentChildrenCount <= 0 || $oldParentChildrenCount == null) {
				$this->oldParent->set('isfolder', false);
				$this->oldParent->save();
			}
		}

		if (!empty($this->newParent)) {
			$this->newParent->set('isfolder', true);
		}
	}


	/** {inheritDoc} */
	public function clearCache() {
		parent::clearCache();
		$this->object->clearCache();
	}

}

return 'msProductUpdateProcessor';