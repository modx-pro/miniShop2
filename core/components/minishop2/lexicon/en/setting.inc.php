<?php
/**
 * Settings English Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */
$_lang['area_ms2_main'] = 'Main settings';
$_lang['area_ms2_category'] = 'Category of the goods';
$_lang['area_ms2_product'] = 'Product';
$_lang['area_ms2_gallery'] = 'Gallery';
$_lang['area_ms2_cart'] = 'Cart';
$_lang['area_ms2_order'] = 'Order';
$_lang['area_ms2_frontend'] = 'Frontend';
$_lang['area_ms2_payment'] = 'Payments';

$_lang['setting_ms2_services'] = 'Store services';
$_lang['setting_ms2_services_desc'] = 'Array with registered classes for cart, order, delivery and payment. Used by third-party extras to load their functionality.';
$_lang['setting_ms2_plugins'] = 'Store plugins';
$_lang['setting_ms2_plugins_desc'] = 'Array with registered plugins for extension objects of store model: products, customer profiles, etc.';

$_lang['setting_ms2_category_grid_fields'] = 'Fields of the table with goods';
$_lang['setting_ms2_category_grid_fields_desc'] = 'Comma separated list of visible fields in the table of goods in category.';
$_lang['setting_ms2_product_main_fields'] = 'Main fields of the panel of the product';
$_lang['setting_ms2_product_main_fields_desc'] = 'Comma separated list of fields in the panel of the product. For example: "pagetitle,longtitle,content".';
$_lang['setting_ms2_product_extra_fields'] = 'Extra fields of the panel of the product';
$_lang['setting_ms2_product_extra_fields_desc'] = 'Comma separated list of fields in the panel of the product, that needed in your shop. For example: "price,old_price,weight".';

$_lang['setting_mgr_tree_icon_mscategory'] = 'The icon of category';
$_lang['setting_mgr_tree_icon_mscategory_desc'] = 'The icon of category with miniShop2 products';
$_lang['setting_mgr_tree_icon_msproduct'] = 'The icon of product';
$_lang['setting_mgr_tree_icon_msproduct_desc'] = 'The icon of miniShop2 product';

$_lang['setting_ms2_product_tab_extra'] = 'Product properties tab';
$_lang['setting_ms2_product_tab_extra_desc'] = 'Display tab with product properties?';
$_lang['setting_ms2_product_tab_gallery'] = 'Product gallery tab';
$_lang['setting_ms2_product_tab_gallery_desc'] = 'Display tab with product gallery?';
$_lang['setting_ms2_product_tab_links'] = 'Product links tab';
$_lang['setting_ms2_product_tab_links_desc'] = 'Display tab with product links?';
$_lang['setting_ms2_product_tab_options'] = 'Product options tab';
$_lang['setting_ms2_product_tab_options_desc'] = 'Display tab with product options?';
$_lang['setting_ms2_product_tab_categories'] = 'Product categories tab';
$_lang['setting_ms2_product_tab_categories_desc'] = 'Display tab with product categories?';

