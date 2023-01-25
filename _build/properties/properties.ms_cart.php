<?php

$properties = [];

$tmp = [
    'tpl' => [
        'type' => 'textfield',
        'value' => 'tpl.msCart',
    ],
    'includeTVs' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'includeThumbs' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'toPlaceholder' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'showLog' => [
        'type' => 'combo-boolean',
        'value' => false,
    ],
];

foreach ($tmp as $k => $v) {
    $properties[] = array_merge([
        'name' => $k,
        'desc' => 'ms2_prop_' . $k,
        'lexicon' => 'minishop2:properties',
    ], $v);
}

return $properties;
