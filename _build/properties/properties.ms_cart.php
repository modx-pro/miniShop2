<?php

$properties = array();

$tmp = array(
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.msCart',
    ),
    'includeTVs' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'includeThumbs' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'toPlaceholder' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'showLog' => array(
        'type' => 'combo-boolean',
        'value' => false,
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
