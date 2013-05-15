<?php
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

if (!empty($product)) {
	$product = $modx->getObject('msProduct', $product);
}
else {
	$product = $modx->resource;
}

if ($product->get('class_key') != 'msProduct') {return false;}
$where = array(
	'product_id' => $product->get('id')
	,'type' => 'image'
);

// Default parameters
$default = array(
	'class' => 'msProductFile'
	,'where' => $modx->toJSON($where)
	,'select' => '{
		"msProductFile":"all"
	}'
	,'sortby' => 'rank'
	,'sortdir' => 'ASC'
	,'fastMode' => false
	,'return' => 'data'
	,'nestedChunkPrefix' => 'minishop2_'
);

// Merge all properties and run!
$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Processing rows
$output = null; $images = array(); $total = 0;
foreach ($rows as $k => $row) {
	if ($row['parent'] == 0) {
		if (isset($images[$row['id']])) {
			$images[$row['id']] = array_merge($images[$row['id']], $row);
		}
		else {
			$images[$row['id']] = $row;
		}
		$total++;
	}
	else if (preg_match('/(\d{1,4}x\d{1,4})/', $row['url'], $size)) {
		$images[$row['parent']][$size[0]] = $row['url'];
	}
}

// Processing chunk
$rows = array();
foreach ($images as $row) {
	if (empty($tplRow)) {
		$rows[$row['rank']] = '<pre>'.str_replace(array('[',']','`'), array('&#91;','&#93;','&#96;'), htmlentities(print_r($row, true), ENT_QUOTES, 'UTF-8')).'</pre>';
	}
	else {
		$rows[$row['rank']] = $pdoFetch->getChunk($tplRow, $row, $pdoFetch->config['fastMode']);
	}
}
ksort($rows);
$pdoFetch->addTime('Returning processed chunks');

if (!empty($rows)) {
	$output = implode($pdoFetch->config['outputSeparator'], $rows);
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre class="msGalleryLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

unset($modx->services['pdofetch']);
// Return output
if (!empty($output)) {
	if (!empty($tplOuter)) {
		$output = $pdoFetch->getChunk($tplOuter, array('rows' => $output));
	}
}
else {
	$output = !empty($tplEmpty) ? $pdoFetch->getChunk($tplEmpty) : '';
}
$modx->setPlaceholder($pdoFetch->config['totalVar'], $total);

if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}