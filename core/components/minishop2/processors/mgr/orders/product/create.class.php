<?php

class msOrderProductCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'msOrderProduct';
	public $languageTopics = array('minishop2:default');
	public $permission = 'msorder_save';
	/* @var msOrder $order */
	protected $order;


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function beforeSet() {
		$count = $this->getProperty('count');
		if ($count <= 0) {
			$this->modx->error->addField('count', $this->modx->lexicon('ms2_err_ns'));
		}

		if ($options = $this->getProperty('options')) {
			$tmp = $this->modx->fromJSON($options);
			if (!is_array($tmp)) {
				$this->modx->error->addField('options', $this->modx->lexicon('ms2_err_json'));
			}
			else {
				$this->setProperty('options', $tmp);
			}
		}

		if (!$this->order = $this->modx->getObject('msOrder', $this->getProperty('order_id'))) {
			return $this->modx->lexicon('ms2_err_order_nf');
		}

		/* @var msOrderStatus $status */
		if ($status = $this->order->getOne('Status')) {
			if ($status->get('final')) {
				return $this->modx->lexicon('ms2_err_status_final');
			}
		}

		$this->setProperty('cost', $this->getProperty('price') * $this->getProperty('count'));
		$this->setProperty('product_id', $this->getProperty('id'));
		return !$this->hasErrors();
	}


	/** {@inheritDoc} */
	public function beforeSave() {
		$this->object->fromArray(array(
			'rank' => $this->modx->getCount('msOrderProduct')
		));
		return parent::beforeSave();
	}

}

return 'msOrderProductCreateProcessor';