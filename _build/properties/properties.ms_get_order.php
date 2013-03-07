<?php
/**
 * Properties for the msGetProducts snippet.
 *
 * @package minishop2
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'tplRow'
		,'desc' => 'ms2_prop_tplRow'
		,'type' => 'textfield'
		,'value' => 'tpl.msGetOrder.row'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplOuter'
		,'desc' => 'ms2_prop_tplOuter'
		,'type' => 'textfield'
		,'value' => ''
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplEmpty'
		,'desc' => 'ms2_prop_tplEmpty'
		,'type' => 'textfield'
		,'value' => ''
		,'lexicon' => 'minishop2:properties'
	)
);

return $properties;