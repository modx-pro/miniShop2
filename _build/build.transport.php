<?php
/**
 * miniShop2 build script
 *
 * @package minishop2 
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME','miniShop2');
define('PKG_NAME_LOWER',strtolower(PKG_NAME));
define('PKG_VERSION','2.0.1');
define('PKG_RELEASE','beta');

/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
	'root' => $root,
	'build' => $root . '_build/',
	'data' => $root . '_build/data/',
	'resolvers' => $root . '_build/resolvers/',
	'chunks' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/chunks/',
	'snippets' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/snippets/',
	'plugins' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
	'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
	'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
	'pages' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/pages/',
	'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
	'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
);
unset($root);

/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');
$modx->log(modX::LOG_LEVEL_INFO,'Created Transport Package and Namespace.');

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_NAME);

/* add snippets */
$snippets = include $sources['data'].'transport.snippets.php';
if (!is_array($snippets)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in snippets.');
} else {
	$category->addMany($snippets);
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($snippets).' snippets.');
}

/* add chunks */
$chunks = include $sources['data'].'transport.chunks.php';
if (!is_array($chunks)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in chunks.');
} else {
	$category->addMany($chunks);
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($chunks).' chunks.');
}

/* add plugins */
$plugins = include $sources['data'].'transport.plugins.php';
if (!is_array($plugins)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in plugins.');
} else {
	$category->addMany($plugins);
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($plugins).' plugins.');
}

/* create category vehicle */
$attr = array(
	xPDOTransport::UNIQUE_KEY => 'category',
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UPDATE_OBJECT => true,
	xPDOTransport::RELATED_OBJECTS => true,
	xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
		'Children' => array(
			xPDOTransport::PRESERVE_KEYS => false,
			xPDOTransport::UPDATE_OBJECT => true,
			xPDOTransport::UNIQUE_KEY => 'category',
			xPDOTransport::RELATED_OBJECTS => true,
			xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
				'Snippets' => array(
					xPDOTransport::PRESERVE_KEYS => false,
					xPDOTransport::UPDATE_OBJECT => true,
					xPDOTransport::UNIQUE_KEY => 'name',
				),
				'Chunks' => array(
					xPDOTransport::PRESERVE_KEYS => false,
					xPDOTransport::UPDATE_OBJECT => false,
					xPDOTransport::UNIQUE_KEY => 'name',
				),
			),
		),
		'Snippets' => array(
			xPDOTransport::PRESERVE_KEYS => false,
			xPDOTransport::UPDATE_OBJECT => true,
			xPDOTransport::UNIQUE_KEY => 'name',
		),
		'Chunks' => array (
			xPDOTransport::PRESERVE_KEYS => false,
			xPDOTransport::UPDATE_OBJECT => false,
			xPDOTransport::UNIQUE_KEY => 'name',
		),
		'Plugins' => array (
			xPDOTransport::PRESERVE_KEYS => false,
			xPDOTransport::UPDATE_OBJECT => true,
			xPDOTransport::UNIQUE_KEY => 'name',
		),
		'PluginEvents' => array (
			xPDOTransport::PRESERVE_KEYS => true,
			xPDOTransport::UPDATE_OBJECT => true,
			xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
		),
	),
);

/* load plugins events */
$events = include $sources['data'].'transport.events.php';
if (!is_array($events)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in events.');
} else {
	$attributes = array (
		xPDOTransport::PRESERVE_KEYS => true,
		xPDOTransport::UPDATE_OBJECT => true,
	);
	foreach ($events as $event) {
		$vehicle = $builder->createVehicle($event,$attributes);
		$builder->putVehicle($vehicle);
	}
	$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' plugins events.');
}
unset ($events, $event, $attributes);

/* load system settings */
$settings = include $sources['data'].'transport.settings.php';
if (!is_array($settings)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
} else {
	$attributes= array(
		xPDOTransport::UNIQUE_KEY => 'key',
		xPDOTransport::PRESERVE_KEYS => true,
		xPDOTransport::UPDATE_OBJECT => false,
	);
	foreach ($settings as $setting) {
		$vehicle = $builder->createVehicle($setting,$attributes);
		$builder->putVehicle($vehicle);
	}
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' System Settings.');
}
unset($settings,$setting,$attributes);

