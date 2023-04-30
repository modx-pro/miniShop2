<?php

$properties = [];

$tmp = [
    'tpl' => [
        'type' => 'textfield',
        'value' => 'tpl.msProducts.row',
    ],
    'limit' => [
        'type' => 'numberfield',
        'value' => 10,
    ],
    'offset' => [
        'type' => 'numberfield',
        'value' => 0,
    ],
    'depth' => [
        'type' => 'numberfield',
        'value' => 10,
    ],
    'sortby' => [
        'type' => 'textfield',
        'value' => 'id',
    ],
    'sortbyOptions' => [
        'type' => 'textfield',
        'value' => '',
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
    'toSeparatePlaceholders' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'showLog' => [
        'type' => 'combo-boolean',
        'value' => false,
    ],
    'parents' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'resources' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'includeContent' => [
        'type' => 'combo-boolean',
        'value' => false,
    ],
    'includeTVs' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'includeThumbs' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'optionFilters' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'where' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'link' => [
        'type' => 'numberfield',
        'value' => '',
    ],
    'master' => [
        'type' => 'numberfield',
        'value' => '',
    ],
    'slave' => [
        'type' => 'numberfield',
        'value' => '',
    ],
    'tvPrefix' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'outputSeparator' => [
        'type' => 'textfield',
        'value' => "\n",
    ],
    'returnIds' => [
        'type' => 'combo-boolean',
        'value' => false,
    ],
    'return' => [
        'type' => 'textfield',
        'value' => 'data',
    ],
    'showUnpublished' => [
        'type' => 'combo-boolean',
        'value' => false,
    ],
    'showDeleted' => [
        'type' => 'combo-boolean',
        'value' => false,
    ],
    'showHidden' => [
        'type' => 'combo-boolean',
        'value' => true,
    ],
    'showZeroPrice' => [
        'type' => 'combo-boolean',
        'value' => true,
    ],
    'wrapIfEmpty' => [
        'type' => 'combo-boolean',
        'value' => true,
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
