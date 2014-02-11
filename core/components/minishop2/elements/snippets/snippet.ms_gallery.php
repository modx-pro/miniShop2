<?php
/* @var array $scriptProperties */
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);
/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {return false;}
$pdoFetch = new pdoFetch($modx, $scriptProperties);

$extensionsDir = $modx->getOption('extensionsDir', $scriptProperties, 'components/minishop2/img/mgr/extensions/', true);

/** @var msProduct $product */
$product = (!empty($product) && $product != $modx->resource->id)
	? $modx->getObject('msProduct', $product)
	: $modx->resource;
if (!$product || !($product instanceof msProduct)) {return 'This resource is not instance of msProduct class.';}

/** @var msProductData $data */
$resolution = array();
if ($data = $product->getOne('Data')) {
	$data->initializeMediaSource();
	$properties = $data->mediaSource->getProperties();
	if (isset($properties['thumbnails']['value'])) {
		$fileTypes = $modx->fromJSON($properties['thumbnails']['value']);
		foreach ($fileTypes as $v) {
			$resolution[] = $v['w'].'x'.$v['h'];
		}
	}
}

if (empty($limit)) {$limit = 100;}
$where = array(
	'product_id' => $product->get('id'),
	'parent' => 0,
);
if (!empty($filetype)) {
	$where['type:IN'] = array_map('trim', explode(',', $filetype));
}
// processing additional query params
if (!empty($scriptProperties['where'])) {
	$tmp = $modx->fromJSON($scriptProperties['where']);
	if (is_array($tmp) && !empty($tmp)) {
		$where = array_merge($where, $tmp);
	}
}
unset($scriptProperties['where']);

// Default parameters
$default = array(
	'class' => 'msProductFile'
	,'where' => $modx->toJSON($where)
	,'select' => '{"msProductFile":"all"}'
	,'limit' => $limit
	,'sortby' => 'rank'
	,'sortdir' => 'ASC'
	,'fastMode' => false
	,'return' => 'data'
	,'nestedChunkPrefix' => 'minishop2_'
);

// Merge all properties and run!
$scriptProperties['tpl'] = !empty($tplRow) ? $tplRow : '';
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$rows = $pdoFetch->run();

// Processing rows
$output = null; $images = array();

$pdoFetch->addTime('Fetching thumbnails');
foreach ($rows as $k => $row) {
	$row['idx'] = $pdoFetch->idx++;
	$images[$row['id']] = $row;

	if (isset($row['type']) && $row['type'] == 'image') {
		$q = $modx->newQuery('msProductFile', array('parent' => $row['id']));
		$q->select('url');
		if ($q->prepare() && $q->stmt->execute()) {
			while ($url = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
				$tmp = parse_url($url);
				if (preg_match('/((?:\d{1,4}|)x(?:\d{1,4}|))/', $tmp['path'], $size)) {
					$images[$row['id']][$size[0]] = $url;
				}
			}
		}
	}
	elseif (isset($row['type'])) {
		$row['thumbnail'] = $row['url'] =  (file_exists(MODX_ASSETS_PATH . $extensionsDir . $row['type'] . '.png'))
			? MODX_ASSETS_URL . $extensionsDir . $row['type'].'.png'
			: MODX_ASSETS_URL . $extensionsDir . 'other.png';
		foreach ($resolution as $v) {
			$images[$row['id']][$v] = $row['thumbnail'];
		}
	}
}

// Processing chunks
$pdoFetch->addTime('Processing chunks');
$output = array();
foreach ($images as $row) {
	$tpl = $pdoFetch->defineChunk($row);
	$output[] = empty($tpl)
		? $pdoFetch->getChunk('', $row)
		: $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
}
$pdoFetch->addTime('Returning processed chunks');

// Return output
$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$log .= '<pre class="msGalleryLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if (!empty($toSeparatePlaceholders)) {
	$output['log'] = $log;
	$modx->setPlaceholders($output, $toSeparatePlaceholders);
}
else {
	if (count($output) === 1 && !empty($tplSingle)) {
		$output = $pdoFetch->getChunk($tplSingle, array_shift($images));
	}
	else {
		if (empty($outputSeparator)) {$outputSeparator = "\n";}
		$output = implode($outputSeparator, $output);

		if (!empty($tplOuter) && !empty($output)) {
			$arr = array_shift($images);
			$arr['rows'] = $output;
			$output = $pdoFetch->getChunk($tplOuter, $arr);
		}
		elseif (empty($output)) {
			$output = !empty($tplEmpty)
				? $pdoFetch->getChunk($tplEmpty)
				: '';
		}
	}

	$output .= $log;
	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}