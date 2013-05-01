<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

class ControllersMgrSettingsManagerController extends miniShop2MainController {
	public static function getDefaultController() { return 'settings'; }
}

class Minishop2SettingsManagerController extends miniShop2MainController {

	public function process(array $scriptProperties = array()) {}

	public function getPageTitle() { return 'miniShop2 :: ' . $this->modx->lexicon('ms2_settings'); }

	public function loadCustomCssJs() {
		$this->addCss($this->miniShop2->config['cssUrl']. 'mgr/bootstrap.min.css');

		$this->addJavascript(MODX_MANAGER_URL.'assets/modext/util/datetime.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/misc/ms2.utils.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/misc/ms2.combo.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/settings/delivery.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/settings/payment.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/settings/status.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/settings/vendor.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/settings/link.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'].'mgr/settings/settings.panel.js');
		$this->addHtml('<script type="text/javascript">
			Ext.onReady(function() {
				MODx.load({ xtype: "minishop2-page-settings"});
			});
		</script>');
		$this->modx->invokeEvent('msOnManagerCustomCssJs',array('controller' => &$this, 'page' => 'settings'));
	}

	public function getTemplateFile() {
		return $this->miniShop2->config['templatesPath'].'mgr/settings.tpl';
	}
}