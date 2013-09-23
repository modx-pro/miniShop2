<?php

$properties = array();

$tmp = array(
	'product' => array(
		'type' => 'numberfield',
		'value' => '',
	),
	'tplRow' => array(
		'type' => 'textfield',
		'value' => 'tpl.msOptions.row',
	),
	'tplOuter' => array(
		'type' => 'textfield',
		'value' => 'tpl.msOptions.outer',
	),
	'tplEmpty' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'name' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'selected' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'outputSeparator' => array(
		'type' => 'textfield',
		'value' => "\n",
	)
);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(array(
			'name' => $k,
			'desc' => 'ms2_prop_' . $k,
			'lexicon' => 'minishop2:properties',
		), $v
	);
}

return $properties;