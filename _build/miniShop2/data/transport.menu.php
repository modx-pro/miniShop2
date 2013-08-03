<?php
/**
 * Adds modActions and modMenus into package
 *
 * @package minishop2
 * @subpackage build
 */

// Available actions
$actions = array();

$actions['main'] = $modx->newObject('modAction');
$actions['main']->fromArray(array(
	'id' => 1,
	'namespace' => 'minishop2',
	'controller' => 'index',
	'haslayout' => 1,
	'lang_topics' => 'minishop2:default',
	'assets' => '',
),'',true,true);

$actions['orders'] = $modx->newObject('modAction');
$actions['orders']->fromArray(array(
	'id' => 1,
	'namespace' => 'minishop2',
	'controller' => 'controllers/mgr/orders',
	'haslayout' => 1,
	'lang_topics' => 'minishop2:default',
	'assets' => '',
),'',true,true);

$actions['settings'] = $modx->newObject('modAction');
$actions['settings']->fromArray(array(
	'id' => 1,
	'namespace' => 'minishop2',
	'controller' => 'controllers/mgr/settings',
	'haslayout' => 1,
	'lang_topics' => 'minishop2:default',
	'assets' => '',
),'',true,true);




// Menus
$menus = array();

$menus['main']= $modx->newObject('modMenu');
$menus['main']->fromArray(array(
	'text' => 'minishop2',
	'parent' => '',
	'description' => 'ms2_menu_desc',
	'menuindex' => 2,
	'params' => '',
	'handler' => 'return false;',
),'',true,true);
$menus['main']->addOne($actions['main']);

$menus['orders']= $modx->newObject('modMenu');
$menus['orders']->fromArray(array(
	'text' => 'ms2_orders',
	'parent' => 'minishop2',
	'description' => 'ms2_orders_desc',
	'menuindex' => 0,
	'params' => '',
	'handler' => '',
),'',true,true);
$menus['orders']->addOne($actions['orders']);

$menus['settings']= $modx->newObject('modMenu');
$menus['settings']->fromArray(array(
	'text' => 'ms2_settings',
	'parent' => 'minishop2',
	'description' => 'ms2_settings_desc',
	'menuindex' => 1,
	'params' => '',
	'handler' => '',
),'',true,true);
$menus['settings']->addOne($actions['settings']);

// Return menus
unset($actions);
return $menus;