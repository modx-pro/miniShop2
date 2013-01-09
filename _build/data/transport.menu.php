<?php
/**
 * Adds modActions and modMenus into package
 *
 * @package minishop2
 * @subpackage build
 */
$action= $modx->newObject('modAction');
$action->fromArray(array(
	'id' => 1,
	'namespace' => 'minishop2',
	'parent' => 0,
	'controller' => 'index',
	'haslayout' => 1,
	'lang_topics' => 'minishop2:default',
	'assets' => '',
),'',true,true);

/* load action into menu */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
	'text' => 'minishop2',
	'parent' => 'components',
	'description' => 'minishop2.menu_desc',
	'icon' => 'images/icons/plugin.gif',
	'menuindex' => 0,
	'params' => '',
	'handler' => '',
),'',true,true);
$menu->addOne($action);
unset($action);

return $menu;