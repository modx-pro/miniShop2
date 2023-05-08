<?php

/** @var modX $modx */
$menus = [];

$tmp = [
    'minishop2' => [
        'description' => 'ms2_menu_desc',
        'icon' => '<i class="icon-shopping-cart icon icon-large"></i>',
        'action' => 'mgr/orders',
    ],
    'ms2_orders' => [
        'description' => 'ms2_orders_desc',
        'parent' => 'minishop2',
        'menuindex' => 0,
        'action' => 'mgr/orders',
    ],
    'ms2_settings' => [
        'description' => 'ms2_settings_desc',
        'parent' => 'minishop2',
        'menuindex' => 1,
        'action' => 'mgr/settings',
    ],
    'ms2_system_settings' => [
        'description' => 'ms2_system_settings_desc',
        'parent' => 'minishop2',
        'menuindex' => 2,
        'namespace' => 'core',
        'permissions' => 'settings',
        'action' => 'system/settings',
        'params' => '&ns=minishop2',
    ],
    'ms2_help' => [
        'description' => 'ms2_help_desc',
        'parent' => 'minishop2',
        'menuindex' => 3,
        'action' => 'mgr/help',
    ],
    'ms2_utilites' => [
        'description' => 'ms2_utilites_desc',
        'parent' => 'minishop2',
        'menuindex' => 4,
        'action' => 'mgr/utilites',
    ],
];

foreach ($tmp as $k => $v) {
    /** @var modMenu $menu */
    $menu = $modx->newObject('modMenu');
    $menu->fromArray(
        array_merge([
            'text' => $k,
            'parent' => 'components',
            'namespace' => PKG_NAME_LOWER,
            'icon' => '',
            'menuindex' => 0,
            'params' => '',
            'handler' => '',
        ], $v),
        '',
        true,
        true
    );
    $menus[] = $menu;
}
unset($menu, $i);

return $menus;
