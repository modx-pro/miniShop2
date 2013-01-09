<?php
/**
 * Properties for the miniShop2 snippet.
 *
 * @package minishop2
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'tpl',
		'desc' => 'prop_minishop2.tpl_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => 'tpl.miniShop2.item',
		'lexicon' => 'minishop2:properties',
	),
	array(
		'name' => 'sortBy',
		'desc' => 'prop_minishop2.sortby_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => 'name',
		'lexicon' => 'minishop2:properties',
	),
	array(
		'name' => 'sortDir',
		'desc' => 'prop_minishop2.sortdir_desc',
		'type' => 'list',
		'options' => array(
			array('text' => 'ASC','value' => 'ASC'),
			array('text' => 'DESC','value' => 'DESC'),
		),
		'value' => 'ASC',
		'lexicon' => 'minishop2:properties',
	),
	array(
		'name' => 'limit',
		'desc' => 'prop_minishop2.limit_desc',
		'type' => 'numberfield',
		'options' => '',
		'value' => 5,
		'lexicon' => 'minishop2:properties',
	),
	array(
		'name' => 'outputSeparator',
		'desc' => 'prop_minishop2.outputseparator_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	),
	array(
		'name' => 'toPlaceholder',
		'desc' => 'prop_minishop2.toplaceholder_desc',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => false,
		'lexicon' => 'minishop2:properties',
	),
/*
	array(
		'name' => '',
		'desc' => 'prop_minishop2.',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'minishop2:properties',
	),
	*/
);

return $properties;