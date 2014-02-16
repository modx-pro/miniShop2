<?php
class msProductFileGenerateMultipleProcessor extends modObjectProcessor {
	public $classKey = 'msProductFile';
	public $languageTopics = array('minishop2:default');
	public $permission = 'msproductfile_generate';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function process() {
		$ids = $this->getProperty('ids');
		if (empty($ids)) return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));

		$separator = $this->getProperty('separator',',');
		$ids = explode($separator,$ids);

		foreach ($ids as $id) {
			/* @var msProductFile $file */
			if ($file = $this->modx->getObject('msProductFile', $id)) {
				$children = $file->getMany('Children');
				/* @var msProductFile $child */
				foreach ($children as $child) {
					$child->remove();
				}
				$file->generateThumbnails();
			}
		}

		return $this->success();
	}
}
return 'msProductFileGenerateMultipleProcessor';