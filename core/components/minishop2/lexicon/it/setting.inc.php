<?php

/**
 * Settings Lexicon Entries
 *
 * @package minishop2
 * @subpackage lexicon
 */

$_lang['area_ms2_main'] = 'Impostazioni principali';
$_lang['area_ms2_category'] = 'Category of the goods';
$_lang['area_ms2_product'] = 'Prodotto';
$_lang['area_ms2_gallery'] = 'Galleria';
$_lang['area_ms2_cart'] = 'Carrello';
$_lang['area_ms2_order'] = 'Ordine';
$_lang['area_ms2_frontend'] = 'Frontend';
$_lang['area_ms2_payment'] = 'Pagamenti';
$_lang['area_ms2_statuses'] = 'Статусы';

$_lang['setting_ms2_services'] = 'Store services';
$_lang['setting_ms2_services_desc'] = 'Array with registered classes for cart, order, delivery and payment. Used by third-party extras to load their functionality.';
$_lang['setting_ms2_plugins'] = 'Store plugins';
$_lang['setting_ms2_plugins_desc'] = 'Array with registered plugins for extension objects of store model: products, customer profiles, etc.';
$_lang['setting_ms2_chunks_categories'] = 'Категории для списка чанков';
$_lang['setting_ms2_chunks_categories_desc'] = 'Список ID категорий через запятую для списка чанков.';
$_lang['setting_ms2_tmp_storage'] = 'Хранилище корзины и временных полей заказа';
$_lang['setting_ms2_tmp_storage_desc'] = "Для хранения корзины и временных полей заказа в сессии укажите <strong>session</strong><br>
Для хранения в базе данных укажите <strong>db</strong>";
$_lang['setting_ms2_use_scheduler'] = 'Использовать менеджер очередей';
$_lang['setting_ms2_use_scheduler_desc'] = 'Перед использованием убедитесь, что у вас установлен компонент Scheduler';

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
$_lang['setting_ms2_add_icon_category'] = 'The category add button icon';
$_lang['setting_ms2_add_icon_category_desc'] = 'The category add button icon in category page';
$_lang['setting_ms2_add_icon_product'] = 'The product add button icon';
$_lang['setting_ms2_add_icon_product_desc'] = 'The product add button icon in category page';

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
$_lang['setting_ms2_category_show_options'] = 'Show product options of category';
$_lang['setting_ms2_category_show_options_desc'] = 'Show product options of category.';
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
//$_lang['setting_ms2_product_thumbnail_size'] = 'Размер превью по умолчанию';
//$_lang['setting_ms2_product_thumbnail_size_desc'] = 'Здесь вы можете указать размер заранее уменьшенной копии изображения для вставки поля "thumb" товара. Конечно, этот размер должен существовать и в настройках источника медиа, чтобы генерировались такие превью. В противном случае вы получите логотип minIShop2 вместо изображения товара в админке.';
$_lang['setting_ms2_product_id_as_alias'] = 'Use id of product as alias';
$_lang['setting_ms2_product_id_as_alias_desc'] = 'If true, aliases for friendly urls of products will don be generated. Id will be set as alias.';

$_lang['setting_ms2_cart_handler_class'] = 'Cart handler class';
$_lang['setting_ms2_cart_handler_class_desc'] = 'The name of the class that implements the logic of a cart.';
$_lang['setting_ms2_cart_context'] = 'Utilizzare un cestino singolo per tutti i contesti?';
$_lang['setting_ms2_cart_context_desc'] = 'Se abilitato, viene utilizzato un cestino comune per tutti i contesti. Se questa opzione è disattivata, ciascun contesto utilizza il proprio carrello.';
$_lang['setting_ms2_order_handler_class'] = 'Order handler class';
$_lang['setting_ms2_order_handler_class_desc'] = 'The name of the class that implements the logic of an ordering.';
$_lang['setting_ms2_cart_max_count'] = 'Максимальное количество товаров в корзине';
$_lang['setting_ms2_cart_max_count_desc'] = 'По умолчанию 1000. При превышении этого значения будет выведено уведомление.';
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

