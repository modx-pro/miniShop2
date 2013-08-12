<?php

$menus = array();

$tmp = array(
	'main' => array(
		'description' => 'ms2_menu_desc',
		'handler' => 'return false;',
		'action' => array(
			'controller' => 'index'
		)
	),
	'orders' => array(
		'description' => 'ms2_orders_desc',
		'action' => array(
			'controller' => 'controllers/mgr/orders'
		)
	),
	'settings' => array(
		'description' => 'ms2_settings_desc',
		'action' => array(
			'controller' => 'controllers/mgr/settings'
		)
	),
);

$i = 0;
foreach ($tmp as $k => $v) {
	$action = null;
	if (!empty($v['action'])) {
		/* @var modAction $action */
		$action = $modx->newObject('modAction');
		$action->fromArray(array_merge(array(
			'id' => 1,
			'namespace' => PKG_NAME_LOWER,
			'parent' => 0,
			'haslayout' => 1,
			'lang_topics' => PKG_NAME_LOWER . ':default',
			'assets' => '',
		), $v['action']), '', true, true);
		unset($v['action']);
	}

	/* @var modMenu $menu */
	$menu = $modx->newObject('modMenu');
	$menu->fromArray(array_merge(array(
		'text' => $k,
		'parent' => 'components',
		'icon' => 'images/icons/plugin.gif',
		'menuindex' => $i,
		'params' => '',
		'handler' => '',
	), $v), '', true, true);

	if (!empty($action) && $action instanceof modAction) {
		//$menu->addOne($action);
	}

	$menus[] = $menu;
	$i++;
}

unset($action, $menu, $i);
return $menus;