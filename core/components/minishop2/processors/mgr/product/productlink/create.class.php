<?php

class msProductLinkCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msProductLink';
	public $languageTopics = array('minishop2:default');
	public $permission = 'msproduct_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function process() {
		if (!$master = $this->getProperty('master')) {
			$this->addFieldError('master', $this->modx->lexicon('ms2_err_ns'));
		}
		if (!$slave = $this->getProperty('slave')) {
			$this->addFieldError('slave', $this->modx->lexicon('ms2_err_ns'));
		}
		if (!$link = $this->getProperty('link')) {
			$this->addFieldError('link', $this->modx->lexicon('ms2_err_ns'));
		}

		if ($this->hasErrors()) {
			return $this->failure();
		}
		else if ($master == $slave) {
			return $this->failure($this->modx->lexicon('ms2_err_link_equal'));
		}

		/* @var msLink $msLink */
		if (!$msLink = $this->modx->getObject('msLink', $link)) {
			return $this->failure($this->modx->lexicon('ms2_err_no_link'));
		}
		$type = $msLink->get('type');

		switch ($type) {
			case 'many_to_many':
				$this->addLink($link, $master, $slave);
				$this->addLink($link, $slave, $master);

				$q = $this->modx->newQuery('msProductLink', array('link' => $link));
				$q->andCondition(array('master:IN' => array($master,$slave)));
				$q->select('slave');

				if ($q->prepare() && $q->stmt->execute()) {
					$slaves = $row = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
					$slaves = array_unique($slaves);
					$rows = array();
					foreach ($slaves as $v) {
						foreach ($slaves as $v2) {
							if ($v != $v2) {
								$rows[] = "('$link','$v','$v2')";
							}
						}
					}
					$sql = "INSERT INTO {$this->modx->getTableName('msProductLink')} (`link`,`master`,`slave`) VALUES ";
					$sql .= implode(',', $rows);
					$sql .= " ON DUPLICATE KEY UPDATE `link` = '$link';";
					$this->modx->exec($sql);
				}
			break;

			case 'one_to_many':
				$this->addLink($link, $master, $slave);
			break;

			case 'many_to_one':
				$this->addLink($link, $slave, $master);
			break;

			case 'one_to_one':
				$this->addLink($link, $master, $slave);
				$this->addLink($link, $slave, $master);
			break;
		}

		return $this->success('');
	}


	/** {@inheritDoc} */
	public function addLink($link = 0, $master = 0, $slave = 0) {
		if ($link && $master && $slave) {
			$sql = "INSERT INTO {$this->modx->getTableName('msProductLink')} (`link`,`master`,`slave`) VALUES ('$link','$master','$slave') ON DUPLICATE KEY UPDATE `link` = '$link';";
			$this->modx->exec($sql);
		}
		return false;
	}
}

return 'msProductLinkCreateProcessor';