$_lang['setting_ms2_category_show_comments'] = 'Display comments of the category';
$_lang['setting_ms2_category_show_comments_desc'] = 'Display comments of all goods from category if component "Tickets" is installed.';
$_lang['setting_ms2_category_show_nested_products'] = 'Show nested product of category';
$_lang['setting_ms2_category_show_nested_products_desc'] = 'If set to true, you will see all nested products of category. They will have another color and name of their category below pagetitle.';
$_lang['setting_ms2_category_remember_tabs'] = 'Remember category active tab';
$_lang['setting_ms2_category_remember_tabs_desc'] = 'If true, active tab of category panel will be remembered and restored on reload page.';
$_lang['setting_ms2_category_remember_grid'] = 'Remembering the categories table';
$_lang['setting_ms2_category_remember_grid_desc'] = 'When enabled, the state of the table categories will be remembered and restored when loading the page, including the page number, and the search string.';
$_lang['setting_ms2_category_id_as_alias'] = 'Use id of category as alias';
$_lang['setting_ms2_category_id_as_alias_desc'] = 'If true, aliases for friendly urls of categories will don be generated. Id will be set as alias.';
$_lang['setting_ms2_category_content_default'] = 'Default content of category';
$_lang['setting_ms2_category_content_default_desc'] = 'Here you can specify the default content of new category. By default it lists children products.';
$_lang['setting_ms2_product_show_comments'] = 'Display comments of the product';
$_lang['setting_ms2_product_show_comments_desc'] = 'Display comments of the product if component "Tickets" is installed.';
$_lang['setting_ms2_template_category_default'] = 'Default template for new category';
$_lang['setting_ms2_template_category_default_desc'] = 'Select template which will be set by default when you creating new category.';
$_lang['setting_ms2_template_product_default'] = 'Default template for new product';
$_lang['setting_ms2_template_product_default_desc'] = 'Select template which will be set by default when you creating new product.';
$_lang['setting_ms2_product_show_in_tree_default'] = 'Show in tree by default';
$_lang['setting_ms2_product_show_in_tree_default_desc'] = 'If you activate this option, all new goods will be shown in resource tree.';
$_lang['setting_ms2_product_source_default'] = 'Default media source';
$_lang['setting_ms2_product_source_default_desc'] = 'Default media source for the product gallery.';
$_lang['setting_ms2_product_vertical_tabs'] = 'Vertical tabs at product page';
$_lang['setting_ms2_product_vertical_tabs_desc'] = 'How to display product page in manager? Disabling this option allows you to fit the product page on the screen with a small horizontal size. Not recommended.';
$_lang['setting_ms2_product_remember_tabs'] = 'Remember product active tab';
$_lang['setting_ms2_product_remember_tabs_desc'] = 'If true, active tab of product panel will be remembered and restored on reload page.';
$_lang['setting_ms2_product_thumbnail_size'] = 'Default thumbnail size';
$_lang['setting_ms2_product_id_as_alias'] = 'Use id of product as alias';
$_lang['setting_ms2_product_id_as_alias_desc'] = 'If true, aliases for friendly urls of products will don be generated. Id will be set as alias.';
$_lang['setting_ms2_product_thumbnail_size_desc'] = 'Size of default pre-generated thumbnail for field "thumb" in msProduct table. Of course, this size should exist in the settings of your media source that generates the previews. Otherwise you will receive  miniShop2 logo instead of product image in manager.';

$_lang['setting_ms2_cart_handler_class'] = 'Cart handler class';
$_lang['setting_ms2_cart_handler_class_desc'] = 'The name of the class that implements the logic of a cart.';
$_lang['setting_ms2_order_handler_class'] = 'Order handler class';
$_lang['setting_ms2_order_handler_class_desc'] = 'The name of the class that implements the logic of an ordering.';
$_lang['setting_ms2_order_user_groups'] = 'Groups for registering customers';
$_lang['setting_ms2_order_user_groups_desc'] = 'Comma-separated list of user groups for adding new users when they orders.';
$_lang['setting_ms2_email_manager'] = 'Managers mailboxes';
$_lang['setting_ms2_email_manager_desc'] = 'Comma-separated list of mailboxes of managers, for sending notifications about changes of the status of the order';
$_lang['setting_ms2_date_format'] = 'Format of dates';
$_lang['setting_ms2_date_format_desc'] = 'You can specify how to format miniShop2 dates using php strftime() syntax. By default format is "%d.%m.%y %H:%M".';
$_lang['setting_ms2_price_format'] = 'Format of prices';
$_lang['setting_ms2_price_format_desc'] = 'You can specify, how to format prices of product by function number_format(). For this used JSON string with array of 3 values: number of decimals, decimals separator and thousands separator. By default format is [2,"."," "], that transforms "15336.6" into "15 336.60"';
$_lang['setting_ms2_price_format_no_zeros'] = 'Remove extra zeros in the prices';
$_lang['setting_ms2_price_format_no_zeros_desc'] = 'By default, weight of goods shown with 2 decimals: "15.20". If enabled this option, extra zeroes at the end will removed and price transforms to "15.2"';
$_lang['setting_ms2_weight_format'] = 'Format of weight';
$_lang['setting_ms2_weight_format_desc'] = 'You can specify, how to format weight of product by function number_format(). For this used JSON string with array of 3 values: number of decimals, decimals separator and thousands separator. By default format is [3,"."," "], that transforms "141.3" into "141.300"';
$_lang['setting_ms2_weight_format_no_zeros'] = 'Remove extra zeros in the weight';
$_lang['setting_ms2_weight_format_no_zeros_desc'] = 'By default, weight of goods shown with 3 decimals: "15.250". If enabled this option, extra zeroes at the end will removed and weight transforms to "15.25".';
$_lang['setting_ms2_price_snippet'] = 'Price modificator';
$_lang['setting_ms2_price_snippet_desc'] = 'You can specify existing snippet for modification of product price, when it showing on site or adding to cart. This snippet must receive object "$product" and return integer.';
$_lang['setting_ms2_weight_snippet'] = 'Weight modificator';
$_lang['setting_ms2_weight_snippet_desc'] = 'You can specify existing snippet for modification of product weight, when it showing on site or adding to cart. This snippet must receive object "$product" and return integer.';

