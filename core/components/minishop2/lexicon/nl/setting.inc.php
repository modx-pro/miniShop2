<?php

/**
 * Settings Russian Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */

$_lang['area_ms2_main'] = 'Основные настройки';
$_lang['area_ms2_category'] = 'Produktkategorie';
$_lang['area_ms2_product'] = 'Produkt';
$_lang['area_ms2_gallery'] = 'Galerie';
$_lang['area_ms2_cart'] = 'Warenkorb';
$_lang['area_ms2_order'] = 'Bestellung';
$_lang['area_ms2_frontend'] = 'Frontend';
$_lang['area_ms2_payment'] = 'Zahlung';
$_lang['area_ms2_statuses'] = 'Статусы';

$_lang['setting_ms2_services'] = 'Службы магазина';
$_lang['setting_ms2_services_desc'] = 'Массив с зарегистрированными классами для корзины, заказа, доставки и оплаты. Используется сторонними дополнениями для загрузки своего функционала.';
$_lang['setting_ms2_plugins'] = 'Плагины магазина';
$_lang['setting_ms2_plugins_desc'] = 'Массив с зарегистрированными плагинами расширения объектов модели магазина: товаров, профилей покупателя и т.д.';
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

$_lang['setting_mgr_tree_icon_mscategory'] = 'Иконка категории';
$_lang['setting_mgr_tree_icon_mscategory_desc'] = 'Иконка категории товаров miniShop2 в дереве ресурсов';
$_lang['setting_mgr_tree_icon_msproduct'] = 'Иконка товара';
$_lang['setting_mgr_tree_icon_msproduct_desc'] = 'Иконка товара miniShop2 в дереве ресурсов';
$_lang['setting_ms2_add_icon_category'] = 'Иконка добавления категории';
$_lang['setting_ms2_add_icon_category_desc'] = 'Иконка на кнопке добавления категории на странице категории';
$_lang['setting_ms2_add_icon_product'] = 'Иконка добавления товара';
$_lang['setting_ms2_add_icon_product_desc'] = 'Иконка на кнопке добавления товара на странице категории';

$_lang['setting_ms2_product_tab_extra'] = 'Вкладка свойств товара';
$_lang['setting_ms2_product_tab_extra_desc'] = 'Показывать вкладку свойств товара?';
$_lang['setting_ms2_product_tab_gallery'] = 'Вкладка галереи товара';
$_lang['setting_ms2_product_tab_gallery_desc'] = 'Показывать вкладку галереи товара?';
$_lang['setting_ms2_product_tab_links'] = 'Вкладка связей товара';
$_lang['setting_ms2_product_tab_links_desc'] = 'Показывать вкладку связей товара?';
$_lang['setting_ms2_product_tab_options'] = 'Вкладка опций товара';
$_lang['setting_ms2_product_tab_options_desc'] = 'Показывать вкладку опций товара?';
$_lang['setting_ms2_product_tab_categories'] = 'Вкладка категорий товара';
$_lang['setting_ms2_product_tab_categories_desc'] = 'Показывать вкладку категорий товара?';

