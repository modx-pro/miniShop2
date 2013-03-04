<?php
/**
 * Properties for the msGetProducts snippet.
 *
 * @package minishop2
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'tplOuter'
		,'desc' => 'ms2_prop_tplOuter'
		,'type' => 'textfield'
		,'value' => 'tpl.msOrder.outer'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplPayment'
		,'desc' => 'ms2_prop_tplPayment'
		,'type' => 'textfield'
		,'value' => 'tpl.msOrder.payment'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplDelivery'
		,'desc' => 'ms2_prop_tplDelivery'
		,'type' => 'textfield'
		,'value' => 'tpl.msOrder.delivery'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplEmpty'
		,'desc' => 'ms2_prop_tplEmpty'
		,'type' => 'textfield'
		,'value' => ''
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplSuccess'
		,'desc' => 'ms2_prop_tplSuccess'
		,'type' => 'textfield'
		,'value' => 'tpl.msOrder.success'
		,'lexicon' => 'minishop2:properties'
	)

);

return $properties;