$_lang['setting_ms2_frontend_css'] = 'Frontend styles';
$_lang['setting_ms2_frontend_css_desc'] = 'Path to file with styles of the shop. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_ms2_frontend_js'] = 'Frontend scripts';
$_lang['setting_ms2_frontend_js_desc'] = 'Path to file with scripts of the shop. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';

$_lang['setting_ms2_payment_paypal_api_url'] = 'PayPal api url';
$_lang['setting_ms2_payment_paypal_checkout_url'] = 'PayPal checkout url';
$_lang['setting_ms2_payment_paypal_currency'] = 'PayPal currency';
$_lang['setting_ms2_payment_paypal_user'] = 'PayPal user';
$_lang['setting_ms2_payment_paypal_pwd'] = 'PayPal password';
$_lang['setting_ms2_payment_paypal_signature'] = 'PayPal signature';
$_lang['setting_ms2_payment_paypal_success_id'] = 'PayPal successful page id';
$_lang['setting_ms2_payment_paypal_cancel_id'] = 'PayPal cancel page id';
$_lang['setting_ms2_payment_paypal_cancel_order'] = 'PayPal cancel order';
$_lang['setting_ms2_payment_paypal_cancel_order_desc'] = 'If true, order will be cancelled if customer cancel payment.';

$_lang['setting_ms2_order_grid_fields'] = 'Fields of the orders table';
$_lang['setting_ms2_order_grid_fields_desc'] = 'Comma separated list of fields in the table of orders. Available: "createdon,updatedon,num,cost,cart_cost,delivery_cost,weight,status,delivery,payment,customer,receiver".';
$_lang['setting_ms2_order_address_fields'] = 'Fields of order address';
$_lang['setting_ms2_order_address_fields_desc'] = 'Comma separated list of address of order, which will be shown on the third tab. Available: "receiver,phone,index,country,region,metro,building,city,street,room". If empty, this tab will be hidden.';
$_lang['setting_ms2_order_product_fields'] = 'Field of the purchased products';
$_lang['setting_ms2_order_product_fields_desc'] = 'which will be shown list of ordered products. Available: "count,price,weight,cost,options". Product fields specified with the prefix "product_", for example "product_pagetitle,product_article". Additionaly, you can specify a values from the options field with the prefix "option_", for example: "option_color,option_size".';

$_lang['ms2_source_thumbnails_desc'] = 'JSON encoded array of options for generating thumbnails.';
$_lang['ms2_source_maxUploadWidth_desc'] = 'Maximum width of image for upload. All images, that exceeds this parameter, will be resized to fit..';
$_lang['ms2_source_maxUploadHeight_desc'] = 'Maximum height of image for upload. All images, that exceeds this parameter, will be resized to fit.';
$_lang['ms2_source_maxUploadSize_desc'] = 'Maximum size of file for upload (in bytes).';
$_lang['ms2_source_imageNameType_desc'] = 'This setting specifies how to rename a file after upload. Hash is the generation of a unique name depending on the contents of the file. Friendly - generation behalf of the algorithm friendly URLs of pages of the site (they are managed by system settings).';