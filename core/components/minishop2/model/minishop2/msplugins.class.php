<?php

class msPlugins
{

    public $_plugins = array();


    /**
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX $modx, array $config)
    {
        $this->modx = $modx;

        $this->config = array_merge(array(
            'pluginsPath' => $modx->getOption('ms2_plugins_path', null,
                MODX_CORE_PATH . 'components/minishop2/plugins/'),
        ), $config);

        $this->getPlugins();
    }


    /**
     * Loads available plugins with parameters
     *
     * @return array
     */
    public function getPlugins()
    {
        if (empty($this->_plugins)) {
            $plugins = scandir($this->config['pluginsPath']);
            foreach ($plugins as $plugin) {
                if ($plugin == '.' || $plugin == '..') {
                    continue;
                }
                $dir = $this->config['pluginsPath'] . $plugin;

                if (is_dir($dir) && file_exists($dir . '/index.php')) {
                    /** @noinspection PhpIncludeInspection */
                    $include = include_once($dir . '/index.php');
                    if (is_array($include)) {
                        $this->_plugins[$plugin] = $include;
                    }
                }
            }
        }

        return $this->_plugins;
    }


    /**
     * Loads additional metadata for miniShop2 objects
     */
    public function loadMap()
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $plugin) {
            if (isset($plugin['xpdo_meta_map']) && is_array($plugin['xpdo_meta_map'])) {
                foreach ($plugin['xpdo_meta_map'] as $class => $map) {
                    if (!isset($this->modx->map[$class])) {
                        $this->modx->loadClass($class, MODX_CORE_PATH . 'components/minishop2/model/minishop2/');
                    }
                    if (isset($this->modx->map[$class])) {
                        foreach ($map as $key => $values) {
                            $this->modx->map[$class][$key] = array_merge($this->modx->map[$class][$key], $values);
                        }
                    }
                }
            }
        }
    }

}