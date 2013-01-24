<?php
/**
 * Loads system settings into build
 *
 * @package minishop2
 * @subpackage build
 */
$settings = array();

$settings['ms2_category_grid_fields']= $modx->newObject('modSystemSetting');
$settings['ms2_category_grid_fields']->fromArray(array(
	'key' => 'ms2_category_grid_fields',
	'value' => 'id,pagetitle,article,price,weight,image',
	'xtype' => 'textarea',
	'namespace' => 'minishop2',
	'area' => 'ms2_category',
),'',true,true);

$settings['ms2_category_show_comments']= $modx->newObject('modSystemSetting');
$settings['ms2_category_show_comments']->fromArray(array(
	'key' => 'ms2_category_show_comments',
	'value' => 'true',
	'xtype' => 'combo-boolean',
	'namespace' => 'minishop2',
	'area' => 'ms2_category',
),'',true,true);

$settings['ms2_product_show_comments']= $modx->newObject('modSystemSetting');
$settings['ms2_product_show_comments']->fromArray(array(
	'key' => 'ms2_product_show_comments',
	'value' => 'true',
	'xtype' => 'combo-boolean',
	'namespace' => 'minishop2',
	'area' => 'ms2_product',
),'',true,true);


return $settings;