<?php

class msLinkRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msLink';
	public $languageTopics = array('minishop2');

	public function initialize() {
		return true;
	}

	public function process() {
		$canRemove = $this->beforeRemove();
		if ($canRemove !== true) {
			return $this->failure($canRemove);
		}

		$link = $this->getProperty('link');
		$master = $this->getProperty('master');
		$slave = $this->getProperty('slave');

		if (!$link || !$master || !$slave) {
			return $this->failure('');
		}

		/* @var msLink $msLink */
		if (!$msLink = $this->modx->getObject('msLink', $link)) {
			return $this->failure($this->modx->lexicon('ms2_err_no_link'));
		}
		$type = $msLink->get('type');

		$sql = "DELETE FROM {$this->modx->getTableName('msProductLink')} WHERE `link` = {$link} AND ";
		switch ($type) {
			case 'many_to_many':
			case 'one_to_one':
				$sql .= "`master` = {$slave} OR `slave` = {$slave}";
			break;

			case 'one_to_many':
				$sql .= "`master` = {$master}";
			break;

			case 'many_to_one':
				$sql .= "`slave` = {$slave}";
			break;
		}
		$this->modx->exec($sql);

		return $this->success('');
	}



}
return 'msLinkRemoveProcessor';
