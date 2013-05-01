<?php

require_once dirname(__FILE__) . '/model/minishop2/minishop2.class.php';

abstract class miniShop2MainController extends modExtraManagerController {
	/** @var miniShop2 $minishop2 */
	public $miniShop2;


	public static function getInstance(modX &$modx, $className, array $config = array()) {
		$action = call_user_func(array($className,'getDefaultController'));
		if (isset($_REQUEST['action'])) {
			$action = str_replace(array('../','./','.','-','@'),'',$_REQUEST['action']);
		}
		$className = self::getControllerClassName($action,$config['namespace']);
		$classPath = $config['namespace_path'].'controllers/mgr/'.$action.'.class.php';
		require_once $classPath;
		/** @var modManagerController $controller */
		$controller = new $className($modx,$config);
		return $controller;
	}


	public function initialize() {
		$this->miniShop2 = new miniShop2($this->modx);
		
		$this->modx->regClientCSS($this->miniShop2->config['cssUrl'].'mgr/main.css');
		$this->addJavaScript($this->miniShop2->config['jsUrl'].'mgr/minishop2.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			miniShop2.config = '.$this->modx->toJSON($this->miniShop2->config).';
			miniShop2.config.connector_url = "'.$this->miniShop2->config['connectorUrl'].'";
		});
		</script>');
		
		parent::initialize();
	}


	public function getLanguageTopics() {
		return array('minishop2:default','minishop2:manager');
	}


	public function checkPermissions() { return true;}
}