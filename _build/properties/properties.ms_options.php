<?php

$properties = array();

$tmp = array(
    'product' => array(
        'type' => 'numberfield',
        'value' => '',
    ),
    'options' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'sortOptionValues' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.msOptions',
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