$_lang['setting_ms2_frontend_message_css'] = 'Стили библиотеки уведомлений';
$_lang['setting_ms2_frontend_message_css_desc'] = "Путь к CSS файлу вашей библиотеки уведомлений. По умолчанию к jgrowl. <br>
Если вы хотите использовать собственную библиотеку - укажите путь к ее css каталогу здесь, или очистите параметр и загрузите их вручную через шаблон сайта.";

$_lang['setting_ms2_frontend_message_js'] = 'Скрипты библиотеки уведомлений';
$_lang['setting_ms2_frontend_message_js_desc'] = "Путь к JS файлу вашей библиотеки уведомлений. По умолчанию к jgrowl. <br>
Если вы хотите использовать собственную библиотеку - укажите путь к ее JS каталогу здесь, или очистите параметр и загрузите их вручную через шаблон сайта.";

$_lang['setting_ms2_frontend_message_js_settings'] = 'Настройки библиотеки уведомлений';
$_lang['setting_ms2_frontend_message_js_settings_desc'] = "Путь к файлу с реализацией шаблона уведомлений на основе вашей библиотеки. <br>
По умолчанию к настройкам jgrowl. <br>
Если вы хотите использовать собственную библиотеку - укажите путь к ее настройкам здесь, или очистите параметр и загрузите их вручную через шаблон сайта.";
$_lang['setting_ms2_register_frontend'] = 'Добавлять js и css из комплекта ms2 файлы в DOM дерево';
$_lang['setting_ms2_register_frontend_desc'] = 'Разрешить добавление в DOM дерево ссылок на js и css файлы из комплекта ms2';

$_lang['setting_ms2_order_format_num'] = 'Формат нумерации заказа';
$_lang['setting_ms2_order_format_num_desc'] = 'Формат нумерации заказа. Доступные значения в формате PHP strftime()';
$_lang['setting_ms2_order_format_num_separator'] = 'Разделитель для нумерации заказа';
$_lang['setting_ms2_order_format_num_separator_desc'] = 'Разделитель для нумерации заказа. Доступные значения: "/", "," и "-"';
$_lang['setting_ms2_order_grid_fields'] = 'Fields of the orders table';
$_lang['setting_ms2_order_grid_fields_desc'] = 'Comma separated list of fields in the table of orders. Available: "createdon,updatedon,num,cost,cart_cost,delivery_cost,weight,status,delivery,payment,customer,receiver".';
$_lang['setting_ms2_order_address_fields'] = 'Fields of order address';
$_lang['setting_ms2_order_address_fields_desc'] = 'Comma separated list of address of order, which will be shown on the third tab. Available: "receiver,phone,index,country,region,metro,building,city,street,room". If empty, this tab will be hidden.';
$_lang['setting_ms2_order_product_fields'] = 'Field of the purchased products';
$_lang['setting_ms2_order_product_fields_desc'] = 'which will be shown list of ordered products. Available: "count,price,weight,cost,options". Product fields specified with the prefix "product_", for example "product_pagetitle,product_article". Additionaly, you can specify a values from the options field with the prefix "option_", for example: "option_color,option_size".';
$_lang['setting_ms2_order_product_options'] = 'Поля опций продукта в заказе';
$_lang['setting_ms2_order_product_options_desc'] = 'Перечень редактируемых опций товара в окне заказа. По умолчанию: "color,size".';

$_lang['ms2_source_thumbnails_desc'] = 'JSON encoded array of options for generating thumbnails.';
$_lang['ms2_source_maxUploadWidth_desc'] = 'Maximum width of image for upload. All images, that exceeds this parameter, will be resized to fit..';
$_lang['ms2_source_maxUploadHeight_desc'] = 'Maximum height of image for upload. All images, that exceeds this parameter, will be resized to fit.';
$_lang['ms2_source_maxUploadSize_desc'] = 'Maximum size of file for upload (in bytes).';
$_lang['ms2_source_imageNameType_desc'] = 'This setting specifies how to rename a file after upload. Hash is the generation of a unique name depending on the contents of the file. Friendly - generation behalf of the algorithm friendly URLs of pages of the site (they are managed by system settings).';

