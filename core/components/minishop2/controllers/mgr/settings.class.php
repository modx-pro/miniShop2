<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

class ControllersMgrSettingsManagerController extends miniShop2MainController {
	public static function getDefaultController() { return 'settings'; }
}

class Minishop2SettingsManagerController extends miniShop2MainController {

	public function process(array $scriptProperties = array()) {}

	public function getPageTitle() { return 'miniShop2 :: ' . $this->modx->lexicon('ms2_settings'); }

	public function loadCustomCssJs() {
		//$this->modx->regClientStartupScript($this->miniShop2->config['jsUrl'].'mgr/orders/orders.grid.js');
		//$this->modx->regClientStartupScript($this->miniShop2->config['jsUrl'].'mgr/orders/orders.panel.js');
		//$this->modx->regClientStartupScript($this->miniShop2->config['jsUrl'].'mgr/home.js');
	}

	public function getTemplateFile() {
		return $this->miniShop2->config['templatesPath'].'mgr/settings.tpl';
	}
}