<?php

$properties = [];

$tmp = [
    'product' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'tpl' => [
        'type' => 'textfield',
        'value' => 'tpl.msProductOptions',
    ],
    'ignoreGroups' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'ignoreOptions' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'onlyOptions' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'sortGroups' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'sortOptions' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'sortOptionValues' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'groups' => [
        'type' => 'textfield',
        'value' => '',
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
