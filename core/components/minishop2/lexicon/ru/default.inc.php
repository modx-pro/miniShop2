<?php
/**
 * Default Russian Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */

include_once('setting.inc.php');

$_lang['minishop2'] = 'miniShop2';
$_lang['ms2_menu_desc'] = 'Продвинутый интернет-магазин';
$_lang['ms2_orders'] = 'Заказы';
$_lang['ms2_orders_intro'] = 'Панель управления заказами';
$_lang['ms2_orders_desc'] = 'Управление заказами';
$_lang['ms2_settings'] = 'Настройки';
$_lang['ms2_settings_intro'] = 'Панель управления настройками магазина. Здесь вы можете указать способы оплаты, доставки и статусы заказов.';
$_lang['ms2_settings_desc'] = 'Статусы заказов, параметры оплаты и доставки';
$_lang['ms2_payments'] = 'Способы оплаты';
$_lang['ms2_payments_intro'] = 'Вы можете создавать любые способы оплаты заказов. Логика оплаты (отправка покупателя на удалённый сервис, приём оплаты и т.п.) реализуется в классе, который вы укажете.<br/>Для методов оплаты параметр "класс" обязателен.';
$_lang['ms2_deliveries'] = 'Варианты доставки';
$_lang['ms2_deliveries_intro'] = 'Возможные варианты доставки. Логика рассчёта стоимости доставки в зависимости от расстояния и веса реализуется классом, который вы укажете в настройках.<br/>Если вы не укажете свой класс, рассчеты будут производиться алгоритмом по-умолчанию.';
$_lang['ms2_statuses'] = 'Статусы заказа';
$_lang['ms2_statuses_intro'] = 'Существует несколько обязательных статусов заказа: "новый", "оплачен", "отправлен" и "отменён". Их можно настраивать, но нельзя удалять, так как они необходимы для работы магазина. Вы можете указать свои статусы для расширенной логики работы с заказами.<br/>Статус может быть окончательным, это значит, что его нельзя переключить на другой, например "отправлен" и "отменён". Стутус может быть зафиксирован, то есть, с него нельзя переключаться на более ранние статусы, например "оплачен" нельзя переключить на "новый".';
$_lang['ms2_vendors'] = 'Производители товаров';
$_lang['ms2_vendors_intro'] = 'Список возможных производителей товаров. То, что вы сюда добавите, можно выбрать в поле "vendor" товара.';


$_lang['ms2_bulk_actions'] = 'Действия';
$_lang['ms2_search'] = 'Поиск';
$_lang['ms2_search_clear'] = 'Очистить';

$_lang['ms2_category'] = 'Категория товаров';
$_lang['ms2_category_tree'] = 'Дерево категорий';
$_lang['ms2_category_type'] = 'Категория товаров';
$_lang['ms2_category_create'] = 'Добавить категорию';
$_lang['ms2_category_create_here'] = 'Категорию с товарами';
$_lang['ms2_category_manage'] = 'Управление товарами';
$_lang['ms2_category_duplicate'] = 'Копировать категорию';
$_lang['ms2_category_publish'] = 'Опубликовать категорию';
$_lang['ms2_category_unpublish'] = 'Убрать с публикации';
$_lang['ms2_category_delete'] = 'Удалить категорию';
$_lang['ms2_category_undelete'] = 'Восстановить категорию';
$_lang['ms2_category_view'] = 'Просмотреть на сайте';
$_lang['ms2_category_new'] = 'Новая категория';

$_lang['ms2_product'] = 'Товар магазина';
$_lang['ms2_product_type'] = 'Товар магазина';
$_lang['ms2_product_create_here'] = 'Товар категории';
$_lang['ms2_product_create'] = 'Добавить товар';

$_lang['ms2_frontend_currency'] = 'руб.';
$_lang['ms2_frontend_weight_unit'] = 'кг.';
$_lang['ms2_frontend_count_unit'] = 'шт.';
$_lang['ms2_frontend_add_to_cart'] = 'Добавить в корзину';
$_lang['ms2_frontend_tags'] = 'Теги';
$_lang['ms2_frontend_colors'] = 'Цвета';
$_lang['ms2_frontend_color'] = 'Цвет';
$_lang['ms2_frontend_sizes'] = 'Размеры';
$_lang['ms2_frontend_size'] = 'Размер';
$_lang['ms2_frontend_popular'] = 'Популярный товар';
$_lang['ms2_frontend_favorite'] = 'Рекомендуем';
$_lang['ms2_frontend_new'] = 'Новика';
$_lang['ms2_frontend_deliveries'] = 'Варианты доставки';
$_lang['ms2_frontend_payments'] = 'Способы оплаты';
$_lang['ms2_frontend_delivery_select'] = 'Выберите доставку';
$_lang['ms2_frontend_payment_select'] = 'Выберите оплату';
$_lang['ms2_frontend_credentials'] = 'Данные получателя';
$_lang['ms2_frontend_address'] = 'Адрес доставки';

$_lang['ms2_frontend_comment'] = 'Комментарий';
$_lang['ms2_frontend_receiver'] = 'Получатель';
$_lang['ms2_frontend_email'] = 'Email';
$_lang['ms2_frontend_phone'] = 'Телефон';
$_lang['ms2_frontend_index'] = 'Почтовый индекс';
$_lang['ms2_frontend_region'] = 'Область';
$_lang['ms2_frontend_city'] = 'Город';
$_lang['ms2_frontend_street'] = 'Улица';
$_lang['ms2_frontend_building'] = 'Дом';
$_lang['ms2_frontend_room'] = 'Комната';

$_lang['ms2_frontend_order_cost'] = 'Итого, с доставкой';
$_lang['ms2_frontend_order_submit'] = 'Сделать заказ!';
$_lang['ms2_frontend_order_cancel'] = 'Очистить форму';
$_lang['ms2_frontend_order_success'] = 'Спасибо за оформление заказа <b>#[[+num]]</b> на нашем сайте <b>[[++site_name]]</b>!';

$_lang['ms2_message_close_all'] = 'закрыть все';
$_lang['ms2_err_unknown'] = 'Неизвестная ошибка';
$_lang['ms2_err_ae'] = 'Это поле должно быть уникально';