/* package in default access policy */
$attributes = array (
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UNIQUE_KEY => array('name'),
	xPDOTransport::UPDATE_OBJECT => true,
);
$policies = include $sources['data'].'transport.policies.php';
if (!is_array($policies)) { $modx->log(modX::LOG_LEVEL_FATAL,'Adding policies failed.'); }
foreach ($policies as $policy) {
	$vehicle = $builder->createVehicle($policy,$attributes);
	$builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($policies).' Access Policies.'); flush();
unset($policies,$policy,$attributes);
/* package in default access policy template */
$templates = include dirname(__FILE__).'/data/transport.policytemplates.php';
$attributes = array (
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UNIQUE_KEY => array('name'),
	xPDOTransport::UPDATE_OBJECT => true,
	xPDOTransport::RELATED_OBJECTS => true,
	xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
		'Permissions' => array (
			xPDOTransport::PRESERVE_KEYS => false,
			xPDOTransport::UPDATE_OBJECT => true,
			xPDOTransport::UNIQUE_KEY => array ('template','name'),
		),
	)
);
if (is_array($templates)) {
	foreach ($templates as $template) {
		$vehicle = $builder->createVehicle($template,$attributes);
		$builder->putVehicle($vehicle);
	}
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($templates).' Access Policy Templates.'); flush();
} else {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in Access Policy Templates.');
}
unset ($templates,$template,$attributes);

/* load menus */
$menus = include $sources['data'].'transport.menu.php';
$attributes = array (
	xPDOTransport::PRESERVE_KEYS => true,
	xPDOTransport::UPDATE_OBJECT => false,
	xPDOTransport::UNIQUE_KEY => 'text',
	xPDOTransport::RELATED_OBJECTS => true,
	xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
		'Action' => array (
			xPDOTransport::PRESERVE_KEYS => false,
			xPDOTransport::UPDATE_OBJECT => false,
			xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
		),
	),
);
if (is_array($menus)) {
	foreach ($menus as $menu) {
		$vehicle = $builder->createVehicle($menu,$attributes);
		$builder->putVehicle($vehicle);
		$modx->log(modX::LOG_LEVEL_INFO,'Packaged in menu "'.$menu->get('text').'".');
	}
}
else {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in menu.');
}
unset($vehicle,$menus,$menu,$attributes);

/* now pack in the license file, readme and setup options */
$vehicle = $builder->createVehicle($category,$attr);
$modx->log(modX::LOG_LEVEL_INFO,'Adding resolvers to category...');
$vehicle->resolve('file',array(
	'source' => $sources['source_assets'],
	'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file',array(
	'source' => $sources['source_core'],
	'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('php',array(
	'source' => $sources['resolvers'] . 'resolve.tables.php',
));
$vehicle->resolve('php',array(
	'source' => $sources['resolvers'] . 'resolve.policy.php',
));
$vehicle->resolve('php',array(
	'source' => $sources['resolvers'] . 'resolve.sources.php',
));
$vehicle->resolve('php',array(
	'source' => $sources['resolvers'] . 'resolve.settings.php',
));
$vehicle->resolve('php',array(
	'source' => $sources['resolvers'] . 'resolve.setup.php',
));

$modx->log(modX::LOG_LEVEL_INFO,'Packaged in resolvers.'); flush();
$builder->putVehicle($vehicle);

$builder->setPackageAttributes(array(
	'changelog' => file_get_contents($sources['docs'] . 'changelog.txt')
	,'license' => file_get_contents($sources['docs'] . 'license.txt')
	,'readme' => file_get_contents($sources['docs'] . 'readme.txt')
	,'setup-options' => array(
		'source' => $sources['build'].'setup.options.php',
	),
));
$modx->log(modX::LOG_LEVEL_INFO,'Added package attributes and setup options.');

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();