<?php

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}
$pdoFetch->addTime('pdoTools loaded.');

if (isset($parents) && $parents === '') {
    $scriptProperties['parents'] = $modx->resource->id;
}

if (!empty($returnIds)) {
    $scriptProperties['return'] = 'ids';
}

if ($scriptProperties['return'] === 'ids') {
    $scriptProperties['returnIds'] = true;
}

// Start build "where" expression
$where = [
    'class_key' => 'msProduct',
];
if (empty($showZeroPrice)) {
    $where['Data.price:>'] = 0;
}
// Add grouping
$groupby = [
    'msProduct.id',
];

// Join tables
$leftJoin = [
    'Data' => ['class' => 'msProductData'],
    'Vendor' => ['class' => 'msVendor', 'on' => 'Data.vendor=Vendor.id'],
];

$select = [
    'msProduct' => !empty($includeContent)
        ? $modx->getSelectColumns('msProduct', 'msProduct')
        : $modx->getSelectColumns('msProduct', 'msProduct', '', ['content'], true),
    'Data' => $modx->getSelectColumns('msProductData', 'Data', '', ['id'], true),
    'Vendor' => $modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', ['id'], true),
];

// Include thumbnails
if (!empty($includeThumbs)) {
    $thumbs = array_map('trim', explode(',', $includeThumbs));
    foreach ($thumbs as $thumb) {
        if (empty($thumb)) {
            continue;
        }
        $leftJoin[$thumb] = [
            'class' => 'msProductFile',
            'on' => "`{$thumb}`.product_id = msProduct.id AND `{$thumb}`.`rank` = 0 AND `{$thumb}`.path LIKE '%/{$thumb}/%'",
        ];
        $select[$thumb] = "`{$thumb}`.url as `{$thumb}`";
        $groupby[] = "`{$thumb}`.url";
    }
}

// Include linked products
$innerJoin = [];
if (!empty($link) && !empty($master)) {
    $innerJoin['Link'] = [
        'class' => 'msProductLink',
        'on' => 'msProduct.id = Link.slave AND Link.link = ' . $link,
    ];
    $where['Link.master'] = $master;
} elseif (!empty($link) && !empty($slave)) {
    $innerJoin['Link'] = [
        'class' => 'msProductLink',
        'on' => 'msProduct.id = Link.master AND Link.link = ' . $link,
    ];
    $where['Link.slave'] = $slave;
}

// Add user parameters
foreach (['where', 'leftJoin', 'innerJoin', 'select', 'groupby'] as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}
$pdoFetch->addTime('Conditions prepared');