// Настройки для альфа релиза miniShop2 4.0.0.beta
$_lang['setting_ms2_frontend_css'] = 'Frontend styles';
$_lang['setting_ms2_frontend_css_desc'] = 'Path to file with styles of the shop. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_ms2_frontend_js'] = 'Frontend scripts';
$_lang['setting_ms2_frontend_js_desc'] = 'Path to file with scripts of the shop. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';

$_lang['setting_ms2_cart_js_class_name'] = 'Название JS класса управления корзиной';
$_lang['setting_ms2_cart_js_class_name_desc'] = 'класс должен быть экспортирован по умолчанию';
$_lang['setting_ms2_cart_js_class_path'] = 'Путь к JS управления корзиной';
$_lang['setting_ms2_cart_js_class_path_desc'] = 'путь указывается относительно папки assets/components/minishop2/js/web/modules';

$_lang['setting_ms2_order_js_class_name'] = 'Название JS класса для оформления заказа';
$_lang['setting_ms2_order_js_class_name_desc'] = 'класс должен быть экспортирован по умолчанию';
$_lang['setting_ms2_order_js_class_path'] = 'Путь к JS классу для оформления заказа';
$_lang['setting_ms2_order_js_class_path_desc'] = 'путь указывается относительно папки assets/components/minishop2/js/web/modules';

$_lang['setting_ms2_notify_js_class_name'] = 'Название JS класса для показа уведомлений';
$_lang['setting_ms2_notify_js_class_name_desc'] = 'класс должен быть экспортирован по умолчанию';
$_lang['setting_ms2_notify_js_class_path'] = 'Путь к JS классу для показа уведомлений';
$_lang['setting_ms2_notify_js_class_path_desc'] = 'путь указывается относительно папки assets/components/minishop2/js/web/modules';

$_lang['setting_ms2_toggle_js_type'] = 'Включить новый JavaScript?';
$_lang['setting_ms2_toggle_js_type_desc'] = 'если выбрано ДА будут подключены скрипты без зависимости от jQuery, написанные с использованием возможностей стандарта ES6';

$_lang['setting_ms2_vanila_js'] = 'Новые скрипты фронтенда';
$_lang['setting_ms2_vanila_js_desc'] = 'путь к файлу инициализации новых скриптов магазина. Если хотите указать свои параметры инициализации - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';

$_lang['setting_ms2_frontend_notify_js_settings'] = 'Настройки уведомлений для новой версии скриптов';
$_lang['setting_ms2_frontend_notify_js_settings_desc'] = 'путь к файлу с настройками. обратите внимание файл в формате JSON';

$_lang['setting_ms2_status_draft'] = 'ID статуса заказа Черновик';
$_lang['setting_ms2_status_draft_desc'] = 'Какой статус нужно устанавливать для заказа-черновика';
$_lang['setting_ms2_status_new'] = 'ID первоначального статуса заказа';
$_lang['setting_ms2_status_new_desc'] = 'Какой статус нужно устанавливать для нового совершенного заказа';
$_lang['setting_ms2_status_paid'] = 'ID статуса оплаченного заказа';
$_lang['setting_ms2_status_paid_desc'] = 'Какой статус нужно устанавливать после оплаты заказа';
$_lang['setting_ms2_status_canceled'] = 'ID статуса отмены заказа';
$_lang['setting_ms2_status_canceled_desc'] = 'Какой статус нужно устанавливать при отмене заказа';
$_lang['setting_ms2_status_for_stat'] = 'ID статусов для статистики';
$_lang['setting_ms2_status_for_stat_desc'] = 'Статусы через запятую, для построения статистики ВЫПОЛНЕННЫХ заказов';
