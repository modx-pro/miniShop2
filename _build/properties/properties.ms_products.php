<?php
/**
 * Properties for the msGetProducts snippet.
 *
 * @package minishop2
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'tpl'
		,'desc' => 'ms2_prop_tpl'
		,'type' => 'textfield'
		,'value' => 'tpl.msProducts.row'
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'limit'
		,'desc' => 'ms2_prop_limit'
		,'type' => 'numberfield'
		,'value' => 10
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
		'name' => 'depth'
		,'desc' => 'ms2_prop_depth'
		,'type' => 'numberfield'
		,'value' => 10
		,'lexicon' => 'minishop2:properties'
	)
	,array(
		'name' => 'sortby'
		,'desc' => 'ms2_prop_sortby'
		,'type' => 'textfield'
		,'value' => 'id'
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
	,array(
		'name' => 'parents',
		'desc' => 'ms2_prop_parents',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'resources',
		'desc' => 'ms2_prop_resources',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'fastMode',
		'desc' => 'ms2_prop_fastMode',
		'type' => 'combo-boolean',
		'value' => false,
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'includeContent',
		'desc' => 'ms2_prop_includeContent',
		'type' => 'combo-boolean',
		'value' => false,
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'includeTVs',
		'desc' => 'ms2_prop_includeTVs',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'includeThumbs',
		'desc' => 'ms2_prop_includeThumbs',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'where',
		'desc' => 'ms2_prop_where',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'link',
		'desc' => 'ms2_prop_link',
		'type' => 'numberfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'master',
		'desc' => 'ms2_prop_master',
		'type' => 'numberfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'slave',
		'desc' => 'ms2_prop_slave',
		'type' => 'numberfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'class',
		'desc' => 'ms2_prop_class',
		'type' => 'textfield',
		'value' => 'msProduct',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'tvPrefix',
		'desc' => 'ms2_prop_tvPrefix',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	)
	,array(
		'name' => 'outputSeparator'
		,'desc' => 'ms2_prop_outputSeparator'
		,'type' => 'textfield'
		,'value' => "\n"
		,'lexicon' => 'minishop2:properties'
	)

	,array(
		'name' => 'showUnpublished', 'desc' => 'ms2_prop_showUnpublished', 'type' => 'combo-boolean', 'lexicon' => 'minishop2:properties'
		,'value' => false,
	)
	,array(
		'name' => 'showDeleted', 'desc' => 'ms2_prop_showDeleted', 'type' => 'combo-boolean', 'lexicon' => 'minishop2:properties'
		,'value' => false,
	)
	,array(
		'name' => 'showHidden', 'desc' => 'ms2_prop_showHidden', 'type' => 'combo-boolean', 'lexicon' => 'minishop2:properties'
		,'value' => false,
	)
	,array(
		'name' => 'showZeroPrice', 'desc' => 'ms2_prop_showZeroPrice', 'type' => 'combo-boolean', 'lexicon' => 'minishop2:properties'
		,'value' => true,
	)
);

return $properties;