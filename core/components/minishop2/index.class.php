<?php

require_once dirname(__FILE__) . '/model/minishop2/minishop2.class.php';

abstract class miniShop2MainController extends modExtraManagerController {
	/** @var miniShop2 $minishop2 */
	public $miniShop2;


	public function initialize() {
		$this->miniShop2 = new miniShop2($this->modx);

		$this->addCss($this->miniShop2->config['cssUrl'] . 'mgr/bootstrap.min.css');
		$this->addCss($this->miniShop2->config['cssUrl'] . 'mgr/main.css');
		$this->addJavaScript($this->miniShop2->config['jsUrl'] . 'mgr/minishop2.js');
		$this->addHtml(str_replace('		', '', '
		<script type="text/javascript">
			miniShop2.config = ' . $this->modx->toJSON($this->miniShop2->config) . ';
			miniShop2.config.connector_url = "' . $this->miniShop2->config['connectorUrl'] . '";
		</script>'));

		parent::initialize();
	}


	public function getLanguageTopics() {
		return array('minishop2:default', 'minishop2:manager');
	}


	public function checkPermissions() {
		return true;
	}
}


/**
 * @package quip
 * @subpackage controllers
 */
class IndexManagerController extends miniShop2MainController {
	public static function getDefaultController() {
		return 'mgr/orders';
	}
}
