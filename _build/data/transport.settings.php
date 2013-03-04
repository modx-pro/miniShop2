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
	'value' => 'pagetitle,article,price,weight,thumb,new,favorite,popular',
	'xtype' => 'textarea',
	'namespace' => 'minishop2',
	'area' => 'ms2_category',
),'',true,true);

$settings['ms2_product_main_fields']= $modx->newObject('modSystemSetting');
$settings['ms2_product_main_fields']->fromArray(array(
	'key' => 'ms2_product_main_fields',
	'value' => 'pagetitle,longtitle,introtext,price,old_price,article,weight,content,publishedon,pub_date,unpub_date,template,parent,alias,menutitle,searchable,cacheable,richtext,uri_override,uri,hidemenu,show_in_tree',
	'xtype' => 'textarea',
	'namespace' => 'minishop2',
	'area' => 'ms2_product',
),'',true,true);

$settings['ms2_product_extra_fields']= $modx->newObject('modSystemSetting');
$settings['ms2_product_extra_fields']->fromArray(array(
	'key' => 'ms2_product_extra_fields',
	'value' => 'color,size,vendor,new,popular,favorite,made_in,tags',
	'xtype' => 'textarea',
	'namespace' => 'minishop2',
	'area' => 'ms2_product',
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

$settings['ms2_template_product_default']= $modx->newObject('modSystemSetting');
$settings['ms2_template_product_default']->fromArray(array(
	'key' => 'ms2_template_product_default',
	'value' => '',
	'xtype' => 'modx-combo-template',
	'namespace' => 'minishop2',
	'area' => 'ms2_product',
),'',true,true);

$settings['ms2_product_show_in_tree_default']= $modx->newObject('modSystemSetting');
$settings['ms2_product_show_in_tree_default']->fromArray(array(
	'key' => 'ms2_product_show_in_tree_default',
	'value' => 'false',
	'xtype' => 'combo-boolean',
	'namespace' => 'minishop2',
	'area' => 'ms2_product',
),'',true,true);

$settings['ms2_product_source_default']= $modx->newObject('modSystemSetting');
$settings['ms2_product_source_default']->fromArray(array(
	'key' => 'ms2_product_source_default',
	'value' => '1',
	'xtype' => 'modx-combo-source',
	'namespace' => 'minishop2',
	'area' => 'ms2_product',
),'',true,true);

$settings['ms2_product_vertical_tabs']= $modx->newObject('modSystemSetting');
$settings['ms2_product_vertical_tabs']->fromArray(array(
	'key' => 'ms2_product_vertical_tabs',
	'value' => true,
	'xtype' => 'combo-boolean',
	'namespace' => 'minishop2',
	'area' => 'ms2_product',
),'',true,true);

$settings['ms2_cart_handler_class']= $modx->newObject('modSystemSetting');
$settings['ms2_cart_handler_class']->fromArray(array(
	'key' => 'ms2_cart_handler_class',
	'value' => 'msCartHandler',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_cart',
),'',true,true);

$settings['ms2_order_handler_class']= $modx->newObject('modSystemSetting');
$settings['ms2_order_handler_class']->fromArray(array(
	'key' => 'ms2_order_handler_class',
	'value' => 'msOrderHandler',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_order',
),'',true,true);

$settings['ms2_order_user_groups']= $modx->newObject('modSystemSetting');
$settings['ms2_order_user_groups']->fromArray(array(
	'key' => 'ms2_order_user_groups',
	'value' => '',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_order',
),'',true,true);

$settings['ms2_frontend_css']= $modx->newObject('modSystemSetting');
$settings['ms2_frontend_css']->fromArray(array(
	'key' => 'ms2_frontend_css',
	'value' => '[[+cssUrl]]web/default.css',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_frontend',
),'',true,true);

$settings['ms2_frontend_js']= $modx->newObject('modSystemSetting');
$settings['ms2_frontend_js']->fromArray(array(
	'key' => 'ms2_frontend_js',
	'value' => '[[+jsUrl]]web/default.js',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_frontend',
),'',true,true);

return $settings;