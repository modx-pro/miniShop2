<?php

/**
 * Properties Lexicon Entries
 * Sorted by key, alphabetically
 *
 * @package minishop2
 * @subpackage lexicon
 */

$_lang['ms2_prop_class'] = 'Name of class for selection. By default, "msProduct".';
$_lang['ms2_prop_depth'] = 'Integer value indicating depth to search for resources from each parent.';
$_lang['ms2_prop_fastMode'] = 'If enabled, then in chunk will be only received values ​​from the database. All raw tags of MODX, such as filters, snippets calls will be cut.';
$_lang['ms2_prop_filetype'] = 'Typ der Dateien, die für eine Stichprobe. Sie können das "image", um die Bilder und Erweiterungen für den Rest der Dateien. Zum Beispiel: "image,pdf,xls,doc".';
$_lang['ms2_prop_groups'] = 'Выводить опции только указанных групп (название или идентификатор категории через запятую, "0" означает без групп)';
$_lang['ms2_prop_hideEmpty'] = 'Не показывать опции с пустыми значениями.';
$_lang['ms2_prop_ignoreGroups'] = 'Группы, опции которых не нужно выводить в списке, через запятую.';
$_lang['ms2_prop_ignoreOptions'] = 'Опции, которые не нужно выводить в списке, через запятую.';
$_lang['ms2_prop_includeContent'] = 'Retrieve field "content" from products.';
$_lang['ms2_prop_includeTVs'] = 'An optional comma-delimited list of TemplateVar names to include in selection. For example "action,time" give you placeholders [[+action]] and [[+time]].';
$_lang['ms2_prop_includeThumbs'] = 'An optional comma-delimited list of Thumbnail sizes to include in selection. For example: "small,medium" give you placeholders [[+small]] and [[+medium]]. Thumbnails must be generted in gallery of product.';
$_lang['ms2_prop_limit'] = 'Anzahl der zu begrenzenden Ergebnisse';
$_lang['ms2_prop_link'] = 'ID связи товаров, который присваивается автоматически при создании новой связи в настройках.';
$_lang['ms2_prop_master'] = 'ID главного товара. Если указаны и "master" и "slave" - выборка пройдёт по master.';
$_lang['ms2_prop_offset'] = 'An offset of resources returned by the criteria to skip';
$_lang['ms2_prop_onlyOptions'] = 'Выводить только этот список опций, указанный через запятую';
$_lang['ms2_prop_optionFilters'] = 'Фильтры по опциям товаров. Передаются JSON строкой, например, {"optionkey:>":10}';
$_lang['ms2_prop_optionName'] = 'Name of the option for displaying.';
$_lang['ms2_prop_optionSelected'] = 'Name of the active option, for setting attribute "selected"';
$_lang['ms2_prop_options'] = 'Список опций для вывода, через запятую.';
$_lang['ms2_prop_outputSeparator'] = 'An optional string to separate each tpl instance.';
$_lang['ms2_prop_parents'] = 'Container list, separated by commas, to search results. By default, the query is limited to the current parent. If set to 0, query not limited.';
$_lang['ms2_prop_product'] = 'ID des Produkts. Falls leer, wird die ID des aktuellen Dokuments verwendet.';
$_lang['ms2_prop_resources'] = 'Список товаров, через запятую, для вывода в результатах. Если ID товара начинается с минуса, этот товар исключается из выборки.';
$_lang['ms2_prop_return'] = 'Способ вывода результатов';
$_lang['ms2_prop_returnIds'] = 'Возвращать строку с ID товаров, вместо оформленных чанков.';
$_lang['ms2_prop_showDeleted'] = 'Gelöschte Produkte anzeigen.';
$_lang['ms2_prop_showHidden'] = 'Produkte anzeigen, die im Menü verborgen sind.';
$_lang['ms2_prop_showLog'] = 'Display additional information about snippet work. Only for authenticated in context "mgr".';
$_lang['ms2_prop_showUnpublished'] = 'Unveröffentlichte Produkte anzeigen.';
$_lang['ms2_prop_showZeroPrice'] = 'Produkte ohne Preis anzeigen.';
$_lang['ms2_prop_slave'] = 'ID подчиненного товара. Если указан "master" - эта опция игнорируется.';
$_lang['ms2_prop_sortGroups'] = 'Указывает порядок сортировки групп опций. Принимает как ID-шники, так и текстовые названия групп. Передаются строкой, например: "22,23,24" или "Размеры,Электроника,Разное".';
$_lang['ms2_prop_sortOptionValues'] = 'Указывает порядок сортировки значений опций. Передаются строкой, например: "size:SORT_DESC:SORT_NUMERIC:100,color:SORT_ASC:SORT_STRING"';
$_lang['ms2_prop_sortOptions'] = 'Указывает порядок сортировки опций. Передаются строкой, например: "size,color".';
$_lang['ms2_prop_sortby'] = 'The field to sort by. For sorting by product fields you need to add prefix "Data.", for example: "&sortby=`Data.price`"';
$_lang['ms2_prop_sortbyOptions'] = 'Указывает по каким опциям и как сортировать среди перечисленного в &sortby. Передаются строкой, например, "optionkey:integer,optionkey2:datetime"';
$_lang['ms2_prop_sortdir'] = 'Sortierreihenfolge';
$_lang['ms2_prop_toPlaceholder'] = 'If not empty, the snippet will save output to placeholder with that name, instead of return it to screen.';
$_lang['ms2_prop_toSeparatePlaceholders'] = 'Если вы укажете слово в этом параметре, то ВСЕ результаты будут выставлены в разные плейсхолдеры, начинающиеся с этого слова и заканчивающиеся порядковым номером строки, от нуля. Например, указав в параметре "myPl", вы получите плейсхолдеры [[+myPl0]], [[+myPl1]] и т.д.';
$_lang['ms2_prop_tpl'] = 'Chunk Template, das für jede Row verwendet wird.';
$_lang['ms2_prop_tplDeliveriesOuter'] = 'Chunk for templating of a block of possible ways of deliveries.';
$_lang['ms2_prop_tplDeliveriesRow'] = 'Chunk to process a way of delivery.';
$_lang['ms2_prop_tplEmpty'] = 'Chunk, der angezeigt wird, wenn keine Ergebnisse ausgegeben werden können.';
$_lang['ms2_prop_tplOuter'] = 'Wrapper for template results of snippet work.';
$_lang['ms2_prop_tplPaymentsOuter'] = 'Chunk for templating of a block of possible payment methods.';
$_lang['ms2_prop_tplPaymentsRow'] = 'Chunk to process a payment method.';
$_lang['ms2_prop_tplRow'] = 'Chunk for template one row of query.';
$_lang['ms2_prop_tplSingle'] = 'Чанк оформления единственного результата выборки.';
$_lang['ms2_prop_tplSuccess'] = 'Chunk with successfull message about snippet work.';
$_lang['ms2_prop_tplValue'] = 'Шаблон одного значения (только для множественных опций)';
$_lang['ms2_prop_tvPrefix'] = 'The prefix for TemplateVar properties, "tv." for example. By default it is empty.';
$_lang['ms2_prop_userFields'] = 'Ассоциативный массив соответствия полей заказа полям профиля пользователя в формате "поле заказа" => "поле профиля".';
$_lang['ms2_prop_valuesSeparator'] = 'Разделитель для значений множественных опций';
$_lang['ms2_prop_where'] = 'A JSON-style expression of criteria to build any additional where clauses from.';
$_lang['ms2_prop_wrapIfEmpty'] = 'Включает вывод чанка-обертки (tplWrapper) даже если результатов нет.';
