<?php
/**
 * Default Russian Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */

include_once('setting.inc.php');
$files = scandir(dirname(__FILE__));
foreach ($files as $file) {
    if (strpos($file, 'msp.') === 0) {
        @include_once($file);
    }
}

$_lang['minishop2'] = 'miniShop2';
$_lang['ms2_menu_desc'] = 'Продвинутый интернет-магазин';
$_lang['ms2_order'] = 'Заказ';
$_lang['ms2_orders'] = 'Заказы';
$_lang['ms2_orders_intro'] = 'Панель управления заказами. Вы можете выбирать сразу несколько заказов через Shift или Ctrl(Cmd).';
$_lang['ms2_orders_desc'] = 'Управление заказами';
$_lang['ms2_settings'] = 'Настройки';
$_lang['ms2_settings_intro'] = 'Панель управления настройками магазина. Здесь вы можете указать способы оплаты, доставки и статусы заказов.';
$_lang['ms2_settings_desc'] = 'Статусы заказов, параметры оплаты и доставки';
$_lang['ms2_payment'] = 'Оплата';
$_lang['ms2_payments'] = 'Способы оплаты';
$_lang['ms2_payments_intro'] = 'Вы можете создавать любые способы оплаты заказов. Логика оплаты (отправка покупателя на удалённый сервис, приём оплаты и т.п.) реализуется в классе, который вы укажете.<br/>Для методов оплаты параметр "класс" обязателен.';
$_lang['ms2_delivery'] = 'Доставка';
$_lang['ms2_deliveries'] = 'Варианты доставки';
$_lang['ms2_deliveries_intro'] = 'Возможные варианты доставки. Логика рассчёта стоимости доставки в зависимости от расстояния и веса реализуется классом, который вы укажете в настройках.<br/>Если вы не укажете свой класс, рассчеты будут производиться алгоритмом по-умолчанию.';
$_lang['ms2_statuses'] = 'Статусы заказа';
$_lang['ms2_statuses_intro'] = 'Существует несколько обязательных статусов заказа: "новый", "оплачен", "отправлен" и "отменён". Их можно настраивать, но нельзя удалять, так как они необходимы для работы магазина. Вы можете указать свои статусы для расширенной логики работы с заказами.<br/>Статус может быть окончательным, это значит, что его нельзя переключить на другой, например "отправлен" и "отменён". Стутус может быть зафиксирован, то есть, с него нельзя переключаться на более ранние статусы, например "оплачен" нельзя переключить на "новый".';
$_lang['ms2_vendors'] = 'Производители товаров';
$_lang['ms2_vendors_intro'] = 'Список возможных производителей товаров. То, что вы сюда добавите, можно выбрать в поле "vendor" товара.';
$_lang['ms2_link'] = 'Связь товаров';
$_lang['ms2_links'] = 'Связи товаров';
$_lang['ms2_links_intro'] = 'Список возможных связей товаров друг с другом. Тип связи характеризует, как именно она будет работать, его нельзя создавать, можно только выбрать из списка.';
$_lang['ms2_option'] = 'Свойство товаров';
$_lang['ms2_options'] = 'Свойства товаров';
$_lang['ms2_options_intro'] = 'Список возможных свойств товаров. Дерево категорий используется для фильтрации свойств выбранных категорий.<br/>Чтобы назначить категориям сразу несколько опций, выберите их через Ctrl(Cmd) или Shift.';
$_lang['ms2_options_category_intro'] = 'Список возможных свойств товаров в данной категории.';
$_lang['ms2_default_value'] = 'Значение по умолчанию';
$_lang['ms2_customer'] = 'Покупатель';
$_lang['ms2_all'] = 'Все';
$_lang['ms2_type'] = 'Тип';

$_lang['ms2_btn_create'] = 'Создать';
$_lang['ms2_btn_copy'] = 'Скопировать';
$_lang['ms2_btn_save'] = 'Сохранить';
$_lang['ms2_btn_edit'] = 'Изменить';
$_lang['ms2_btn_view'] = 'Просмотр';
$_lang['ms2_btn_delete'] = 'Удалить';
$_lang['ms2_btn_undelete'] = 'Восстановить';
$_lang['ms2_btn_publish'] = 'Включить';
$_lang['ms2_btn_unpublish'] = 'Отключить';
$_lang['ms2_btn_cancel'] = 'Отмена';
$_lang['ms2_btn_back'] = 'Назад (alt + &uarr;)';
$_lang['ms2_btn_prev'] = 'Предыдущий товар (alt + &larr;)';
$_lang['ms2_btn_next'] = 'Следующий товар (alt + &rarr;)';
$_lang['ms2_btn_help'] = 'Помощь';
$_lang['ms2_btn_duplicate'] = 'Сделать копию товара';
$_lang['ms2_btn_addoption'] = 'Добавить';
$_lang['ms2_btn_assign'] = 'Назначить';

