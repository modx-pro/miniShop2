<?php

$properties = [];

$tmp = [
    'product' => [
        'type' => 'numberfield',
        'value' => '',
    ],
    'options' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'sortOptionValues' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'tpl' => [
        'type' => 'textfield',
        'value' => 'tpl.msOptions',
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
