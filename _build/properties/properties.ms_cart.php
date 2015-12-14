<?php

$properties = array();

$tmp = array(
    'tplRow' => array(
        'type' => 'textfield',
        'value' => 'tpl.msCart.row',
    ),
    'tplOuter' => array(
        'type' => 'textfield',
        'value' => 'tpl.msCart.outer',
    ),
    'tplEmpty' => array(
        'type' => 'textfield',
        'value' => 'tpl.msCart.empty',
    ),
    'includeTVs' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'includeThumbs' => array(
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