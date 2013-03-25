<?php
/**
 * Settings Russian Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */
$_lang['area_ms2_category'] = 'Категория товаров';
$_lang['area_ms2_product'] = 'Товар';
$_lang['area_ms2_gallery'] = 'Галерея';
$_lang['area_ms2_cart'] = 'Корзина';
$_lang['area_ms2_order'] = 'Заказы';
$_lang['area_ms2_frontend'] = 'Сайт';
$_lang['area_ms2_payment'] = 'Платежи';

$_lang['setting_ms2_category_grid_fields'] = 'Поля таблицы товаров';
$_lang['setting_ms2_category_grid_fields_desc'] = 'Список видимых полей таблицы с товарами категории, через запятую.';
$_lang['setting_ms2_product_main_fields'] = 'Основные поля панели товара';
$_lang['setting_ms2_product_main_fields_desc'] = 'Список полей панели товара, через запятую. Например: "pagetitle,longtitle,content".';
$_lang['setting_ms2_product_extra_fields'] = 'Дополнительные поля товара';
$_lang['setting_ms2_product_extra_fields_desc'] = 'Список дополнительных полей товара, использующихся в магазине, через запятую. Например: "price,old_price,weight".';

$_lang['setting_ms2_category_show_comments'] = 'Показывать комментарии категории';
$_lang['setting_ms2_category_show_comments_desc'] = 'Показывать комментарии оставленные ко всем товарам категории, если установлен компонент "Tickets"';
$_lang['setting_ms2_category_show_nested_products'] = 'Показывать вложенные товары категории';
$_lang['setting_ms2_category_show_nested_products_desc'] = 'Если вы включаете эту опцию, то в категории будут показаны все вложенные товары. Они выделены другим цветом и у них есть имя родной категории под pagetitle.';
$_lang['setting_ms2_product_show_comments'] = 'Показывать комментарии товара';
$_lang['setting_ms2_product_show_comments_desc'] = 'Показывать комментарии оставленные к товару, если установлен компонент "Tickets"';
$_lang['setting_ms2_template_product_default'] = 'Шаблон по умолчанию для новых товаров';
$_lang['setting_ms2_template_product_default_desc'] = 'Выберете шаблон, который будет установлен по умолчанию при создании товара.';
$_lang['setting_ms2_product_show_in_tree_default'] = 'Показывать в дереве по умолчанию';
$_lang['setting_ms2_product_show_in_tree_default_desc'] = 'Включите эту опцию, чтобы все создаваемые товары были видны в дереве ресурсов.';
$_lang['setting_ms2_product_source_default'] = 'Источник файлов по умолчанию';
$_lang['setting_ms2_product_source_default_desc'] = 'Источник файлов для галереи изображений товара по умолчанию.';
$_lang['setting_ms2_product_vertical_tabs'] = 'Вертикальные табы на странице товара';
$_lang['setting_ms2_product_vertical_tabs_desc'] = 'Как показывать страницу твоара? Отключение этой опции позволяет уместить страницу товара на экранах с небольшой горизонталью. Не рекомендуется.';
$_lang['setting_ms2_product_thumbnail_size'] = 'Размер превью по умолчанию';
$_lang['setting_ms2_product_thumbnail_size_desc'] = 'Вы можете указать размер уменьшенной копии изображения для поля "thumb" товара.';
$_lang['ms2_source_thumbnails_desc'] = 'Закодированный в JSON массив с параметрами генерации уменьшенных копий изображений.';

$_lang['setting_ms2_cart_handler_class'] = 'Класс обработчик корзины';
$_lang['setting_ms2_cart_handler_class_desc'] = 'Имя класса, который реализует логику работы с корзиной.';
$_lang['setting_ms2_order_handler_class'] = 'Класс обработчик заказа';
$_lang['setting_ms2_order_handler_class_desc'] = 'Имя класса, который реализует логику оформления заказа.';
$_lang['setting_ms2_order_user_groups'] = 'Группы регистрации покупателей';
$_lang['setting_ms2_order_user_groups_desc'] = 'Список групп, через запятую, в которые вы хотите добавлять новых покупателей при оформлении заказа.';
$_lang['setting_ms2_email_manager'] = 'Почтовые адреса менеджеров';
$_lang['setting_ms2_email_manager_desc'] = 'Список почтовых ящиков менеджеров, через запятую, на которые отправлять уведомления об изменении статуса заказа.';
$_lang['setting_ms2_date_format'] = 'Форматирование дат';
$_lang['setting_ms2_date_format_desc'] = 'Вы можете указать как форматировать даты miniShop2, используя синтаксис php функции strftime().';

$_lang['setting_ms2_frontend_css'] = 'Стили фронтенда';
$_lang['setting_ms2_frontend_css_desc'] = 'Путь к файлу со стилями магазина. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_ms2_frontend_js'] = 'Скрипты фронтенда';
$_lang['setting_ms2_frontend_js_desc'] = 'Путь к файлу со скриптами магазина. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';

$_lang['setting_ms2_payment_paypal_api_url'] = 'Url api запросов PayPal';
$_lang['setting_ms2_payment_paypal_checkout_url'] = 'Url оплаты PayPal';
$_lang['setting_ms2_payment_paypal_currency'] = 'Валюта на PayPal';
$_lang['setting_ms2_payment_paypal_user'] = 'Логин продавца PayPal';
$_lang['setting_ms2_payment_paypal_pwd'] = 'Пароль продавца PayPal';
$_lang['setting_ms2_payment_paypal_signature'] = 'Подпись продавца PayPal';
$_lang['setting_ms2_payment_paypal_success_id'] = 'Страница успешной оплаты PayPal';
$_lang['setting_ms2_payment_paypal_cancel_id'] = 'Страница отказа от оплаты PayPal';
$_lang['setting_ms2_payment_paypal_cancel_order'] = 'Отмена заказа PayPalß';
$_lang['setting_ms2_payment_paypal_cancel_order_desc'] = 'Если включено, заказ будет отменён при отказе от оплааты.';