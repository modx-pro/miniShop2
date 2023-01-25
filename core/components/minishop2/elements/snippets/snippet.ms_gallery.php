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

$extensionsDir = $modx->getOption('extensionsDir', $scriptProperties, 'components/minishop2/img/mgr/extensions/', true);
$limit = $modx->getOption('limit', $scriptProperties, 0);
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msGallery');

/** @var msProduct $product */
$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', ['id' => $product])
    : $modx->resource;
if (!($product instanceof msProduct)) {
    return "[msGallery] The resource with id = {$product->id} is not instance of msProduct.";
}

$where = [
    'product_id' => $product->id,
    'parent' => 0,
];
if (!empty($filetype)) {
    $where['type:IN'] = array_map('trim', explode(',', $filetype));
}
if (empty($showInactive)) {
    $where['active'] = 1;
}
$select = [
    'msProductFile' => '*',
];

// Add user parameters
foreach (['where'] as $v) {
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
$default = [
    'class' => 'msProductFile',
    'where' => $where,
    'select' => $select,
    'limit' => $limit,
    'sortby' => '`rank`',
    'sortdir' => 'ASC',
    'fastMode' => false,
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
];
if ($scriptProperties['return'] === 'tpl') {
    unset($scriptProperties['return']);
}
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$rows = $pdoFetch->run();
if ($scriptProperties['return'] === 'sql' || $scriptProperties['return'] === 'json') {
    return $rows;
}
$pdoFetch->addTime('Fetching thumbnails');

$resolution = [];
/** @var msProductData $data */
if ($data = $product->getOne('Data')) {
    if ($data->initializeMediaSource()) {
        $properties = $data->mediaSource->getProperties();
        if (isset($properties['thumbnails']['value'])) {
            $fileTypes = json_decode($properties['thumbnails']['value'], true);
            foreach ($fileTypes as $k => $v) {
                if (!is_numeric($k)) {
                    $resolution[] = $k;
                } elseif (!empty($v['name'])) {
                    $resolution[] = $v['name'];
                } else {
                    $resolution[] = @$v['w'] . 'x' . @$v['h'];
                }
            }
        }
    }
}

// Processing rows
$files = [];
foreach ($rows as $row) {
    if (isset($row['type']) && $row['type'] == 'image') {
        $c = $modx->newQuery('msProductFile', ['parent' => $row['id']]);
        $c->select('product_id,url');
        $tstart = microtime(true);
        if ($c->prepare() && $c->stmt->execute()) {
            $modx->queryTime += microtime(true) - $tstart;
            $modx->executedQueries++;
            while ($tmp = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (preg_match("#/{$tmp['product_id']}/(.*?)/#", $tmp['url'], $size)) {
                    $row[$size[1]] = $tmp['url'];
                }
            }
        }
    } elseif (isset($row['type'])) {
        $row['thumbnail'] = file_exists(MODX_ASSETS_PATH . $extensionsDir . $row['type'] . '.png')
            ? MODX_ASSETS_URL . $extensionsDir . $row['type'] . '.png'
            : MODX_ASSETS_URL . $extensionsDir . 'other.png';
        foreach ($resolution as $v) {
            $row[$v] = $row['thumbnail'];
        }
    }

    $files[] = $row;
}

if ($scriptProperties['return'] === 'data') {
    return $files;
}

$output = $pdoFetch->getChunk($tpl, [
    'files' => $files,
    'scriptProperties' => $scriptProperties
]);

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="msGalleryLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
