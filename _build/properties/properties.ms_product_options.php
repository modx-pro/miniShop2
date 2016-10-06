<?php

$properties = array();

$tmp = array(
    'product' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.msProductOptions',
    ),
    'ignoreOptions' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'sortOptions' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'groups' => array(
        'type' => 'textfield',
        'value' => '',
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
