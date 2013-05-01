<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

class ControllersMgrOrdersManagerController extends miniShop2MainController {
	public static function getDefaultController() { return 'orders'; }
}

class Minishop2OrdersManagerController extends miniShop2MainController {

	public function process(array $scriptProperties = array()) {}

	public function getPageTitle() { return 'miniShop2 :: ' . $this->modx->lexicon('ms2_orders'); }

	public function getLanguageTopics() {
		return array('minishop2:default','minishop2:product','minishop2:manager');
	}

	public function loadCustomCssJs() {
		$this->addCss($this->miniShop2->config['cssUrl']. 'mgr/bootstrap.min.css');

		$this->addJavascript(MODX_MANAGER_URL.'assets/modext/util/datetime.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/misc/ms2.utils.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/misc/ms2.combo.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/orders/orders.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/orders/orders.panel.js');
		$this->addHtml('<script type="text/javascript">
			Ext.onReady(function() {
				MODx.load({ xtype: "minishop2-page-orders"});
			});
		</script>');

		$this->modx->invokeEvent('msOnManagerCustomCssJs',array('controller' => &$this, 'page' => 'orders'));
	}

	public function getTemplateFile() {
		return $this->miniShop2->config['templatesPath'].'mgr/orders.tpl';
	}
}