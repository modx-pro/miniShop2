<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);
/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

$extensionsDir = $modx->getOption('extensionsDir', $scriptProperties, 'components/minishop2/img/mgr/extensions/', true);
$limit = $modx->getOption('limit', $scriptProperties, 0);
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msGallery');

/** @var msProduct $product */
$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', $product)
    : $modx->resource;
if (!$product || !($product instanceof msProduct)) {
    return "[msGallery] The resource with id = {$product->id} is not instance of msProduct.";
}

$where = array(
    'product_id' => $product->id,
    'parent' => 0,
);
if (!empty($filetype)) {
    $where['type:IN'] = array_map('trim', explode(',', $filetype));
}
$select = array(
    'msProductFile' => '*',
);

// Add user parameters
foreach (array('where') as $v) {
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

$default = array(
    'class' => 'msProductFile',
    'where' => $where,
    'select' => $select,
    'limit' => $limit,
    'sortby' => 'rank',
    'sortdir' => 'ASC',
    'fastMode' => false,
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
);
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$rows = $pdoFetch->run();

$pdoFetch->addTime('Fetching thumbnails');

$resolution = array();
/** @var msProductData $data */
if ($data = $product->getOne('Data')) {
    if ($data->initializeMediaSource()) {
        $properties = $data->mediaSource->getProperties();
        if (isset($properties['thumbnails']['value'])) {
            $fileTypes = json_decode($properties['thumbnails']['value'], true);
            foreach ($fileTypes as $v) {
                $resolution[] = $v['w'] . 'x' . $v['h'];
            }
        }
    }
}

// Processing rows
$files = array();
foreach ($rows as $row) {
    if (isset($row['type']) && $row['type'] == 'image') {
        $c = $modx->newQuery('msProductFile', array('parent' => $row['id']));
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
        $row['thumbnail'] =
        $row['url'] = file_exists(MODX_ASSETS_PATH . $extensionsDir . $row['type'] . '.png')
            ? MODX_ASSETS_URL . $extensionsDir . $row['type'] . '.png'
            : MODX_ASSETS_URL . $extensionsDir . 'other.png';
        foreach ($resolution as $v) {
            $row[$v] = $row['thumbnail'];
        }
    }

    $files[] = $row;
}

$output = $pdoFetch->getChunk($tpl, array(
    'files' => $files,
));

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="msGalleryLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}