// Workaround: оптимизация обработки parents
// pdoTools использует getChildIds() который возвращает ВСЕ дочерние ресурсы
// Мы фильтруем только msCategory для повышения производительности
$_ms2Parents = isset($scriptProperties['parents']) ? (string)$scriptProperties['parents'] : '';
if ($_ms2Parents !== '' && $_ms2Parents !== '0') {
    $_ms2Depth = isset($scriptProperties['depth']) ? (int)$scriptProperties['depth'] : 10;
    $_ms2ParentsIn = [];
    $_ms2ParentsOut = [];

    // Разбираем parents: положительные - включить, отрицательные - исключить
    foreach (array_map('trim', explode(',', $_ms2Parents)) as $_ms2Parent) {
        $_ms2Parent = (int)$_ms2Parent;
        if ($_ms2Parent > 0) {
            $_ms2ParentsIn[] = $_ms2Parent;
        } elseif ($_ms2Parent < 0) {
            $_ms2ParentsOut[] = abs($_ms2Parent);
        }
    }

    // Получаем дочерние категории для включения (только msCategory, не все ресурсы)
    if (!empty($_ms2ParentsIn) && $_ms2Depth > 0) {
        $_ms2CatIds = $_ms2ParentsIn;
        for ($_ms2i = 0; $_ms2i < $_ms2Depth; $_ms2i++) {
            $_ms2CatQuery = $modx->newQuery('msCategory');
            $_ms2CatQuery->where([
                'class_key' => 'msCategory',
                'parent:IN' => $_ms2CatIds,
                'published' => 1,
                'deleted' => 0,
            ]);
            $_ms2CatQuery->select('id');

            if ($_ms2CatQuery->prepare() && $_ms2CatQuery->stmt->execute()) {
                $_ms2ChildIds = $_ms2CatQuery->stmt->fetchAll(PDO::FETCH_COLUMN);
                if (empty($_ms2ChildIds)) {
                    break;
                }
                $_ms2ParentsIn = array_merge($_ms2ParentsIn, $_ms2ChildIds);
                $_ms2CatIds = $_ms2ChildIds;
            } else {
                break;
            }
        }
    }

    // Получаем дочерние категории для исключения
    if (!empty($_ms2ParentsOut) && $_ms2Depth > 0) {
        $_ms2CatIds = $_ms2ParentsOut;
        for ($_ms2i = 0; $_ms2i < $_ms2Depth; $_ms2i++) {
            $_ms2CatQuery = $modx->newQuery('msCategory');
            $_ms2CatQuery->where([
                'class_key' => 'msCategory',
                'parent:IN' => $_ms2CatIds,
                'published' => 1,
                'deleted' => 0,
            ]);
            $_ms2CatQuery->select('id');

            if ($_ms2CatQuery->prepare() && $_ms2CatQuery->stmt->execute()) {
                $_ms2ChildIds = $_ms2CatQuery->stmt->fetchAll(PDO::FETCH_COLUMN);
                if (empty($_ms2ChildIds)) {
                    break;
                }
                $_ms2ParentsOut = array_merge($_ms2ParentsOut, $_ms2ChildIds);
                $_ms2CatIds = $_ms2ChildIds;
            } else {
                break;
            }
        }
    }

    // Вычитаем исключённые категории из включённых
    $_ms2ParentsIn = array_unique($_ms2ParentsIn);
    $_ms2ParentsOut = array_unique($_ms2ParentsOut);
    if (!empty($_ms2ParentsOut)) {
        $_ms2ParentsIn = array_diff($_ms2ParentsIn, $_ms2ParentsOut);
    }

    // ВСЕГДА отключаем pdoTools parent processing - мы обрабатываем сами
    if (!empty($_ms2ParentsIn)) {
        $_ms2ParentsList = implode(',', array_map('intval', $_ms2ParentsIn));

        // Получаем товары из дополнительных категорий
        $_ms2MemberQuery = $modx->newQuery('msCategoryMember');
        $_ms2MemberQuery->where(['category_id:IN' => $_ms2ParentsIn]);
        $_ms2MemberQuery->select('product_id');

        $_ms2MemberIds = [];
        if ($_ms2MemberQuery->prepare() && $_ms2MemberQuery->stmt->execute()) {
            $_ms2MemberIds = $_ms2MemberQuery->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        // Строим WHERE: parent IN категориях, опционально OR id IN доп. категориях
        if (!empty($_ms2MemberIds)) {
            $_ms2MembersList = implode(',', array_map('intval', $_ms2MemberIds));
            $where[] = "(`msProduct`.`parent` IN ({$_ms2ParentsList}) OR `msProduct`.`id` IN ({$_ms2MembersList}))";
        } else {
            // Нет товаров в доп. категориях - просто фильтруем по parent
            $where[] = "`msProduct`.`parent` IN ({$_ms2ParentsList})";
        }

        // ВСЕГДА отключаем стандартную фильтрацию pdoTools по parents
        $scriptProperties['parents'] = 0;
    }
}

// Add filters by options
$joinedOptions = [];
if (!empty($scriptProperties['optionFilters'])) {
    $filters = json_decode($scriptProperties['optionFilters'], true);
    foreach ($filters as $key => $value) {
        $components = explode(':', $key, 2);

        if (count($components) === 2) {
            if (in_array(strtolower($components[0]), ['or', 'and'])) {
                [$operator, $key] = $components;
            }
        }

        $option = preg_replace('#\:.*#', '', $key);
        $key = str_replace($option, $option . '.value', $key);

        if (!in_array($option, $joinedOptions)) {
            $leftJoin[$option] = [
                'class' => 'msProductOption',
                'on' => "`{$option}`.product_id = Data.id AND `{$option}`.key = '{$option}'",
            ];
            $joinedOptions[] = $option;
        }

        $index = isset($operator) && in_array(strtolower($operator), ['or', 'and'], true)
            ? sprintf('%s:%s', strtoupper($operator), $key)
            : $key;
        $where[$index] = $value;
    }
}

// Add sort by options
if (!empty($scriptProperties['sortbyOptions'])) {
    $sorts = array_map('trim', explode(',', $scriptProperties['sortbyOptions']));
    foreach ($sorts as $sort) {
        $sort = explode(':', $sort);
        $option = $sort[0];
        if (preg_match("#\b{$option}\b#", $scriptProperties['sortby'], $matches)) {
            $type = 'string';
            if (isset($sort[1])) {
                $type = $sort[1];
            }
            switch ($type) {
                case 'number':
                case 'decimal':
                    $sortbyOptions = "CAST(`{$option}`.`value` AS DECIMAL(13,3))";
                    break;
                case 'int':
                case 'integer':
                    $sortbyOptions = "CAST(`{$option}`.`value` AS UNSIGNED INTEGER)";
                    break;
                case 'date':
                case 'datetime':
                    $sortbyOptions = "CAST(`{$option}`.`value` AS DATETIME)";
                    break;
                default:
                    $sortbyOptions = "`{$option}`.`value`";
                    break;
            }
            $scriptProperties['sortby'] = preg_replace("#\b{$option}\b#", $sortbyOptions, $scriptProperties['sortby']);
            $groupby[] = "`{$option}`.value";
        }

        if (!in_array($option, $joinedOptions)) {
            $leftJoin[$option] = [
                'class' => 'msProductOption',
                'on' => "`{$option}`.product_id = Data.id AND `{$option}`.key = '{$option}'",
            ];
            $joinedOptions[] = $option;
        }
    }
}

$default = [
    'class' => 'msProduct',
    'where' => $where,
    'leftJoin' => $leftJoin,
    'innerJoin' => $innerJoin,
    'select' => $select,
    'sortby' => 'msProduct.id',
    'sortdir' => 'ASC',
    'groupby' => implode(', ', $groupby),
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
];
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$rows = $pdoFetch->run();

if ($scriptProperties['return'] == 'json') {
    $rows = json_decode($rows, true);
}

// Process rows
$output = $additionalPlaceholders = [];
if (!empty($rows) && is_array($rows)) {
    $c = $modx->newQuery(
        'modPluginEvent',
        ['event:IN' => ['msOnGetProductPrice', 'msOnGetProductWeight', 'msOnGetProductFields']]
    );
    $c->innerJoin('modPlugin', 'modPlugin', 'modPlugin.id = modPluginEvent.pluginid');
    $c->where('modPlugin.disabled = 0');

    $modifications = $modx->getOption('ms2_price_snippet', null, false, true) ||
        $modx->getOption('ms2_weight_snippet', null, false, true) || $modx->getCount('modPluginEvent', $c);
    if ($modifications) {
        /** @var msProductData $product */
        $product = $modx->newObject('msProductData');
    }
    $pdoFetch->addTime('Checked the active modifiers');

    // Adding extra parameters into special place so we can put them in a results
    /** @var modSnippet $snippet */
    $properties = [];
    if (isset($this) && $this instanceof modSnippet && $this->get('properties')) {
        $properties = $this->get('properties');
    } elseif ($snippet = $modx->getObject('modSnippet', ['name' => 'msProduct'])) {
        $properties = $snippet->get('properties');
    }
    if (!empty($properties)) {
        foreach ($scriptProperties as $k => $v) {
            if (!isset($properties[$k])) {
                $additionalPlaceholders[$k] = $v;
            }
        }
    }
    $opt_time = 0;
    foreach ($rows as $k => $row) {
        if ($modifications) {
            $product->fromArray($row, '', true, true);
            $tmp = $row['price'];
            $row['price'] = $product->getPrice($row);
            $row['weight'] = $product->getWeight($row);
            // A discount here, so we should replace old price
            if ($row['price'] < $tmp) {
                $row['old_price'] = $tmp;
            }
            $row = $product->modifyFields($row);
        }
        $row['price'] = $miniShop2->formatPrice($row['price']);
        $row['old_price'] = $miniShop2->formatPrice($row['old_price']);
        $row['weight'] = $miniShop2->formatWeight($row['weight']);
        $row['idx'] = $pdoFetch->idx++;

        $opt_time_start = microtime(true);
        $options = $modx->call('msProductData', 'loadOptions', [$modx, $row['id']]);
        $rows[$k] = $row = array_merge($additionalPlaceholders, $row, $options);
        $opt_time += microtime(true) - $opt_time_start;

        $tpl = $pdoFetch->defineChunk($row);
        $output[] = $pdoFetch->getChunk($tpl, $row);
    }
    $pdoFetch->addTime('Time to load products options', $opt_time);
}

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="msProductsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if ($scriptProperties['return'] == 'json') {
    $rows = json_encode($rows);
}

// Return output
if (is_string($rows)) {
    $modx->setPlaceholder('msProducts.log', $log);
    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $rows);
    } else {
        return $rows;
    }
} elseif (!empty($toSeparatePlaceholders)) {
    $output['log'] = $log;
    $modx->setPlaceholders($output, $toSeparatePlaceholders);
} else {
    if (empty($outputSeparator)) {
        $outputSeparator = "\n";
    }
    $output['log'] = $log;
    $output = implode($outputSeparator, $output);

    if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
        $output = $pdoFetch->getChunk(
            $tplWrapper,
            array_merge($additionalPlaceholders, ['output' => $output, 'scriptProperties' => $scriptProperties])
        );
    }

    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $output);
    } else {
        return $output;
    }
}
