<?php

$properties = [];

$tmp = [
    'product' => [
        'type' => 'numberfield',
        'value' => '',
    ],
    'tpl' => [
        'type' => 'textfield',
        'value' => 'tpl.msGallery',
    ],
    'limit' => [
        'type' => 'numberfield',
        'value' => 0,
    ],
    'offset' => [
        'type' => 'numberfield',
        'value' => 0,
    ],
    'sortby' => [
        'type' => 'textfield',
        'value' => 'rank',
    ],
    'sortdir' => [
        'type' => 'list',
        'options' => [
            ['text' => 'ASC', 'value' => 'ASC'],
            ['text' => 'DESC', 'value' => 'DESC'],
        ],
        'value' => 'ASC',
    ],
    'toPlaceholder' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'showLog' => [
        'type' => 'combo-boolean',
        'value' => false,
    ],
    'where' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'filetype' => [
        'type' => 'textfield',
        'value' => '',
        'desc' => 'ms2_prop_filetype',
    ],
    'return' => [
        'type' => 'textfield',
        'value' => 'tpl',
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
