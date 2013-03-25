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

$settings['ms2_category_show_nested_products']= $modx->newObject('modSystemSetting');
$settings['ms2_category_show_nested_products']->fromArray(array(
	'key' => 'ms2_category_show_nested_products',
	'value' => 'true',
	'xtype' => 'combo-boolean',
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

$settings['ms2_product_thumbnail_size']= $modx->newObject('modSystemSetting');
$settings['ms2_product_thumbnail_size']->fromArray(array(
	'key' => 'ms2_product_thumbnail_size',
	'value' => '120x90',
	'xtype' => 'textfield',
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

$settings['ms2_email_manager']= $modx->newObject('modSystemSetting');
$settings['ms2_email_manager']->fromArray(array(
	'key' => 'ms2_email_manager',
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

$settings['ms2_date_format']= $modx->newObject('modSystemSetting');
$settings['ms2_date_format']->fromArray(array(
	'key' => 'ms2_date_format',
	'value' => '%d.%m.%y <span class="gray">%H:%M</span>',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_order',
),'',true,true);

$settings['ms2_payment_paypal_api_url']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_api_url']->fromArray(array(
	'key' => 'ms2_payment_paypal_api_url',
	'value' => 'https://api-3t.paypal.com/nvp',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_checkout_url']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_checkout_url']->fromArray(array(
	'key' => 'ms2_payment_paypal_checkout_url',
	'value' => 'https://www.paypal.com/webscr?cmd=_express-checkout&token=',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_currency']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_currency']->fromArray(array(
	'key' => 'ms2_payment_paypal_currency',
	'value' => 'USD',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_user']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_user']->fromArray(array(
	'key' => 'ms2_payment_paypal_user',
	'value' => '',
	'xtype' => 'textfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_pwd']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_pwd']->fromArray(array(
	'key' => 'ms2_payment_paypal_pwd',
	'value' => '',
	'xtype' => 'text-password',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_signature']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_signature']->fromArray(array(
	'key' => 'ms2_payment_paypal_signature',
	'value' => '',
	'xtype' => 'text-password',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_success_id']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_success_id']->fromArray(array(
	'key' => 'ms2_payment_paypal_success_id',
	'value' => '',
	'xtype' => 'numberfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_cancel_id']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_cancel_id']->fromArray(array(
	'key' => 'ms2_payment_paypal_cancel_id',
	'value' => '',
	'xtype' => 'numberfield',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

$settings['ms2_payment_paypal_cancel_order']= $modx->newObject('modSystemSetting');
$settings['ms2_payment_paypal_cancel_order']->fromArray(array(
	'key' => 'ms2_payment_paypal_cancel_order',
	'value' => 'false',
	'xtype' => 'combo-boolean',
	'namespace' => 'minishop2',
	'area' => 'ms2_payment',
),'',true,true);

return $settings;