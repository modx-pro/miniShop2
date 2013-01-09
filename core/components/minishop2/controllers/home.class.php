<?php
/**
 * The home manager controller for miniShop2.
 *
 * @package minishop2
 */
class miniShop2HomeManagerController extends miniShop2MainController {
	public function process(array $scriptProperties = array()) {}
	
	public function getPageTitle() { return $this->modx->lexicon('minishop2'); }
	
	public function loadCustomCssJs() {
		$this->modx->regClientStartupScript($this->miniShop2->config['jsUrl'].'mgr/widgets/items.grid.js');
		$this->modx->regClientStartupScript($this->miniShop2->config['jsUrl'].'mgr/widgets/home.panel.js');
		$this->modx->regClientStartupScript($this->miniShop2->config['jsUrl'].'mgr/sections/home.js');
	}
	
	public function getTemplateFile() {
		return $this->miniShop2->config['templatesPath'].'home.tpl';
	}
}