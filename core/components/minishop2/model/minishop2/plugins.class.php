<?php

class ms2Plugins {

	public $plugins = array();


	public function __construct(xPDO $xpdo, array $config) {
		$this->xpdo = $xpdo;

		$this->config = array_merge(array(
			'pluginsPath' => $this->xpdo->getOption('ms2_plugins_path')
		), $config);

		$this->getPlugins();
	}


	/*
	 * Loads available plugins with parameters
	 *
	 * @return void
	 * */
	public function getPlugins() {
		$this->plugins = array();

		/*
		$this->plugins['plugin1']['xpdo_meta_map']['msProductData'] = array(
			'fields' => array(
				'size' => 20
			)
			,'fieldMeta' => array(
				'size' => array(
					'dbtype' => 'varchar'
					,'precision' => 20
					,'phptype' => 'string'
					,'null' => 1
					,'default' => 0
				)
			)
		);
		*/
	}


	/*
	 * Loads additional metadata for miniShop2 objects
	 *
	 * @param string $className Name of class for extension
	 * @param array  $xpdo_meta_map Array with meta fields of xPDO
	 *
	 * @return array $xpdo_meta_map Array with extended fields
	 * */
	public function loadMap($className, $xpdo_meta_map) {

		foreach ($this->plugins as $plugin) {
			if (array_key_exists('xpdo_meta_map', $plugin) && array_key_exists($className, $plugin['xpdo_meta_map']) && is_array($plugin['xpdo_meta_map'][$className])) {
				$xpdo_meta_map = array_merge_recursive($xpdo_meta_map, $plugin['xpdo_meta_map'][$className]);
			}
		}

		return $xpdo_meta_map;
	}

}