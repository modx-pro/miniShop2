<?php

/**
 * Settings Lexicon Entries
 *
 * @package minishop2
 * @subpackage lexicon
 */

$_lang['area_ms2_main'] = 'Основні налаштування';
$_lang['area_ms2_category'] = 'Категорія товарів';
$_lang['area_ms2_product'] = 'Товар';
$_lang['area_ms2_gallery'] = 'Галерея';
$_lang['area_ms2_cart'] = 'Кошик';
$_lang['area_ms2_order'] = 'Замовлення';
$_lang['area_ms2_frontend'] = 'Сайт';
$_lang['area_ms2_payment'] = 'Платежі';
$_lang['area_ms2_statuses'] = 'Статусы';

$_lang['setting_ms2_services'] = 'Служби магазину';
$_lang['setting_ms2_services_desc'] = 'Масив із зареєстрованими класами для кошика, замовлення, доставки і оплати. Використовується сторонніми доповненнями для завантаження свого функціоналу.';
$_lang['setting_ms2_plugins'] = 'Плагіни магазину';
$_lang['setting_ms2_plugins_desc'] = 'Масив із зареєстрованими плагінами розширення обʼєктів моделі магазину: товарів, профілей покупців і т.д.';
$_lang['setting_ms2_chunks_categories'] = 'Категорії для списку чанкі';
$_lang['setting_ms2_chunks_categories_desc'] = 'Список ID категорій через кому для списку чанкі.';
$_lang['setting_ms2_tmp_storage'] = 'Хранилище корзины и временных полей заказа';
$_lang['setting_ms2_tmp_storage_desc'] = "Для хранения корзины и временных полей заказа в сессии укажите <strong>session</strong><br>
Для хранения в базе данных укажите <strong>db</strong>";
$_lang['setting_ms2_use_scheduler'] = 'Использовать менеджер очередей';
$_lang['setting_ms2_use_scheduler_desc'] = 'Перед использованием убедитесь, что у вас установлен компонент Scheduler';

$_lang['setting_ms2_category_grid_fields'] = 'Поля таблиці товарів';
$_lang['setting_ms2_category_grid_fields_desc'] = 'Список видимих полів таблиці з товарами категорії, через кому.';
$_lang['setting_ms2_product_main_fields'] = 'Основні поля панелі товару';
$_lang['setting_ms2_product_main_fields_desc'] = 'Список полів панелі товару, через кому. Наприклад: "pagetitle,longtitle,content".';
$_lang['setting_ms2_product_extra_fields'] = 'Додаткові поля товару';
$_lang['setting_ms2_product_extra_fields_desc'] = 'Список додаткових полів товару, що використовуються в магазині, через кому. Наприклад: "price,old_price,weight".';

$_lang['setting_mgr_tree_icon_mscategory'] = 'Іконка категорії';
$_lang['setting_mgr_tree_icon_mscategory_desc'] = 'Іконка категорії товарів miniShop2 в дереві ресурсів';
$_lang['setting_mgr_tree_icon_msproduct'] = 'Іконка товару';
$_lang['setting_mgr_tree_icon_msproduct_desc'] = 'Іконка товару miniShop2 в дереві ресурсів';
$_lang['setting_ms2_add_icon_category'] = 'Іконка додавання категорії';
$_lang['setting_ms2_add_icon_category_desc'] = 'Іконка на кнопці додавання категорії на сторінці категорії';
$_lang['setting_ms2_add_icon_product'] = 'Іконка додавання товару';
$_lang['setting_ms2_add_icon_product_desc'] = 'Іконка на кнопці додавання товару на сторінці категорії';

$_lang['setting_ms2_product_tab_extra'] = 'Вкладка властивостей товару';
$_lang['setting_ms2_product_tab_extra_desc'] = 'Показувати вкладку властивостей товару?';
$_lang['setting_ms2_product_tab_gallery'] = 'Вкладка галереї товару';
$_lang['setting_ms2_product_tab_gallery_desc'] = 'Показувати вкладку галереї товару?';
$_lang['setting_ms2_product_tab_links'] = 'Вкладка звʼязків товару';
$_lang['setting_ms2_product_tab_links_desc'] = 'Показувати вкладку звʼязків товару?';
$_lang['setting_ms2_product_tab_options'] = 'Вкладка опцій товару';
$_lang['setting_ms2_product_tab_options_desc'] = 'Показувати вкладку опцій товару?';
$_lang['setting_ms2_product_tab_categories'] = 'Вкладка категорій товару';
$_lang['setting_ms2_product_tab_categories_desc'] = 'Показувати вкладку категорій товару?';

