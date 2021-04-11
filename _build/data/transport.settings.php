<?php

$settings = array();

$tmp = array(
    'mgr_tree_icon_mscategory' => array(
        'value' => 'icon icon-barcode',
        'xtype' => 'textarea',
        'area' => 'ms2_category',
        'key' => 'mgr_tree_icon_mscategory',
    ),
    'mgr_tree_icon_msproduct' => array(
        'value' => 'icon icon-tag',
        'xtype' => 'textarea',
        'area' => 'ms2_product',
        'key' => 'mgr_tree_icon_msproduct',
    ),

    'ms2_add_icon_category' => array(
        'value' => 'icon icon-folder-open',
        'xtype' => 'textfield',
        'area' => 'ms2_category',
    ),
    'ms2_add_icon_product' => array(
        'value' => 'icon icon-tag',
        'xtype' => 'textfield',
        'area' => 'ms2_category',
    ),

    'ms2_services' => array(
        'value' => '{"cart":[],"order":[],"payment":[],"delivery":[]}',
        'xtype' => 'textarea',
        'area' => 'ms2_main',
    ),
    'ms2_plugins' => array(
        'value' => '[]',
        'xtype' => 'textarea',
        'area' => 'ms2_main',
    ),
    'ms2_chunks_categories' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_main',
    ),

    'ms2_category_grid_fields' => array(
        'value' => 'id,menuindex,pagetitle,article,price,thumb,new,favorite,popular',
        'xtype' => 'textarea',
        'area' => 'ms2_category',
    ),
    'ms2_category_show_nested_products' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ),
    'ms2_category_show_comments' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ),
    'ms2_category_show_options' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ),
    'ms2_category_remember_tabs' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ),
    /*
    'ms2_category_remember_grid' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ),
    */
    'ms2_category_id_as_alias' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ),
    'ms2_category_content_default' => array(
        'value' => '',
        'xtype' => 'textarea',
        'area' => 'ms2_category',
    ),
    'ms2_template_category_default' => array(
        'value' => '',
        'xtype' => 'modx-combo-template',
        'area' => 'ms2_category',
    ),
    /*
    'ms2_product_main_fields' => array(
        'value' => 'pagetitle,longtitle,introtext,price,old_price,article,weight,content,publishedon,pub_date,unpub_date,template,parent,alias,menutitle,searchable,cacheable,richtext,uri_override,uri,hidemenu,show_in_tree',
        'xtype' => 'textarea',
        'area' => 'ms2_product',
    ),
    */
    'ms2_product_extra_fields' => array(
        'value' => 'price,old_price,article,weight,color,size,vendor,made_in,tags,new,popular,favorite',
        'xtype' => 'textarea',
        'area' => 'ms2_product',
    ),
    'ms2_product_show_comments' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_template_product_default' => array(
        'value' => '',
        'xtype' => 'modx-combo-template',
        'area' => 'ms2_product',
    ),
    'ms2_product_show_in_tree_default' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_product_source_default' => array(
        'value' => 0,
        'xtype' => 'modx-combo-source',
        'area' => 'ms2_product',
    ),
    'ms2_product_thumbnail_size' => array(
        'value' => 'small',
        'xtype' => 'textfield',
        'area' => 'ms2_product',
    ),
    /*
    'ms2_product_vertical_tabs' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    */
    'ms2_product_remember_tabs' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_product_id_as_alias' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_price_format' => array(
        'value' => '[2, ".", " "]',
        'xtype' => 'textfield',
        'area' => 'ms2_product',
    ),
    'ms2_weight_format' => array(
        'value' => '[3, ".", " "]',
        'xtype' => 'textfield',
        'area' => 'ms2_product',
    ),
    /*
    'ms2_price_snippet' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_product'
    )
    */
    'ms2_price_format_no_zeros' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    /*
    'ms2_weight_snippet' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_product'
    )
    */
    'ms2_weight_format_no_zeros' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_product_tab_extra' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_product_tab_gallery' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_product_tab_links' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_product_tab_options' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),
    'ms2_product_tab_categories' => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ),

    'ms2_cart_handler_class' => array(
        'value' => 'msCartHandler',
        'xtype' => 'textfield',
        'area' => 'ms2_cart',
    ),
    'ms2_cart_context' => array(
        'value' => '',
        'xtype' => 'combo-boolean',
        'area' => 'ms2_cart',
    ),

    'ms2_order_format_num' => array(
        'value' => '%y%m',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ),
    'ms2_order_format_num_separator' => array(
        'value' => '/',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ),
    'ms2_order_grid_fields' => array(
        'value' => 'id,num,customer,status,cost,weight,delivery,payment,createdon,updatedon,comment',
        'xtype' => 'textarea',
        'area' => 'ms2_order',
    ),
    'ms2_order_address_fields' => array(
        'xtype' => 'textarea',
        'value' => 'receiver,phone,index,country,region,city,metro,street,building,entrance,floor,room,comment,text_address',
        'area' => 'ms2_order',
    ),
    'ms2_order_product_fields' => array(
        'xtype' => 'textarea',
        'value' => 'product_pagetitle,product_article,weight,price,count,cost',
        'area' => 'ms2_order',
    ),
    'ms2_order_product_options' => array(
        'xtype' => 'textarea',
        'value' => 'size,color',
        'area' => 'ms2_order',
    ),

    'ms2_order_handler_class' => array(
        'value' => 'msOrderHandler',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ),
    'ms2_order_user_groups' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ),
    'ms2_date_format' => array(
        'value' => '%d.%m.%y <span class="gray">%H:%M</span>',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ),
    'ms2_email_manager' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ),

    'ms2_frontend_css' => array(
        'value' => '[[+cssUrl]]web/default.css',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ),
    'ms2_frontend_message_css' => array(
        'value' => '[[+cssUrl]]web/lib/jquery.jgrowl.min.css',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ),
    'ms2_frontend_js' => array(
        'value' => '[[+jsUrl]]web/default.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ),
    'ms2_frontend_message_js' => array(
        'value' => '[[+jsUrl]]web/lib/jquery.jgrowl.min.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ),
    'ms2_frontend_message_js_settings' => array(
        'value' => '[[+jsUrl]]web/message_settings.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ),

    'ms2_payment_paypal_api_url' => array(
        'value' => 'https://api-3t.paypal.com/nvp',
        'xtype' => 'textfield',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_checkout_url' => array(
        'value' => 'https://www.paypal.com/webscr?cmd=_express-checkout&token=',
        'xtype' => 'textfield',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_currency' => array(
        'value' => 'USD',
        'xtype' => 'textfield',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_user' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_pwd' => array(
        'value' => '',
        'xtype' => 'text-password',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_signature' => array(
        'value' => '',
        'xtype' => 'text-password',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_success_id' => array(
        'value' => '',
        'xtype' => 'numberfield',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_cancel_id' => array(
        'value' => '',
        'xtype' => 'numberfield',
        'area' => 'ms2_payment',
    ),
    'ms2_payment_paypal_cancel_order' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_payment',
    ),
);

/** @var modX $modx */
foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => $k,
            'namespace' => 'minishop2',
            'editedon' => date('Y-m-d H:i:s'),
        ),
        $v
    ), '', true, true);
    $settings[] = $setting;
}

return $settings;
