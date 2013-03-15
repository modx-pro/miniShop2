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
		,'value' => 'tpl.msGallery.row'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplOuter'
		,'desc' => 'ms2_prop_tplOuter'
		,'type' => 'textfield'
		,'value' => 'tpl.msGallery.outer'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'tplEmpty'
		,'desc' => 'ms2_prop_tplEmpty'
		,'type' => 'textfield'
		,'value' => 'tpl.msGallery.empty'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'limit'
		,'desc' => 'ms2_prop_limit'
		,'type' => 'numberfield'
		,'value' => 0
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'offset'
		,'desc' => 'ms2_prop_offset'
		,'type' => 'numberfield'
		,'value' => 0
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'sortby'
		,'desc' => 'ms2_prop_sortby'
		,'type' => 'textfield'
		,'value' => 'rank'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'sortdir',
		'desc' => 'ms2_prop_sortdir',
		'type' => 'list',
		'options' => array(
			array('text' => 'ASC','value' => 'ASC'),
			array('text' => 'DESC','value' => 'DESC'),
		),
		'value' => 'ASC',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'toPlaceholder',
		'desc' => 'ms2_prop_toPlaceholder',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'showLog',
		'desc' => 'ms2_prop_showLog',
		'type' => 'combo-boolean',
		'value' => false,
		'lexicon' => 'minishop2:properties',
	)

);

return $properties;