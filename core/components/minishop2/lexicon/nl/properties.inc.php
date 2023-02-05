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
$_lang['ms2_prop_groups'] = 'Show options only by chosen groups (name or id of category separated by comma, "0" means no group).';
$_lang['ms2_prop_hideEmpty'] = 'Hide options with empty value.';
$_lang['ms2_prop_ignoreGroups'] = 'Группы, опции которых не нужно выводить в списке, через запятую.';
$_lang['ms2_prop_ignoreOptions'] = 'Options that should be ignored by snippet, comma-separated list';
$_lang['ms2_prop_includeContent'] = 'Retrieve field "content" from products.';
$_lang['ms2_prop_includeTVs'] = 'An optional comma-delimited list of TemplateVar names to include in selection. For example "action,time" give you placeholders [[+action]] and [[+time]].';
$_lang['ms2_prop_includeThumbs'] = 'An optional comma-delimited list of Thumbnail sizes to include in selection. For example: "small,medium" give you placeholders [[+small]] and [[+medium]]. Thumbnails must be generted in gallery of product.';
$_lang['ms2_prop_limit'] = 'The number of results to limit.';
$_lang['ms2_prop_link'] = 'ID связи товаров, который присваивается автоматически при создании новой связи в настройках.';
$_lang['ms2_prop_master'] = 'ID главного товара. Если указаны и "master" и "slave" - выборка пройдёт по master.';
$_lang['ms2_prop_offset'] = 'An offset of resources returned by the criteria to skip';
$_lang['ms2_prop_onlyOptions'] = 'Show only this comma-separated list of options.';
$_lang['ms2_prop_optionFilters'] = 'Filters by product options via JSON, e.g. {"optionkey:>":10}';
$_lang['ms2_prop_optionName'] = 'Name of the option for displaying.';
$_lang['ms2_prop_optionSelected'] = 'Name of the active option, for setting attribute "selected"';
$_lang['ms2_prop_options'] = 'Comma-separated list of options to output.';
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
$_lang['ms2_prop_sortbyOptions'] = 'Lists options from &sortby for sorting with type via string, e.g. "optionkey:integer,optionkey2:datetime"';
$_lang['ms2_prop_sortdir'] = 'Sortierreihenfolge';
$_lang['ms2_prop_toPlaceholder'] = 'If not empty, the snippet will save output to placeholder with that name, instead of return it to screen.';
$_lang['ms2_prop_toSeparatePlaceholders'] = 'If set, will assign EACH result to a separate placeholder named by this param suffixed with a sequential number (starting from 0).';
$_lang['ms2_prop_tpl'] = 'Chunk Template, das für jede Row verwendet wird.';
$_lang['ms2_prop_tplDeliveriesOuter'] = 'Chunk for templating of a block of possible ways of deliveries.';
$_lang['ms2_prop_tplDeliveriesRow'] = 'Chunk to process a way of delivery.';
$_lang['ms2_prop_tplEmpty'] = 'Chunk, der angezeigt wird, wenn keine Ergebnisse ausgegeben werden können.';
$_lang['ms2_prop_tplOuter'] = 'Wrapper for template results of snippet work.';
$_lang['ms2_prop_tplPaymentsOuter'] = 'Chunk for templating of a block of possible payment methods.';
$_lang['ms2_prop_tplPaymentsRow'] = 'Chunk to process a payment method.';
$_lang['ms2_prop_tplRow'] = 'Chunk for template one row of query.';
$_lang['ms2_prop_tplSingle'] = 'Chunk for template single row of query.';
$_lang['ms2_prop_tplSuccess'] = 'Chunk with successfull message about snippet work.';
$_lang['ms2_prop_tplValue'] = 'Chunk for templating of one value for multiple options';
$_lang['ms2_prop_tvPrefix'] = 'The prefix for TemplateVar properties, "tv." for example. By default it is empty.';
$_lang['ms2_prop_userFields'] = 'An associative array of order and user fields in format "order field" => "user field".';
$_lang['ms2_prop_valuesSeparator'] = 'Separator between values in multiple options';
$_lang['ms2_prop_where'] = 'A JSON-style expression of criteria to build any additional where clauses from.';
$_lang['ms2_prop_wrapIfEmpty'] = 'If true, will output the wrapper specified in &tplWrapper even if the output is empty.';
