<?php
/**
 * The main manager controller for miniShop2.
 *
 * @package minishop2
 */

require_once dirname(__FILE__) . '/model/minishop2/minishop2.class.php';

abstract class miniShop2MainController extends modExtraManagerController {
	/** @var miniShop2 $minishop2 */
	public $miniShop2;

	public function initialize() {
		$this->miniShop2 = new miniShop2($this->modx);
		
		$this->modx->regClientCSS($this->miniShop2->config['cssUrl'].'mgr/main.css');
		$this->modx->regClientStartupScript($this->miniShop2->config['jsUrl'].'mgr/minishop2.js');
		$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
		Ext.onReady(function() {
			miniShop2.config = '.$this->modx->toJSON($this->miniShop2->config).';
			miniShop2.config.connector_url = "'.$this->miniShop2->config['connectorUrl'].'";
		});
		</script>');
		
		parent::initialize();
	}

	public function getLanguageTopics() {
		return array('minishop2:default');
	}

	public function checkPermissions() { return true;}
}

class IndexManagerController extends miniShop2MainController {
	public static function getDefaultController() { return 'home'; }
}