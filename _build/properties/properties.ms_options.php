<?php
/**
 * Properties for the msGetProducts snippet.
 *
 * @package minishop2
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'product'
		,'desc' => 'ms2_prop_product'
		,'type' => 'numberfield'
		,'value' => ''
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplRow'
		,'desc' => 'ms2_prop_tplRow'
		,'type' => 'textfield'
		,'value' => 'tpl.msOptions.row'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplOuter'
		,'desc' => 'ms2_prop_tplOuter'
		,'type' => 'textfield'
		,'value' => 'tpl.msOptions.outer'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplEmpty'
		,'desc' => 'ms2_prop_tplEmpty'
		,'type' => 'textfield'
		,'value' => 'tpl.msOptions.empty'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'name'
		,'desc' => 'ms2_prop_optionName'
		,'type' => 'textfield'
		,'value' => ''
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'selected'
		,'desc' => 'ms2_prop_optionSelected'
		,'type' => 'textfield'
		,'value' => ''
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'outputSeparator'
		,'desc' => 'ms2_prop_outputSeparator'
		,'type' => 'textfield'
		,'value' => "\n"
		,'lexicon' => 'minishop2:properties'
	)
);

return $properties;