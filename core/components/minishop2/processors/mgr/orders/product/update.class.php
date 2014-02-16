<?php

class msOrderProductUpdateProcessor extends modObjectUpdateProcessor {
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

		if (!$this->order = $this->object->getOne('Order')) {
			return $this->modx->lexicon('ms2_err_order_nf');
		}

		/* @var msOrderStatus $status */
		if ($status = $this->order->getOne('Status')) {
			if ($status->get('final')) {
				return $this->modx->lexicon('ms2_err_status_final');
			}
		}

		$this->setProperty('cost', $this->getProperty('price') * $this->getProperty('count'));

		return !$this->hasErrors();
	}


	/** {@inheritDoc} */
	public function afterSave() {
		$this->order->updateProducts();
	}

}

return 'msOrderProductUpdateProcessor';