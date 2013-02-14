<?php
class msProductFileGenerateProcessor extends modObjectProcessor {
	public $classKey = 'msProductFile';
	public $languageTopics = array('minishop2:default');

	public function process() {
		$id = $this->getProperty('id');
		if (empty($id)) return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));

		/* @var msProductFile $file */
		if ($file = $this->modx->getObject('msProductFile', $id)) {
			$children = $file->getMany('Children');
			/* @var msProductFile $child */
			foreach ($children as $child) {
				$child->remove();
			}
			$file->generateThumbnails();
		}

		return $this->success();
	}
}
return 'msProductFileGenerateProcessor';