$_lang['ms2_actions'] = 'Действия';
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
$_lang['ms2_category_option_add'] = 'Добавить характеристику';
$_lang['ms2_category_option_rank'] = 'Порядок сортировки';
$_lang['ms2_category_show_nested'] = 'Показывать вложенные товары';

$_lang['ms2_product'] = 'Товар магазина';
$_lang['ms2_product_type'] = 'Товар магазина';
$_lang['ms2_product_create_here'] = 'Товар магазина';
$_lang['ms2_product_create'] = 'Добавить товар';

$_lang['ms2_option_type'] = 'Тип свойства';

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
$_lang['ms2_frontend_new'] = 'Новинка';
$_lang['ms2_frontend_deliveries'] = 'Варианты доставки';
$_lang['ms2_frontend_delivery'] = 'Доставка';
$_lang['ms2_frontend_payments'] = 'Способы оплаты';
$_lang['ms2_frontend_payment'] = 'Оплата';
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
$_lang['ms2_err_ns'] = 'Это поле обязательно';
$_lang['ms2_err_ae'] = 'Это поле должно быть уникально';
$_lang['ms2_err_json'] = 'Это поле требует JSON строку';
$_lang['ms2_err_order_nf'] = 'Заказ с таким идентификатором не найден.';
$_lang['ms2_err_status_nf'] = 'Статус с таким идентификатором не найден.';
$_lang['ms2_err_delivery_nf'] = 'Способ доставки с таким идентификатором не найден.';
$_lang['ms2_err_payment_nf'] = 'Способ оплаты с таким идентификатором не найден.';
$_lang['ms2_err_status_final'] = 'Установлен финальный статус. Его нельзя менять.';
$_lang['ms2_err_status_fixed'] = 'Установлен фиксирующий статус. Вы не можете сменить его на более ранний.';
$_lang['ms2_err_status_wrong'] = 'Неверный статус заказа.';
$_lang['ms2_err_status_same'] = 'Этот статус уже установлен.';
$_lang['ms2_err_register_globals'] = 'Ошибка: php параметр <b>register_globals</b> должен быть выключен.';
$_lang['ms2_err_link_equal'] = 'Вы пытаетесь добавить товару ссылку на самого себя';
$_lang['ms2_err_value_duplicate'] = 'Вы не ввели значение или ввели повтор.';

$_lang['ms2_err_gallery_save'] = 'Не могу сохранить файл не был сохранён (см. системный журнал).';
$_lang['ms2_err_gallery_ns'] = 'Передан пустой файл';
$_lang['ms2_err_gallery_ext'] = 'Неверное расширение файла';
$_lang['ms2_err_gallery_exists'] = 'Такое изображение уже есть в галерее товара.';
$_lang['ms2_err_gallery_thumb'] = 'Не получилось сгенерировать превьюшки. Смотрите системный лог.';
$_lang['ms2_err_wrong_image'] = 'Файл не является корректным изображением.';

$_lang['ms2_email_subject_new_user'] = 'Вы сделали заказ #[[+num]] на сайте [[++site_name]]';
$_lang['ms2_email_subject_new_manager'] = 'У вас новый заказ #[[+num]]';
$_lang['ms2_email_subject_paid_user'] = 'Вы оплатили заказ #[[+num]]';
$_lang['ms2_email_subject_paid_manager'] = 'Заказ #[[+num]] был оплачен';
$_lang['ms2_email_subject_sent_user'] = 'Ваш заказ #[[+num]] был отправлен';
$_lang['ms2_email_subject_cancelled_user'] = 'Ваш заказ #[[+num]] был отменён';

$_lang['ms2_payment_link'] = 'Если вы случайно прервали процедуру оплаты, вы всегда можете <a href="[[+link]]" style="color:#348eda;">продолжить её по этой ссылке</a>.';

$_lang['ms2_category_err_ns'] = 'Категория не выбрана';
$_lang['ms2_option_err_ns'] = 'Свойство не выбрано';
$_lang['ms2_option_err_nf'] = 'Свойство не найдено';
$_lang['ms2_option_err_ae'] = 'Свойство уже существует';
$_lang['ms2_option_err_save'] = 'Ошибка сохранения свойства';
$_lang['ms2_option_err_reserved_key'] = 'Такой ключ опции использовать нельзя';
$_lang['ms2_option_err_invalid_key'] = 'Неправильный ключ для свойства';