$_lang['setting_ms2_category_show_comments'] = 'Показувати коментарі категорії';
$_lang['setting_ms2_category_show_comments_desc'] = 'Показувати коментарі, залишені для всіх товарів категорії, якщо встановлений компонент "Tickets"';
$_lang['setting_ms2_category_show_nested_products'] = 'Показувати дочірні товари категорії';
$_lang['setting_ms2_category_show_nested_products_desc'] = 'Якщо ви вмикаєте цю опцію, то в категорії будуть показані всі дочірні товари. Вони виділені іншим кольором і у них є назва власної категорії під pagetitle.';
$_lang['setting_ms2_category_show_options'] = 'Показувати опціі товарів категорії';
$_lang['setting_ms2_category_show_options_desc'] = 'Показувати опції товарів категорії.';
$_lang['setting_ms2_category_remember_tabs'] = 'Запамʼятовування вкладки категорії';
$_lang['setting_ms2_category_remember_tabs_desc'] = 'Якщо увімкнено, активна вкладка панелі категорії буде запамʼятовуватись і відновлюватись при завантаженні сторінки.';
$_lang['setting_ms2_category_remember_grid'] = 'Запамʼятовування таблиці категорії';
$_lang['setting_ms2_category_remember_grid_desc'] = 'Якщо увімкнено, стан таблиці категорії буде запамʼятовуватись і відновлюватись при завантаженні сторінки, включаючи номер сторінки і рядок пошуку.';
$_lang['setting_ms2_category_id_as_alias'] = 'Id категорії як псевдонім';
$_lang['setting_ms2_category_id_as_alias_desc'] = 'Якщо увімкнено, псевдоніми для дружніх імен категорій не будуть генеруватись. Замість цього будуть підставлятись їх id.';
$_lang['setting_ms2_category_content_default'] = 'Вміст категорії за замовчуванням';
$_lang['setting_ms2_category_content_default_desc'] = 'Тут ви можете вказати контент для нової категорії. За замовчуванням встановлений вивід дочірніх товарів.';
$_lang['setting_ms2_product_show_comments'] = 'Показувати коментарі товару';
$_lang['setting_ms2_product_show_comments_desc'] = 'Показувати коментарі товару, якщо встановлений компонент "Tickets"';
$_lang['setting_ms2_template_category_default'] = 'Шаблон за замовчуванням для нових категорій';
$_lang['setting_ms2_template_category_default_desc'] = 'Виберіть шаблон, що буде встановлений за замовчуванням при створенні категорії.';
$_lang['setting_ms2_template_product_default'] = 'Шаблон за замовчуванням для нових товарів';
$_lang['setting_ms2_template_product_default_desc'] = 'Виберіть шаблон, що буде встановлений за замовчуванням при створенні товару.';
$_lang['setting_ms2_product_show_in_tree_default'] = 'Показувати в дереві за замовчуванням';
$_lang['setting_ms2_product_show_in_tree_default_desc'] = 'Увімкніть цю опцію, щоб усі створені товари були видимі в дереві ресурсів.';
$_lang['setting_ms2_product_source_default'] = 'Джерело файлів за замовчуванням';
$_lang['setting_ms2_product_source_default_desc'] = 'Джерело файлів для галереї зображень товару за замовчуванням.';
$_lang['setting_ms2_product_vertical_tabs'] = 'Вертикальні таби на сторінці товару';
$_lang['setting_ms2_product_vertical_tabs_desc'] = 'Як показувати сторінку товару? Вимкнення даної опції дозволяє вмістити сторінку товару на екранах з невеликою горизонталлю. Не рекомендується.';
$_lang['setting_ms2_product_remember_tabs'] = 'Запамʼятовування вкладки товару';
$_lang['setting_ms2_product_remember_tabs_desc'] = 'Якщо увімкнено, активна вкладка панелі товару буде запамʼятовуватись і відновлюватись при завантаженні сторінки.';
//$_lang['setting_ms2_product_thumbnail_size'] = 'Размер превью по умолчанию';
//$_lang['setting_ms2_product_thumbnail_size_desc'] = 'Здесь вы можете указать размер заранее уменьшенной копии изображения для вставки поля "thumb" товара. Конечно, этот размер должен существовать и в настройках источника медиа, чтобы генерировались такие превью. В противном случае вы получите логотип minIShop2 вместо изображения товара в админке.';
$_lang['setting_ms2_product_id_as_alias'] = 'Id товару як псевдонім';
$_lang['setting_ms2_product_id_as_alias_desc'] = 'Якщо увімкнено, псевдоніми для дружніх імен товарів не будуть генеруватися. Замість цього будуть підставлятись їх id.';

