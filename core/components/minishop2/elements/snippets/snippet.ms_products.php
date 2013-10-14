<?php
/* @var array $scriptProperties */
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);

// You can set modResource instead of msProduct
if (empty($class)) {$scriptProperties['class'] = 'msProduct';}
if (isset($parents) && $parents === '') {
	$scriptProperties['parents'] = $modx->resource->id;
}

/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {return false;}
$pdoFetch = new pdoFetch($modx, $scriptProperties);

// Start building "Where" expression
$where = array('class_key' => 'msProduct');
if (empty($showZeroPrice)) {$where['Data.price:>'] = 0;}

// Joining tables
$leftJoin = array(
	array('class' => 'msProductData', 'alias' => 'Data', 'on' => '`'.$class.'`.`id`=`Data`.`id`'),
	array('class' => 'msVendor', 'alias' => 'Vendor', 'on' => '`Data`.`vendor`=`Vendor`.`id`'),
);
$innerJoin = array();

// Include Thumbnails
$thumbsSelect = array();
if (!empty($includeThumbs)) {
	$thumbs = array_map('trim',explode(',',$includeThumbs));
	if(!empty($thumbs[0])){
		foreach ($thumbs as $thumb) {
			$leftJoin[] = array(
				'class' => 'msProductFile',
				'alias' => $thumb,
				'on' => "`$thumb`.`product_id` = `$class`.`id` AND `$thumb`.`parent` != 0 AND `$thumb`.`path` LIKE '%/$thumb/'"
			);
			$thumbsSelect[$thumb] = "`$thumb`.`url` as `$thumb`";
		}
	}
}

// include Linked products
if (!empty($link) && !empty($master)) {
	$innerJoin[] = array('class' => 'msProductLink', 'alias' => 'Link', 'on' => '`'.$class.'`.`id` = `Link`.`slave` AND `Link`.`link` = '.$link);
	$where['Link.master'] = $master;
}
else if (!empty($link) && !empty($slave)) {
	$innerJoin[] = array('class' => 'msProductLink', 'alias' => 'Link', 'on' => '`'.$class.'`.`id` = `Link`.`master` AND `Link`.`link` = '.$link);
	$where['Link.slave'] = $slave;
}

// Fields to select
$select = array(
	$class => !empty($includeContent) ?  $modx->getSelectColumns($class, $class) : $modx->getSelectColumns($class, $class, '', array('content'), true),
	'Data' => $modx->getSelectColumns('msProductData', 'Data', '', array('id'), true),
	'Vendor' => $modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', array('id'), true),
);
if (!empty($thumbsSelect)) {$select = array_merge($select, $thumbsSelect);}

// Add custom parameters
foreach (array('where','leftJoin','innerJoin','select') as $v) {
	if (!empty($scriptProperties[$v])) {
		$tmp = $modx->fromJSON($scriptProperties[$v]);
		if (is_array($tmp)) {
			$$v = array_merge($$v, $tmp);
		}
	}
	unset($scriptProperties[$v]);
}

// Default parameters
$default = array(
	'class' => $class,
	'where' => $modx->toJSON($where),
	'leftJoin' => $modx->toJSON($leftJoin),
	'innerJoin' => $modx->toJSON($innerJoin),
	'select' => $modx->toJSON($select),
	'sortby' => $class.'id',
	'sortdir' => 'ASC',
	'groupby' => $class.'.id',
	'fastMode' => false,
	'return' => !empty($returnIds) ? 'ids' : 'data',
	'nestedChunkPrefix' => 'minishop2_',
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$rows = $pdoFetch->run();

if (!empty($returnIds)) {return $rows;}

// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
	$q = $modx->newQuery('modPluginEvent', array('event:IN' => array('msOnGetProductPrice','msOnGetProductWeight')));
	$q->innerJoin('modPlugin', 'modPlugin', 'modPlugin.id = modPluginEvent.pluginid');
	$q->where('modPlugin.disabled = 0');

	if ($modificators = $modx->getOption('ms2_price_snippet', null, false, true) || $modx->getOption('ms2_weight_snippet', null, false, true) || $modx->getCount('modPluginEvent', $q)) {
		/* @var msProductData $product */
		$product = $modx->newObject('msProductData');
	}
	$pdoFetch->addTime('Checked the active modifiers');

	foreach ($rows as $k => $row) {
		if ($modificators) {
			$product->fromArray($row, '', true, true);
			$tmp = $row['price'];
			$row['price'] = $product->getPrice($row);
			$row['weight'] = $product->getWeight($row);
			if ($row['price'] != $tmp) {
				$row['old_price'] = $tmp;
			}
		}
		$row['price'] = $miniShop2->formatPrice($row['price']);
		$row['old_price'] = $miniShop2->formatPrice($row['old_price']);
		$row['weight'] = $miniShop2->formatWeight($row['weight']);
		$row['idx'] = $pdoFetch->idx++;

		$tpl = $pdoFetch->defineChunk($row);
		$output[] .= empty($tpl)
			? $pdoFetch->getChunk('', $row)
			: $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
	}
	$pdoFetch->addTime('Returning processed chunks');
}

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$log .= '<pre class="msProductsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toSeparatePlaceholders)) {
	$modx->setPlaceholders($output, $toSeparatePlaceholders);
	$modx->setPlaceholder($log, $toSeparatePlaceholders.'log');
}
else {
	if (empty($outputSeparator)) {$outputSeparator = "\n";}
	$output = is_array($output) ? implode($outputSeparator, $output) : $output;
	$output .= $log;

	if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
		$output = $pdoFetch->getChunk($tplWrapper, array('output' => $output), $pdoFetch->config['fastMode']);
	}

	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}