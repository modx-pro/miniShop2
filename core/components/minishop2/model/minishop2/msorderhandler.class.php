<?php

interface msOrderInterface {

	/* Initializes order to context
	 * Here you can load custom javascript or styles
	 *
	 * @param string $ctx Context for initialization
	 *
	 * @return boolean
	 * */
	public function initialize($ctx = 'web');

}


class msOrderHandler implements msOrderInterface {

	function __construct(modX & $modx, array $config = array()) {
		$this->modx = & $modx;

		$this->config = array_merge(array(),$config);

		$this->modx->lexicon->load('minishop2:order');
	}

	/* @inheritdoc} */
	public function initialize($ctx = 'web') {
		return true;
	}
}