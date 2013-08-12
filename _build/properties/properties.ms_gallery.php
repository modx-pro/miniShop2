<?php

$properties = array();

$tmp = array(
	'product' => array(
		'type' => 'numberfield',
		'value' => '',
	),
	'tplRow' => array(
		'type' => 'textfield',
		'value' => 'tpl.msGallery.row',
	),
	'tplOuter' => array(
		'type' => 'textfield',
		'value' => 'tpl.msGallery.outer',
	),
	'tplEmpty' => array(
		'type' => 'textfield',
		'value' => 'tpl.msGallery.empty',
	),
	'limit' => array(
		'type' => 'numberfield',
		'value' => 0,
	),
	'offset' => array(
		'type' => 'numberfield',
		'value' => 0,
	),
	'sortby' => array(
		'type' => 'textfield',
		'value' => 'rank',
	),
	'sortdir' => array(
		'type' => 'list',
		'options' => array(
			array(
				'text' => 'ASC',
				'value' => 'ASC'
			),
		),
		'value' => 'ASC',
	),
	'toPlaceholder' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'showLog' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'where' => array(
		'type' => 'textfield',
		'value' => '',
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