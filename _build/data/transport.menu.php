<?php

$menus = array();

$tmp = array(
	'minishop2' => array(
		'description' => 'ms2_menu_desc',
		'parent' => 'components',
		//'handler' => 'return false;',
		'icon' => '<i class="icon-shopping-cart icon icon-large"></i>',
		'action' => array(
			'controller' => 'index'
		)
	),
	'ms2_orders' => array(
		'description' => 'ms2_orders_desc',
		'parent' => 'minishop2',
		'menuindex' => 0,
		'action' => array(
			'controller' => 'controllers/mgr/orders'
		)
	),
	'ms2_settings' => array(
		'description' => 'ms2_settings_desc',
		'parent' => 'minishop2',
		'menuindex' => 1,
		'action' => array(
			'controller' => 'controllers/mgr/settings'
		)
	),
);

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
		'namespace' => PKG_NAME_LOWER,
		'icon' => '',
		'menuindex' => 0,
		'params' => '',
		'handler' => '',
	), $v), '', true, true);

	if (!empty($action) && $action instanceof modAction) {
		$menu->addOne($action);
	}

	$menus[] = $menu;
}

unset($action, $menu, $i);
return $menus;