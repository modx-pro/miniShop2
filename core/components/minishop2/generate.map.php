<?php
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';
/*******************************************************/

$package = 'minishop2'; // Class name for generation
$suffix = 'minishop_'; // Suffix of tables.
$prefix = $modx->config['table_prefix']; // table prefix

// Folders for schema and model
$Model = dirname(__FILE__).'/model/';
$Schema = dirname(__FILE__).'/model/schema/';
$xml = $Schema.$package.'.mysql.schema.xml';

// Remove old files
rrmdir($Model.$package .'/mysql');
//unlink($xml);

/*******************************************************/

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->error->message = null;
$modx->loadClass('transport.modPackageBuilder', '', false, true);
$manager = $modx->getManager();

$generator = $manager->getGenerator();
$generator->parseSchema($xml, $Model);

$modx->addPackage($package, $Model);

//$manager->removeObjectContainer('msProductData');
//$manager->removeObjectContainer('msVendor');

//$manager->createObjectContainer('msProductData');
//$manager->createObjectContainer('msVendor');

add_plugins_call($Model.$package);

print "\nDone\n";


/********************************************************/
function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);

		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}

		reset($objects);
		rmdir($dir);
	}
}

function add_plugins_call($dir) {
	require $dir . '/metadata.mysql.php';
	foreach ($xpdo_meta_map as $object) {
		foreach ($object as $name) {
			$file = $dir . '/mysql/' . strtolower($name .'.map.inc.php');
			if (file_exists($file)) {
				file_put_contents($file, '
if (!in_array(\'ms2Plugins\', get_declared_classes())) {
	require_once (dirname(dirname(__FILE__)) . \'/plugins.class.php\');
	$this->ms2Plugins = new ms2Plugins($this, array());
}

$xpdo_meta_map[\''.$name.'\'] = $this->ms2Plugins->loadMap(\''.$name.'\', $xpdo_meta_map[\''.$name.'\']);', FILE_APPEND);
			}
		}
	}
}