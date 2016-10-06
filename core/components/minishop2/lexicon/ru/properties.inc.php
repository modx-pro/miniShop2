<?php
/**
 * Properties Russian Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */
$_lang['ms2_prop_limit'] = 'Лимит выборки результатов';
$_lang['ms2_prop_offset'] = 'Пропуск результатов с начала выборки';
$_lang['ms2_prop_depth'] = 'Глубина поиска товаров от каждого родителя.';
$_lang['ms2_prop_sortby'] = 'Сортировка выборки. Для сортировки по полям товара нужно добавить префикс "Data.", например: "&sortby=`Data.price`"';
$_lang['ms2_prop_sortdir'] = 'Направление сортировки';
$_lang['ms2_prop_where'] = 'Дополнительные параметры выборки, закодированные в JSON.';
$_lang['ms2_prop_tpl'] = 'Чанк оформления для каждого результата';
$_lang['ms2_prop_toPlaceholder'] = 'Если не пусто, сниппет сохранит все данные в плейсхолдер с этим именем, вместо вывода не экран.';
$_lang['ms2_prop_toSeparatePlaceholders'] = 'Если вы укажете слово в этом параметре, то ВСЕ результаты будут выставлены в разные плейсхолдеры, начинающиеся с этого слова и заканчивающиеся порядковым номером строки, от нуля. Например, указав в параметре "myPl", вы получите плейсхолдеры [[+myPl0]], [[+myPl1]] и т.д.';
$_lang['ms2_prop_showLog'] = 'Показывать дополнительную информацию о работе сниппета. Только для авторизованных в контексте "mgr".';
$_lang['ms2_prop_parents'] = 'Список категорий, через запятую, для поиска результатов. По умолчанию выборка ограничена текущим родителем. Если поставить 0 - выборка не ограничивается.';
$_lang['ms2_prop_resources'] = 'Список товаров, через запятую, для вывода в результатах. Если id товара начинается с минуса, этот товар исключается из выборки.';
$_lang['ms2_prop_fastMode'] = 'Если включено - в чанк результата будут подставлены только значения из БД. Все необработанные теги MODX, такие как фильтры, вызов сниппетов и другие - будут вырезаны.';
$_lang['ms2_prop_where'] = 'Строка, закодированная в JSON, с дополнительными условиями выборки.';
$_lang['ms2_prop_includeContent'] = 'Выбирать поле "content" у товаров.';
$_lang['ms2_prop_includeTVs'] = 'Список ТВ параметров для выборки, через запятую. Например: "action,time" дадут плейсхолдеры [[+action]] и [[+time]].';
$_lang['ms2_prop_includeThumbs'] = 'Список размеров превьюшек для выборки, через запятую. Например: "120x90,360x240" дадут плейслолдеры [[+120x90]] и [[+360x240]]. Картинки должны быть заранее сгенерированы в галерее товара.';
$_lang['ms2_prop_link'] = 'Id связи товаров, который присваивается автоматически при создании новой связи в настройках.';
$_lang['ms2_prop_master'] = 'Id главного товара. Если указаны и "master" и "slave" - выборка пройдёт по master.';
$_lang['ms2_prop_slave'] = 'Id подчиненного товара. Если указан "master" - эта опция игнорируется.';
$_lang['ms2_prop_class'] = 'Имя класса для выборки. По умолчанию, "msProduct".';
$_lang['ms2_prop_tvPrefix'] = 'Префикс для ТВ плейсхолдеров, например "tv.". По умолчанию параметр пуст.';
$_lang['ms2_prop_outputSeparator'] = 'Необязательная строка для разделения результатов работы.';
$_lang['ms2_prop_returnIds'] = 'Возвращать строку с id товаров, вместо оформленных чанков.';

$_lang['ms2_prop_showUnpublished'] = 'Показывать неопубликованные товары.';
$_lang['ms2_prop_showDeleted'] = 'Показывать удалённые товары.';
$_lang['ms2_prop_showHidden'] = 'Показывать товары, скрытые в меню.';
$_lang['ms2_prop_showZeroPrice'] = 'Показывать товары с нулевой ценой.';

$_lang['ms2_prop_tplRow'] = 'Чанк оформления одного элемента выборки.';
$_lang['ms2_prop_tplSingle'] = 'Чанк оформления единственного результата выборки.';
$_lang['ms2_prop_tplOuter'] = 'Обёртка для вывода результатов работы сниппета.';
$_lang['ms2_prop_tplEmpty'] = 'Чанк, который выводится при отсутствии результатов.';
$_lang['ms2_prop_tplSuccess'] = 'Чанк с сообщением об успешной работе сниппета.';
$_lang['ms2_prop_tplPaymentsOuter'] = 'Чанк для оформления блока возможных методов оплаты.';
$_lang['ms2_prop_tplPaymentsRow'] = 'Чанк для оформления одного метода оплаты.';
$_lang['ms2_prop_tplDeliveriesOuter'] = 'Чанк для оформления блока возможных способов доставки.';
$_lang['ms2_prop_tplDeliveriesRow'] = 'Чанк для оформления одного способа доставки.';

$_lang['ms2_prop_options'] = 'Список опций для вывода, через запятую.';
$_lang['ms2_prop_product'] = 'Идентификатор товара. Если не указан, используется id текущего документа.';
$_lang['ms2_prop_optionSelected'] = 'Имя активной опции, чтобы поставить аттрибут "selected"';
$_lang['ms2_prop_optionName'] = 'Имя опции для вывода.';
$_lang['ms2_prop_filetype'] = 'Тип файлов для выборки. Можно использовать "image" для указания картинок и расширения для остальных файлов. Например: "image,pdf,xls,doc".';
$_lang['ms2_prop_optionFilters'] = 'Фильтры по опциям товаров. Передаются JSON строкой, например, {"optionkey:>":10}';
$_lang['ms2_prop_sortbyOptions'] = 'Указывает по каким опциям и как сортировать среди перечисленного в &sortby. Передаются строкой, например, "optionkey:integer,optionkey2:datetime"';
$_lang['ms2_prop_sortOptions'] = 'Указывает порядок сортировки значений опций. Передаются строкой, например, "size:SORT_DESC:SORT_NUMERIC:100,color:SORT_ASC:SORT_STRING"';
$_lang['ms2_prop_valuesSeparator'] = 'Разделитель для значений множественных опций';
$_lang['ms2_prop_ignoreOptions'] = 'Опции, которые не нужно выводить в списке, через запятую.';
$_lang['ms2_prop_hideEmpty'] = 'Не показывать опции с пустыми значениями.';
$_lang['ms2_prop_groups'] = 'Выводить опции только указанных групп (название или идентификатор категории через запятую, "0" означает без групп)';
$_lang['ms2_prop_tplValue'] = 'Шаблон одного значения (только для множественных опций)';

$_lang['ms2_prop_userFields'] = 'Ассоциативный массив соответствия полей заказа полям профиля пользователя в формате "поле заказа" => "поле профиля".';
$_lang['ms2_prop_wrapIfEmpty'] = 'Включает вывод чанка-обертки (tplWrapper) даже если результатов нет.';