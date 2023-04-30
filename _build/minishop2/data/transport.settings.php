<?php

$settings = [];

$tmp = [
    'mgr_tree_icon_mscategory' => [
        'value' => 'icon icon-barcode',
        'xtype' => 'textarea',
        'area' => 'ms2_category',
        'key' => 'mgr_tree_icon_mscategory',
    ],
    'mgr_tree_icon_msproduct' => [
        'value' => 'icon icon-tag',
        'xtype' => 'textarea',
        'area' => 'ms2_product',
        'key' => 'mgr_tree_icon_msproduct',
    ],
    'ms2_services' => [
        'value' => '{"cart":[],"order":[],"payment":[],"delivery":[]}',
        'xtype' => 'textarea',
        'area' => 'ms2_main',
    ],
    'ms2_plugins' => [
        'value' => '[]',
        'xtype' => 'textarea',
        'area' => 'ms2_main',
    ],
    'ms2_chunks_categories' => [
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_main',
    ],
    'ms2_tmp_storage' => [
        'value' => 'session',
        'xtype' => 'textfield',
        'area' => 'ms2_main',
    ],
    'ms2_use_scheduler' => [
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_main',
    ],

    'ms2_category_grid_fields' => [
        'value' => 'id,menuindex,pagetitle,article,price,thumb,new,favorite,popular',
        'xtype' => 'textarea',
        'area' => 'ms2_category',
    ],
    'ms2_category_show_nested_products' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ],
    'ms2_category_show_comments' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ],
    'ms2_category_show_options' => [
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ],
    'ms2_category_remember_tabs' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ],
    'ms2_category_id_as_alias' => [
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_category',
    ],
    'ms2_category_content_default' => [
        'value' => '',
        'xtype' => 'textarea',
        'area' => 'ms2_category',
    ],
    'ms2_template_category_default' => [
        'value' => '',
        'xtype' => 'modx-combo-template',
        'area' => 'ms2_category',
    ],
    'ms2_product_extra_fields' => [
        'value' => 'price,old_price,article,weight,color,size,vendor,made_in,tags,new,popular,favorite',
        'xtype' => 'textarea',
        'area' => 'ms2_product',
    ],
    'ms2_product_show_comments' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_template_product_default' => [
        'value' => '',
        'xtype' => 'modx-combo-template',
        'area' => 'ms2_product',
    ],
    'ms2_product_show_in_tree_default' => [
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_product_source_default' => [
        'value' => 0,
        'xtype' => 'modx-combo-source',
        'area' => 'ms2_product',
    ],
    'ms2_product_thumbnail_default' => [
        'value' => '{assets_url}components/minishop2/img/mgr/ms2_thumb.png',
        'xtype' => 'textfield',
        'area' => 'ms2_product',
    ],
    'ms2_product_thumbnail_size' => [
        'value' => 'small',
        'xtype' => 'textfield',
        'area' => 'ms2_product',
    ],
    'ms2_product_remember_tabs' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_product_id_as_alias' => [
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_price_format' => [
        'value' => '[2, ".", " "]',
        'xtype' => 'textfield',
        'area' => 'ms2_product',
    ],
    'ms2_weight_format' => [
        'value' => '[3, ".", " "]',
        'xtype' => 'textfield',
        'area' => 'ms2_product',
    ],
    'ms2_price_format_no_zeros' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_weight_format_no_zeros' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_product_tab_extra' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_product_tab_gallery' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_product_tab_links' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_product_tab_options' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],
    'ms2_product_tab_categories' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_product',
    ],

    'ms2_cart_handler_class' => [
        'value' => 'msCartHandler',
        'xtype' => 'textfield',
        'area' => 'ms2_cart',
    ],
    'ms2_cart_context' => [
        'value' => '',
        'xtype' => 'combo-boolean',
        'area' => 'ms2_cart',
    ],
    'ms2_cart_max_count' => [
        'value' => 1000,
        'xtype' => 'numberfield',
        'area' => 'ms2_cart',
    ],
    'ms2_cart_product_key_fields' => [
        'value' => 'id,options',
        'xtype' => 'textfield',
        'area' => 'ms2_cart',
    ],

    'ms2_order_format_phone' => [
        'value' => '/[^-+()0-9]/u',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],
    'ms2_order_format_num' => [
        'value' => '%y%m',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],
    'ms2_order_format_num_separator' => [
        'value' => '/',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],
    'ms2_order_grid_fields' => [
        'value' => 'id,num,customer,status,cost,weight,delivery,payment,createdon,updatedon,comment',
        'xtype' => 'textarea',
        'area' => 'ms2_order',
    ],
    'ms2_order_address_fields' => [
        'xtype' => 'textarea',
        'value' => 'receiver,phone,email,index,country,region,city,metro,street,building,entrance,floor,room,comment,text_address',
        'area' => 'ms2_order',
    ],
    'ms2_order_product_fields' => [
        'xtype' => 'textarea',
        'value' => 'product_pagetitle,vendor_name,product_article,weight,price,count,cost',
        'area' => 'ms2_order',
    ],
    'ms2_order_product_options' => [
        'xtype' => 'textarea',
        'value' => 'size,color',
        'area' => 'ms2_order',
    ],

    'ms2_order_tv_list' => [
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],

    'ms2_order_handler_class' => [
        'value' => 'msOrderHandler',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],
    'ms2_order_user_groups' => [
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],
    'ms2_date_format' => [
        'value' => '%d.%m.%y <span class="gray">%H:%M</span>',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],
    'ms2_email_manager' => [
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_order',
    ],

    'ms2_frontend_css' => [
        'value' => '[[+cssUrl]]web/default.css',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_frontend_message_css' => [
        'value' => '[[+cssUrl]]web/lib/jquery.jgrowl.min.css',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_frontend_js' => [
        'value' => '[[+jsUrl]]web/default.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_frontend_message_js' => [
        'value' => '[[+jsUrl]]web/lib/jquery.jgrowl.min.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_frontend_message_js_settings' => [
        'value' => '[[+jsUrl]]web/message_settings.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_register_frontend' => [
        'value' => true,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_frontend',
    ],

    'ms2_toggle_js_type' => [
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_frontend',
    ],
    'ms2_vanila_js' => [
        'value' => '[[+jsUrl]]web/vanilajs/default.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_frontend_notify_js_settings' => [
        'value' => '[[+jsUrl]]web/vanilajs/message_settings.json',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_cart_js_class_path' => [
        'value' => '[[+jsUrl]]web/vanilajs/modules/mscart.class.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_cart_js_class_name' => [
        'value' => 'MsCart',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_order_js_class_path' => [
        'value' => '[[+jsUrl]]web/vanilajs/modules/msorder.class.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_order_js_class_name' => [
        'value' => 'MsOrder',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_notify_js_class_path' => [
        'value' => '[[+jsUrl]]web/vanilajs/modules/msizitoast.class.js',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],
    'ms2_notify_js_class_name' => [
        'value' => 'MsIziToast',
        'xtype' => 'textfield',
        'area' => 'ms2_frontend',
    ],

    'ms2_status_draft' => [
        'value' => 0,
        'xtype' => 'numberfield',
        'area' => 'ms2_statuses',
    ],
    'ms2_status_new' => [
        'value' => 0,
        'xtype' => 'numberfield',
        'area' => 'ms2_statuses',
    ],
    'ms2_status_paid' => [
        'value' => 0,
        'xtype' => 'numberfield',
        'area' => 'ms2_statuses',
    ],
    'ms2_status_canceled' => [
        'value' => 0,
        'xtype' => 'numberfield',
        'area' => 'ms2_statuses',
    ],
    'ms2_status_for_stat' => [
        'value' => '2,3',
        'xtype' => 'textfield',
        'area' => 'ms2_statuses',
    ]
];

/** @var modX $modx */
foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(
        array_merge(
            [
                'key' => $k,
                'namespace' => 'minishop2',
                'editedon' => date('Y-m-d H:i:s'),
            ],
            $v
        ),
        '',
        true,
        true
    );
    $settings[] = $setting;
}

return $settings;
