<?php

/** @var modX $modx */
$plugins = [];

$tmp = [
    'miniShop2' => [
        'file' => 'minishop2',
        'description' => '',
        'events' => [
            'OnMODXInit',
            'OnHandleRequest',
            'OnLoadWebDocument',
            'OnWebPageInit',
            'OnUserSave',
            'msOnChangeOrderStatus',
            'OnManagerPageBeforeRender',
        ],
    ],
];

foreach ($tmp as $k => $v) {
    /** @var modPlugin $plugin */
    $plugin = $modx->newObject('modPlugin');
    /** @var array $sources */
    $plugin->fromArray([
        'id' => 0,
        'name' => $k,
        'category' => 0,
        'description' => @$v['description'],
        'plugincode' => getSnippetContent($sources['source_core'] . '/elements/plugins/plugin.' . $v['file'] . '.php'),
        'static' => BUILD_PLUGIN_STATIC,
        'source' => 1,
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/plugin.' . $v['file'] . '.php',
    ], '', true, true);

    $events = [];
    if (!empty($v['events'])) {
        foreach ($v['events'] as $k2 => $v2) {
            /** @var modPluginEvent $event */
            $event = $modx->newObject('modPluginEvent');
            $event->fromArray([
                'event' => $v2,
                'priority' => 0,
                'propertyset' => 0,
            ], '', true, true);
            $events[] = $event;
        }
        unset($v['events']);
    }

    if (!empty($events)) {
        $plugin->addMany($events);
    }

    $plugins[] = $plugin;
}
unset($tmp, $properties);

return $plugins;
