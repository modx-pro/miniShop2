<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class msProductUpdateProcessor extends modResourceUpdateProcessor {
	public $classKey = 'msProduct';
	public $languageTopics = array('resource','minishop2:default');
	public $permission = 'msproduct_save';
	public $objectType = 'resource';
	public $beforeSaveEvent = 'OnBeforeDocFormSave';
	public $afterSaveEvent = 'OnDocFormSave';
	/** @var msProduct $object */
	public $object;
	/**
	 * Handle formatting of various checkbox fields
	 * @return void
	 */
	public function handleCheckBoxes() {
		parent::handleCheckBoxes();
		$this->setCheckbox('new');
		$this->setCheckbox('popular');
		$this->setCheckbox('favorite');
	}

	/**
	 * Set publishedon date if publish change is different
	 * @return int
	 */
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

	/**
	 * {@inheritDoc}
	 * @return string|mixed
	 */
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
	 */
	public function clearCache() {
		parent::clearCache();
		$this->object->clearCache();
	}

}

return 'msProductUpdateProcessor';