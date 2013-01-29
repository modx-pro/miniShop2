<?php
require_once (dirname(__FILE__).'/update.class.php');

class msProductUpdateFromGridProcessor extends msProductUpdateProcessor {

	public static function getInstance(modX &$modx,$className,$properties = array()) {

		/** @var modProcessor $processor */
		$processor = new msProductUpdateFromGridProcessor($modx,$properties);
		return $processor;
	}


	public function initialize() {
		$data = $this->getProperty('data');
		if (empty($data)) {
			return $this->modx->lexicon('invalid_data');
		}

		$data = $this->modx->fromJSON($data);
		if (empty($data)) {
			return $this->modx->lexicon('invalid_data');
		}

		$this->setProperties($data);
		$this->unsetProperty('data');

		return parent::initialize();
	}


	/**
	 * Handle formatting of various checkbox fields
	 * @return void
	 */
	public function handleCheckBoxes() {
		$this->setCheckbox('hidemenu');
		$this->setCheckbox('isfolder');
		$this->setCheckbox('richtext');
		$this->setCheckbox('published');
		$this->setCheckbox('cacheable');
		$this->setCheckbox('searchable');
		$this->setCheckbox('syncsite');
		$this->setCheckbox('deleted');
		$this->setCheckbox('uri_override');
		$this->setCheckbox('show_in_tree');
	}


	/**
	 * Cleanup the processor and return the resulting object
	 *
	 * @return array
	 */
	public function cleanup() {
		$this->object->removeLock();
		$this->clearCache();

		$returnArray = $this->object->toArray();

		return $this->success('',$returnArray);
	}

}
return 'msProductUpdateFromGridProcessor';