$_lang['setting_ms2_category_show_comments'] = 'Display comments of the category';
$_lang['setting_ms2_category_show_comments_desc'] = 'Display comments of all goods from category if component "Tickets" is installed.';
$_lang['setting_ms2_category_show_nested_products'] = 'Show nested product of category';
$_lang['setting_ms2_category_show_nested_products_desc'] = 'If set to true, you will see all nested products of category. They will have another color and name of their category below pagetitle.';
$_lang['setting_ms2_category_show_options'] = 'Показывать опции товаров категории';
$_lang['setting_ms2_category_show_options_desc'] = 'Показывать опции к товарам категории.';
$_lang['setting_ms2_category_remember_tabs'] = 'Запоминание вкладки категории';
$_lang['setting_ms2_category_remember_tabs_desc'] = 'Если включено, активная вкладка панели категории будет запоминаться и восстанавливаться при загрузке страницы.';
$_lang['setting_ms2_category_remember_grid'] = 'Запоминание таблицы категории';
$_lang['setting_ms2_category_remember_grid_desc'] = 'Если включено, состояние таблицы категории будет запоминаться и восстанавливаться при загрузке страницы, включая номер страницы и строку поиска.';
$_lang['setting_ms2_category_id_as_alias'] = 'Id категории как псевдоним';
$_lang['setting_ms2_category_id_as_alias_desc'] = 'Если включено, псевдонимы для дружественных имён категорий не будут генерироваться. Вместо этого будут подставляться их id.';
$_lang['setting_ms2_category_content_default'] = 'Содержимое категории по умолчанию';
$_lang['setting_ms2_category_content_default_desc'] = 'Здесь вы можете указать контент вновь создаваемой категории. По умолчанию установлен вывод дочерних товаров.';
$_lang['setting_ms2_product_show_comments'] = 'Display comments of the product';
$_lang['setting_ms2_product_show_comments_desc'] = 'Display comments of the product if component "Tickets" is installed.';
$_lang['setting_ms2_template_category_default'] = 'Шаблон по умолчанию для новых категорий';
$_lang['setting_ms2_template_category_default_desc'] = 'Выберите шаблон, который будет установлен по умолчанию при создании категории.';
$_lang['setting_ms2_template_product_default'] = 'Standard-Template für neues Produkt';
$_lang['setting_ms2_template_product_default_desc'] = 'Template wählen, das standardmäßig bei der Neuerstellung eines Produktes verwendet wird.';
$_lang['setting_ms2_product_show_in_tree_default'] = 'Standardmäßig im Resourcenbaum anzeigen';
$_lang['setting_ms2_product_show_in_tree_default_desc'] = 'Durch Aktivieren dieser Option werden alle neuen Produkte im Resourcenbaum angezeigt.';
$_lang['setting_ms2_product_source_default'] = 'Standard Medienquelle';
$_lang['setting_ms2_product_source_default_desc'] = 'Standard Medienquelle für die Produktgalerie.';
$_lang['setting_ms2_product_vertical_tabs'] = 'Vertikale Tabs auf der Produktseite';
$_lang['setting_ms2_product_vertical_tabs_desc'] = 'How to display product page in manager? Disabling this option allows you to fit the product page on the screen with a small horizontal size. Not recommended.';
$_lang['setting_ms2_product_remember_tabs'] = 'Запоминание вкладки товара';
$_lang['setting_ms2_product_remember_tabs_desc'] = 'Если включено, активная вкладка панели товара будет запоминаться и восстанавливаться при загрузке страницы.';
//$_lang['setting_ms2_product_thumbnail_size'] = 'Размер превью по умолчанию';
//$_lang['setting_ms2_product_thumbnail_size_desc'] = 'Здесь вы можете указать размер заранее уменьшенной копии изображения для вставки поля "thumb" товара. Конечно, этот размер должен существовать и в настройках источника медиа, чтобы генерировались такие превью. В противном случае вы получите логотип minIShop2 вместо изображения товара в админке.';
$_lang['setting_ms2_product_id_as_alias'] = 'Id товара как псевдоним';
$_lang['setting_ms2_product_id_as_alias_desc'] = 'Если включено, псевдонимы для дружественных имён товаров не будут генерироваться. Вместо этого будут подставляться их id.';

$_lang['setting_ms2_cart_handler_class'] = 'Cart handler class';
$_lang['setting_ms2_cart_handler_class_desc'] = 'The name of the class that implements the logic of a cart.';
$_lang['setting_ms2_cart_context'] = 'Verwenden Sie einen einzigen Korb für alle Kontexte?';
$_lang['setting_ms2_cart_context_desc'] = 'Wenn aktiviert, wird für alle Kontexte ein gemeinsamer Warenkorb verwendet. Wenn deaktiviert, verwendet jeder Kontext seinen eigenen Warenkorb.';
$_lang['setting_ms2_order_handler_class'] = 'Order handler class';
$_lang['setting_ms2_order_handler_class_desc'] = 'The name of the class that implements the logic of an ordering.';
$_lang['setting_ms2_cart_max_count'] = 'Максимальное количество товаров в корзине';
$_lang['setting_ms2_cart_max_count_desc'] = 'По умолчанию 1000. При превышении этого значения будет выведено уведомление.';
$_lang['setting_ms2_order_user_groups'] = 'Groups for registering customers';
$_lang['setting_ms2_order_user_groups_desc'] = 'Comma-separated list of user groups for adding new users when they orders.';
$_lang['setting_ms2_email_manager'] = 'Managers mailboxes';
$_lang['setting_ms2_email_manager_desc'] = 'Comma-separated list of mailboxes of managers, for sending notifications about changes of the status of the order';
$_lang['setting_ms2_date_format'] = 'Datums-Format';
$_lang['setting_ms2_date_format_desc'] = 'miniShop2 formatiert das Datum mit Hilfe der PHP strftime() Syntax. Standardmäßig ist das Format "%d.%m.%y %H:%M".';
$_lang['setting_ms2_price_format'] = 'Preis-Format';
$_lang['setting_ms2_price_format_desc'] = 'You can specify, how to format prices of product by function number_format(). For this used JSON string with array of 3 values: number of decimals, decimals separator and thousands separator. By default format is [2,"."," "], that transforms "15336.6" into "15 336.60"';
$_lang['setting_ms2_price_format_no_zeros'] = 'Nullstellen beim Preis entfernen';
$_lang['setting_ms2_price_format_no_zeros_desc'] = 'By default, weight of goods shown with 2 decimals: "15.20". If enabled this option, extra zeroes at the end will removed and price transforms to "15.2"';
$_lang['setting_ms2_weight_format'] = 'Gewicht-Format';
$_lang['setting_ms2_weight_format_desc'] = 'You can specify, how to format weight of product by function number_format(). For this used JSON string with array of 3 values: number of decimals, decimals separator and thousands separator. By default format is [3,"."," "], that transforms "141.3" into "141.300"';
$_lang['setting_ms2_weight_format_no_zeros'] = 'Nullstellen beim Gewicht entfernen';
$_lang['setting_ms2_weight_format_no_zeros_desc'] = 'By default, weight of goods shown with 3 decimals: "15.250". If enabled this option, extra zeroes at the end will removed and weight transforms to "15.25".';
$_lang['setting_ms2_price_snippet'] = 'Модификатор цены';
$_lang['setting_ms2_price_snippet_desc'] = 'Здесь вы можете указать имя сниппета для модификации цены при выводе на сайте и добавлении в корзину. Он должен принимать объект "$product" и возвращать число.';
$_lang['setting_ms2_weight_snippet'] = 'Модификатор веса';
$_lang['setting_ms2_weight_snippet_desc'] = 'Здесь вы можете указать имя сниппета для модификации веса товара при выводе на сайте и добавлении в корзину. Он должен принимать объект "$product" и возвращать число.';

