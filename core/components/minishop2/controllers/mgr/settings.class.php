<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

class ControllersSettingsManagerController extends miniShop2MainController {
	public static function getDefaultController() {
		return 'settings';
	}
}

class Minishop2SettingsManagerController extends miniShop2MainController {

	public function getPageTitle() {
		return 'miniShop2 :: ' . $this->modx->lexicon('ms2_settings');
	}


	public function loadCustomCssJs() {
		$this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.utils.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.combo.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/delivery.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/payment.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/status.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/vendor.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/link.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/category.tree.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/option.grid.js');
		$this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/settings.panel.js');

        $types = $this->miniShop2->loadOptionTypeList();
        foreach ($types as $type) {
            $className = $this->miniShop2->loadOptionType($type);
            if (class_exists($className)) {
                /** @var msOptionType $className */
                if ($className::$script) {
                    $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/settings/types/' . $className::$script);
                }
            }
        }

		$this->addHtml('<script type="text/javascript">
			Ext.onReady(function() {
				MODx.load({ xtype: "minishop2-page-settings"});
			});
		</script>');
		$this->modx->invokeEvent('msOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'settings'));
	}


	public function getTemplateFile() {
		return $this->miniShop2->config['templatesPath'] . 'mgr/settings.tpl';
	}
}

// MODX 2.3
class ControllersMgrSettingsManagerController extends ControllersSettingsManagerController {
	public static function getDefaultController() {
		return 'mgr/settings';
	}
}

class Minishop2MgrSettingsManagerController extends Minishop2SettingsManagerController {
}