$_lang['setting_ms2_cart_handler_class'] = 'Клас обробника кошика';
$_lang['setting_ms2_cart_handler_class_desc'] = 'Імʼя класу, що реалізує логіку роботи з кошиком.';
$_lang['setting_ms2_cart_context'] = 'Використовувати єдиний кошик для всіх контекстів?';
$_lang['setting_ms2_cart_context_desc'] = 'Якщо включено, то використовується загальний кошик для всіх контекстів. Якщо вимкнено - то у кожного контексту використовується свій кошик.';
$_lang['setting_ms2_order_handler_class'] = 'Клас обробник замовлення';
$_lang['setting_ms2_order_handler_class_desc'] = 'Імʼя класу, що реалізує логіку оформлення замовлення.';
$_lang['setting_ms2_cart_max_count'] = 'Максимальное количество товаров в корзине';
$_lang['setting_ms2_cart_max_count_desc'] = 'По умолчанию 1000. При превышении этого значения будет выведено уведомление.';
$_lang['setting_ms2_order_user_groups'] = 'Групи реєстрації покупців';
$_lang['setting_ms2_order_user_groups_desc'] = 'Список груп, через кому, до яких ви бажаєте додавати нових покупців при оформленні замовлення.';
$_lang['setting_ms2_email_manager'] = 'Email адреси менеджерів';
$_lang['setting_ms2_email_manager_desc'] = 'Список поштових скриньок менеджерів, через кому, на які відправляються повідомлення про зміну статусу замовлення.';
$_lang['setting_ms2_date_format'] = 'Формат дати';
$_lang['setting_ms2_date_format_desc'] = 'Вкажіть формат дат miniShop2, використовуючи синтаксис php функції strftime(). За замовчуванням формат "%d.%m.%y %H:%M".';
$_lang['setting_ms2_price_format'] = 'Формат цін';
$_lang['setting_ms2_price_format_desc'] = 'Вкажіть, як потрібно форматувати ціни товарів функцією number_format(). Використовується JSON строка з масивом для передачі 3х параметрів: кількість десяткових знаків, роздільник десяткових та роздільник тысяч. За замовчуванням формат [2,"."," "], що перетворює "15336.6" в "15 336.60"';
$_lang['setting_ms2_price_format_no_zeros'] = 'Прибирати зайві нулі в цінах';
$_lang['setting_ms2_price_format_no_zeros_desc'] = 'За замовчуванням, ціни товарів виводяться з двома десятковими знаками: "15.20". Якщо увімкнено, зайві нули прибираються і ви отримуєте "15.2".';
$_lang['setting_ms2_weight_format'] = 'Формат ваги';
$_lang['setting_ms2_weight_format_desc'] = 'Вкажіть, як потрібно форматувати вагу товарів функцією number_format(). Використовується JSON строка з масивом для передачі 3х параметрів: кількість десяткових, розділювач десяткових та розділювач тисяч. За замовчуванням формат [3,"."," "], що перетворює "141.3" в "141.300"';
$_lang['setting_ms2_weight_format_no_zeros'] = 'Прибирати зайві нулі з ваги';
$_lang['setting_ms2_weight_format_no_zeros_desc'] = 'За замовчуванням, вага товарів виводиться з трьома десятковими знаками: "15.250". Якщо увімкнено, зайві нулі в кінці ваги прибираються, і ви отримуєте "15.25".';
$_lang['setting_ms2_price_snippet'] = 'Модифікатор ціни';
$_lang['setting_ms2_price_snippet_desc'] = 'Тут ви можете вказати імʼя сніпета для модифікації ціни при виводі на сайті і додаванні в кошик. Він повинен приймати обʼєкт "$product" і повертати число.';
$_lang['setting_ms2_weight_snippet'] = 'Модифікатор ваги';
$_lang['setting_ms2_weight_snippet_desc'] = 'Тут ви можете вказати імʼя сніпета для модифікації ваги товару при виводі на сайті і додаванні в кошик. Він повинен приймати обʼєкт "$product" і повертати число.';

