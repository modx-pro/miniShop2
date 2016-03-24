<?php

$properties = array();

$tmp = array(
    'product' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'tplRow' => array(
        'type' => 'textfield',
        'value' => 'tpl.msProductOptions.row',
    ),
    'tplOuter' => array(
        'type' => 'textfield',
        'value' => 'tpl.msProductOptions.outer',
    ),
    'valuesSeparator' => array(
        'type' => 'textfield',
        'value' => ', ',
    ),
    'outputSeparator' => array(
        'type' => 'textfield',
        'value' => "\n",
    ),
    'ignoreOptions' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'hideEmpty' => array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'groups' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'tplValue' => array(
        'type' => 'textfield',
        'value' => '@INLINE [[+value]]',
    ),
);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(array(
        'name' => $k,
        'desc' => 'ms2_prop_' . $k,
        'lexicon' => 'minishop2:properties',
    ), $v);
}

return $properties;
