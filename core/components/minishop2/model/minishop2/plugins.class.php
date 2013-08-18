<?php

class ms2Plugins {

	public $plugins = array();


	/**
	 * @param xPDO $xpdo
	 * @param array $config
	 */
	public function __construct(xPDO $xpdo, array $config) {
		$this->xpdo = $xpdo;

		$this->config = array_merge(array(
			'pluginsPath' => $this->xpdo->getOption('ms2_plugins_path', null, MODX_CORE_PATH . 'components/minishop2/plugins/')
		), $config);

		$this->getPlugins();
	}


	/**
	 * Loads available plugins with parameters
	 *
	 * @return void
	 */
	public function getPlugins() {
		$plugins = scandir($this->config['pluginsPath']);
		foreach ($plugins as $plugin) {
			if ($plugin == '.' || $plugin == '..') {continue;}
			$dir = $this->config['pluginsPath'] . $plugin;

			if (is_dir($dir) && file_exists($dir . '/index.php')) {
				$include = include_once($dir . '/index.php');
				if (is_array($include)) {
					$this->plugins[$plugin] = $include;
				}
			}
		}
	}


	/**
	 * Loads additional metadata for miniShop2 objects
	 *
	 * @param string $className Name of class for extension
	 * @param array $xpdo_meta_map Array with meta fields of xPDO
	 *
	 * @return array $xpdo_meta_map Array with extended fields
	 */
	public function loadMap($className, $xpdo_meta_map) {
		foreach ($this->plugins as $plugin) {
			if (array_key_exists('xpdo_meta_map', $plugin) && array_key_exists($className, $plugin['xpdo_meta_map']) && is_array($plugin['xpdo_meta_map'][$className])) {
				foreach ($plugin['xpdo_meta_map'][$className] as $k => $v) {
					$xpdo_meta_map[$k] = array_merge($xpdo_meta_map[$k], $v);
				}
			}
		}

		return $xpdo_meta_map;
	}

}