$_lang['setting_ms2_frontend_css'] = 'Стилі фронтенду';
$_lang['setting_ms2_frontend_css_desc'] = 'Шлях до файлу зі стилями магазину. Якщо ви бажаєте використовувати власні стилі - вкажіть шлях до них тут, або очистіть параметр і завантажте їх вручну через шаблон сайту.';
$_lang['setting_ms2_frontend_js'] = 'Скрипти фронтенду';
$_lang['setting_ms2_frontend_js_desc'] = 'Шлях до файлу зі скриптами магазину. Якщо ви бажаєте використовувати власні стилі - вкажіть шлях до них тут, або очистіть параметр і завантажте їх вручну через шаблон сайту.';

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
$_lang['setting_ms2_order_grid_fields'] = 'Поля таблиці замовлень';
$_lang['setting_ms2_order_grid_fields_desc'] = 'Список полів, що будуть показані в таблиці замовлень. Доступні: "createdon,updatedon,num,cost,cart_cost,delivery_cost,weight,status,delivery,payment,customer,receiver".';
$_lang['setting_ms2_order_address_fields'] = 'Поля адреси доставки';
$_lang['setting_ms2_order_address_fields_desc'] = 'Список полів доставки, що будуть показані на третій вкладці картки замовлення. Доступні: "receiver,phone,index,country,region,metro,building,city,street,room". Якщо параметр пустий, вкладка буде схована.';
$_lang['setting_ms2_order_product_fields'] = 'Поля таблиці покупок';
$_lang['setting_ms2_order_product_fields_desc'] = 'Список полів таблиці замовлених товарів. Доступні: "count,price,weight,cost,options". Поля товару вказуються з префіксом "product_", наприклад "product_pagetitle,product_article". Додатково можна вказувати значення з поля options з префіксом "option_", наприклад: "option_color,option_size".';
$_lang['setting_ms2_order_product_options'] = 'Поля опций продукта в заказе';
$_lang['setting_ms2_order_product_options_desc'] = 'Перечень редактируемых опций товара в окне заказа. По умолчанию: "color,size".';

$_lang['ms2_source_thumbnails_desc'] = 'Закодований в JSON масив з параметрами генерації зменшених копій зображень.';
$_lang['ms2_source_maxUploadWidth_desc'] = 'Максимальна ширина зображення для завантаження. Все,  що більше, буде стиснуто до цього значення.';
$_lang['ms2_source_maxUploadHeight_desc'] = 'Максимальна висота зображення для завантаження. Все, що більше, буде стиснуто до цього значення.';
$_lang['ms2_source_maxUploadSize_desc'] = 'Максимальний розмір завантажуваних зображень (в байтах).';
$_lang['ms2_source_imageNameType_desc'] = 'Цей параметр вказує, як потрібно перейменувати файл при завантаженні. Hash - це генерація унікального імені, в залежності від змісту файла. Friendly - генерація імені по алгоритму дружніх url сторінок сайту (вони визначаються системними налаштуваннями).';

// Настройки для альфа релиза miniShop2 4.0.0.beta
$_lang['setting_ms2_frontend_css'] = 'Стилі фронтенду';
$_lang['setting_ms2_frontend_css_desc'] = 'Шлях до файлу зі стилями магазину. Якщо ви бажаєте використовувати власні стилі - вкажіть шлях до них тут, або очистіть параметр і завантажте їх вручну через шаблон сайту.';
$_lang['setting_ms2_frontend_js'] = 'Скрипти фронтенду';
$_lang['setting_ms2_frontend_js_desc'] = 'Шлях до файлу зі скриптами магазину. Якщо ви бажаєте використовувати власні стилі - вкажіть шлях до них тут, або очистіть параметр і завантажте їх вручну через шаблон сайту.';

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
