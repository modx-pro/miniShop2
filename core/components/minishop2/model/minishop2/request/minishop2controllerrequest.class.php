<?php
/**
 * @package minishop2
 */
require_once MODX_CORE_PATH . 'model/modx/modrequest.class.php';
/**
 * Encapsulates the interaction of MODx manager with an HTTP request.
 *
 * {@inheritdoc}
 *
 * @package minishop2
 * @extends modRequest
 */
class miniShop2ControllerRequest extends modRequest {
	public $miniShop2 = null;
	public $actionVar = 'action';
	public $defaultAction = 'home';

	function __construct(miniShop2 &$miniShop2) {
		parent :: __construct($miniShop2->modx);
		$this->miniShop2 =& $miniShop2;
	}

	/**
	 * Extends modRequest::handleRequest and loads the proper error handler and
	 * actionVar value.
	 *
	 * {@inheritdoc}
	 */
	public function handleRequest() {
		$this->loadErrorHandler();

		/* save page to manager object. allow custom actionVar choice for extending classes. */
		$this->action = isset($_REQUEST[$this->actionVar]) ? $_REQUEST[$this->actionVar] : $this->defaultAction;

		return $this->_respond();
	}

	/**
	 * Prepares the MODx response to a mgr request that is being handled.
	 *
	 * @access public
	 * @return boolean True if the response is properly prepared.
	 */
	private function _respond() {
		$modx =& $this->modx;
		$miniShop2 =& $this->miniShop2;

		$viewHeader = include $this->miniShop2->config['corePath'].'controllers/mgr/header.php';

		$f = $this->miniShop2->config['corePath'].'controllers/mgr/'.$this->action.'.php';
		if (file_exists($f)) {
			$viewOutput = include $f;
		} else {
			$viewOutput = 'Action not found: '.$f;
		}

		return $viewHeader.$viewOutput;
	}
}