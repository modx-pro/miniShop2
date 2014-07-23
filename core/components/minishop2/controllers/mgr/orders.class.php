<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

class ControllersOrdersManagerController extends miniShop2MainController {
	public static function getDefaultController() {
		return 'orders';
	}
}

class Minishop2OrdersManagerController extends miniShop2MainController {

	public function getPageTitle() {
		return 'miniShop2 :: ' . $this->modx->lexicon('ms2_orders');
	}


	public function getLanguageTopics() {
		return array('minishop2:default', 'minishop2:product', 'minishop2:manager');
	}


	public function loadCustomCssJs() {
		$this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.utils.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.combo.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.panel.js');

		$grid_fields = array_map('trim', explode(',', $this->modx->getOption('ms2_order_grid_fields', null, 'id,customer,num,status,cost,weight,delivery,payment,createdon,updatedon,comment', true)));
		$grid_fields = array_values(array_unique(array_merge($grid_fields, array('id', 'user_id', 'num', 'type', 'actions'))));

		$address_fields = array_map('trim', explode(',', $this->modx->getOption('ms2_order_address_fields')));
		$product_fields = array_map('trim', explode(',', $this->modx->getOption('ms2_order_product_fields', null, '')));
		$product_fields = array_values(array_unique(array_merge($product_fields, array('id', 'product_id', 'name'))));

		$this->addHtml(str_replace('			', '', '
			<script type="text/javascript">
				miniShop2.config.order_grid_fields = ' . $this->modx->toJSON($grid_fields) . ';
				miniShop2.config.order_address_fields = ' . $this->modx->toJSON($address_fields) . ';
				miniShop2.config.order_product_fields = ' . $this->modx->toJSON($product_fields) . ';

				Ext.onReady(function() {
					MODx.load({ xtype: "minishop2-page-orders"});
				});
			</script>'
		));

		$this->modx->invokeEvent('msOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'orders'));
	}


	public function getTemplateFile() {
		return $this->miniShop2->config['templatesPath'] . 'mgr/orders.tpl';
	}
}

// MODX 2.3
class ControllersMgrOrdersManagerController extends ControllersOrdersManagerController {
	public static function getDefaultController() {
		return 'mgr/orders';
	}
}

class Minishop2MgrOrdersManagerController extends Minishop2OrdersManagerController {
}