$_lang['setting_ms2_frontend_css'] = 'Frontend Styles';
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
$_lang['setting_ms2_order_grid_fields'] = 'Поля таблицы заказов';
$_lang['setting_ms2_order_grid_fields_desc'] = 'Список полей, которые будут показаны в таблице заказов. Доступны: "id,num,customer,status,cost,weight,delivery,payment,createdon,updatedon,comment".';
$_lang['setting_ms2_order_address_fields'] = 'Поля адреса доставки';
$_lang['setting_ms2_order_address_fields_desc'] = 'Список полей доставки, которые будут показаны на третьей вкладке карточки заказа. Доступны: "receiver,phone,email,index,country,region,city,metro,street,building,entrance,floor,room,comment,text_address". Если параметр пуст, вкладка будет скрыта.';
$_lang['setting_ms2_order_product_fields'] = 'Поля таблицы покупок';
$_lang['setting_ms2_order_product_fields_desc'] = 'Список полей таблицы заказанных товаров. Доступны: "product_pagetitle,vendor_name,product_article,weight,price,count,cost". Поля товара указываются с префиксом "product_", например "product_pagetitle,product_article". Дополнительно можно указывать значения из поля options с префиксом "option_", например: "option_color,option_size".';
$_lang['setting_ms2_order_product_options'] = 'Поля опций продукта в заказе';
$_lang['setting_ms2_order_product_options_desc'] = 'Перечень редактируемых опций товара в окне заказа. По умолчанию: "color,size".';

$_lang['ms2_source_thumbnails_desc'] = 'JSON encoded array of options for generating thumbnails.';
$_lang['ms2_source_maxUploadWidth_desc'] = 'Максимальная ширина изображения для загрузки. Всё, что больше, будет ужато до этого значения.';
$_lang['ms2_source_maxUploadHeight_desc'] = 'Максимальная высота изображения для загрузки. Всё, что больше, будет ужато до этого значения.';
$_lang['ms2_source_maxUploadSize_desc'] = 'Максимальный размер загружаемых изображений (в байтах).';
$_lang['ms2_source_imageNameType_desc'] = 'Этот параметр указывает, как нужно переименовать файл при загрузке. Hash - это генерация уникального имени, в зависимости от содержимого файла. Friendly - генерация имени по алгоритму дружественных url страниц сайта (они управляются системными настройками).';

// Настройки для альфа релиза miniShop2 4.0.0.beta
$_lang['setting_ms2_frontend_css'] = 'Стили фронтенда';
$_lang['setting_ms2_frontend_css_desc'] = 'Путь к файлу со стилями магазина. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_ms2_frontend_js'] = 'Скрипты фронтенда';
$_lang['setting_ms2_frontend_js_desc'] = 'Путь к файлу со скриптами магазина. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';

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
