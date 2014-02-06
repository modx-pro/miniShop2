<?php

if (!defined('MODX_BASE_PATH')) {
	require 'build.config.php';
}

/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
	'root' => $root,
	'build' => $root . '_build/',
	'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
	'model' => $root.'core/components/'.PKG_NAME_LOWER.'/model/',
	'schema' => $root.'core/components/'.PKG_NAME_LOWER.'/model/schema/',
	'xml' => $root.'core/components/'.PKG_NAME_LOWER.'/model/schema/'.PKG_NAME_LOWER.'.mysql.schema.xml',
);
unset($root);

require MODX_CORE_PATH . 'model/modx/modx.class.php';
require $sources['build'] . '/includes/functions.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->loadClass('transport.modPackageBuilder', '', false, true);

/** @var xPDOManager $manager */
$manager = $modx->getManager();
/** @var xPDOGenerator $generator */
$generator = $manager->getGenerator();

// Remove old model
rrmdir($sources['model'] . PKG_NAME_LOWER . '/mysql');

// Generate a new one
$generator->parseSchema($sources['xml'], $sources['model']);
$modx->addPackage(PKG_NAME_LOWER, $sources['model']);

$modx->log(modX::LOG_LEVEL_INFO, 'Model generated.');

add_plugins_call($sources['model'] . PKG_NAME_LOWER, array(
	'msProductData',
	'msCustomerProfile',
));

print "\nDone\n";

/********************************************************/

function add_plugins_call($dir, $classes = array()) {
	foreach ($classes as $name) {
		$file = $dir . '/mysql/' . strtolower($name) . '.map.inc.php';
		if (file_exists($file)) {
			file_put_contents($file, str_replace('				', '', "\n" . '
				if (!class_exists(\'ms2Plugins\') || !is_object($this->ms2Plugins)) {
					require_once (dirname(dirname(__FILE__)) . \'/plugins.class.php\');
					$this->ms2Plugins = new ms2Plugins($this, array());
				}
				$xpdo_meta_map[\'' . $name . '\'] = $this->ms2Plugins->loadMap(\'' . $name . '\', $xpdo_meta_map[\'' . $name . '\']);')
				, FILE_APPEND);
		}
	}
}