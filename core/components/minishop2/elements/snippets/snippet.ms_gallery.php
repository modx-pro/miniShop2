<?php
/* @var array $scriptProperties */
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);
/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {return false;}
$pdoFetch = new pdoFetch($modx, $scriptProperties);

$product = (!empty($product) && $product != $modx->resource->id)
	? $modx->getObject('msProduct', $product)
	: $modx->resource;
if (!$product || !($product instanceof msProduct)) {return 'This resource is not instance of msProduct class.';}

if (empty($limit)) {$limit = 100;}
$where = array(
	'product_id' => $product->get('id')
	,'parent' => 0
	,'type' => 'image'
);
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
$scriptProperties['tpl'] = $tplRow;
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$rows = $pdoFetch->run();

// Processing rows
$output = null; $images = array();

$pdoFetch->addTime('Fetching thumbnails');
foreach ($rows as $k => $row) {
	$row['idx'] = $pdoFetch->idx++;
	$images[$row['id']] = $row;
	$q = $modx->newQuery('msProductFile', array('parent' => $row['id']));
	$q->select('url');
	if ($q->prepare() && $q->stmt->execute()) {
		while ($tmp = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
			if (preg_match('/((?:\d{1,4}|)x(?:\d{1,4}|))/', $tmp, $size)) {
				$images[$row['id']][$size[0]] = $tmp;
			}
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
		$output = $pdoFetch->getChunk($tplSingle, array_pop($images));
	}
	else {
		if (empty($outputSeparator)) {$outputSeparator = "\n";}
		$output = implode($outputSeparator, $output);

		if (!empty($tplOuter) && !empty($output)) {
			$output = $pdoFetch->getChunk($tplOuter, array('rows' => $output));
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