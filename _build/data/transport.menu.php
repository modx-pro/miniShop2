<?php

$menus = array();

$tmp = array(
    'minishop2' => array(
        'description' => 'ms2_menu_desc',
        'icon' => '<i class="icon-shopping-cart icon icon-large"></i>',
        'action' => 'mgr/orders',
    ),
    'ms2_orders' => array(
        'description' => 'ms2_orders_desc',
        'parent' => 'minishop2',
        'menuindex' => 0,
        'action' => 'mgr/orders',
    ),
    'ms2_settings' => array(
        'description' => 'ms2_settings_desc',
        'parent' => 'minishop2',
        'menuindex' => 1,
        'action' => 'mgr/settings',
    ),
);

foreach ($tmp as $k => $v) {
    /** @var modMenu $menu */
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
    $menus[] = $menu;
}
unset($menu, $i);

return $menus;