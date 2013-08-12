<?php

$properties = array();

$tmp = array(
	'tplOuter' => array(
		'type' => 'textfield',
		'value' => 'tpl.msOrder.outer',
	),
	'tplPayment' => array(
		'type' => 'textfield',
		'value' => 'tpl.msOrder.payment',
	),
	'tplDelivery' => array(
		'type' => 'textfield',
		'value' => 'tpl.msOrder.delivery',
	),
	'tplEmpty' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'tplSuccess' => array(
		'type' => 'textfield',
		'value' => 'tpl.msOrder.success',
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