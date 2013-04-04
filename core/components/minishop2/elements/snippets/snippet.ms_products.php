<?php
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
if (!empty($modx->services['pdofetch'])) {unset($modx->services['pdofetch']);}
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->config['nestedChunkPrefix'] = 'minishop2_';
$pdoFetch->addTime('pdoTools loaded.');

$class = !empty($class) ? trim($class) : 'msProduct';
if (!$tmp = $modx->newObject($class)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[msProducts] Error: could not load class "'.$class.'". Cannot continue.');
	return;
}
else if (!($tmp instanceof modResource)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[msProducts] Error: class "'.$class.'" is not instance of modResource. Cannot continue.');
	return;
}
unset($tmp, $scriptProperties['class']);

// Start building "Where" expression
$where = !empty($class) && $class == 'msProduct' ? array('class_key' => 'msProduct') : array();
if (empty($showUnpublished)) {$where['published'] = 1;}
if (empty($showHidden)) {$where['hidemenu'] = 0;}
if (empty($showDeleted)) {$where['deleted'] = 0;}
if (empty($showZeroPrice)) {$where['Data.price:>'] = 0;}

// Filter by ids
if (!empty($resources)){
	$resources = array_map('trim', explode(',', $resources));
	$in = $out = array();
	foreach ($resources as $v) {
		if (!is_numeric($v)) {continue;}
		if ($v < 0) {$out[] = abs($v);}
		else {$in[] = $v;}
	}
	if (!empty($in)) {$where['id:IN'] = $in;}
	if (!empty($out)) {$where['id:NOT IN'] = $out;}
}
else {
	// Filter by parents
	if (empty($parents) && $parents != '0') {$parents = $modx->resource->id;}
	if (!empty($parents) && $parents > 0){
		if (empty($depth)) {$depth = 1;}
		$pids = array_map('trim', explode(',', $parents));
		$parents = $pids;
		foreach ($pids as $v) {
			if (!is_numeric($v)) {continue;}
			$parents = array_merge($parents, $modx->getChildIds($v, $depth));
		}

		// Add product categories
		$q = $modx->newQuery('msCategoryMember', array('category_id:IN' => $parents));
		$q->select('product_id');
		if ($q->prepare() && $q->stmt->execute()) {
			$members = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
		}

		if (!empty($members)) {
			$where[] = '(`msProduct`.`parent` IN ('.implode(',',$parents).') OR `msProduct`.`id` IN ('.implode(',',$members).'))';
		}
		else {
			$where['parent:IN'] = $parents;
		}
	}
}

// Adding custom where parameters
if (!empty($scriptProperties['where'])) {
	$tmp = $modx->fromJSON($scriptProperties['where']);
	if (is_array($tmp)) {
		$where = array_merge($where, $tmp);
	}
}
unset($scriptProperties['where']);
$pdoFetch->addTime('"Where" expression built.');
// End of building "Where" expression

// Joining tables
$leftJoin = ($class == 'msProduct')
	? array('{"class":"msProductData","alias":"Data","on":"`msProduct`.`id`=`Data`.`id`"}','{"class":"msVendor","alias":"Vendor","on":"`Data`.`vendor`=`Vendor`.`id`"}')
	: array();
$innerJoin = array();

// Include TVs
$tvsLeftJoin = $tvsSelect = array();
if (!empty($includeTVList) && empty($includeTVs)) {$includeTVs = $includeTVList;}
if (!empty($includeTVs)) {
	$tvs = array_map('trim',explode(',',$includeTVs));
	if(!empty($tvs[0])){
		$q = $modx->newQuery('modTemplateVar', array('name:IN' => $tvs));
		$q->select('id,name');
		if ($q->prepare() && $q->stmt->execute()) {
			$tv_ids = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!empty($tv_ids)) {
				foreach ($tv_ids as $tv) {
					$leftJoin[] = '{"class":"modTemplateVarResource","alias":"TV'.$tv['name'].'","on":"TV'.$tv['name'].'.contentid = '.$class.'.id AND TV'.$tv['name'].'.tmplvarid = '.$tv['id'].'"}';
					$tvsSelect[] = ' "TV'.$tv['name'].'":"`TV'.$tv['name'].'`.`value` as `'.$tvPrefix.$tv['name'].'`" ';
				}
			}
		}
		$pdoFetch->addTime('Included list of tvs: <b>'.implode(', ',$tvs).'</b>.');
	}
}
// End of including TVs

if ($class == 'msProduct') {
	// Include Thumbnails
	$thumbsLeftJoin = '';
	$thumbsSelect = array();
	if (!empty($includeThumbs)) {
		$thumbs = array_map('trim',explode(',',$includeThumbs));
		if(!empty($thumbs[0])){
			foreach ($thumbs as $thumb) {
				$leftJoin[] = '{"class":"msProductFile","alias":"'.$thumb.'","on":"`'.$thumb.'`.`product_id` = `msProduct`.`id` AND `'.$thumb.'`.`parent` != 0 AND `'.$thumb.'`.`path` LIKE \'%/'.$thumb.'/\'"}';
				$thumbsSelect[] = ' "'.$thumb.'":"`'.$thumb.'`.`url` as `'.$thumb.'`" ';
			}
			$pdoFetch->addTime('Included list of thumbnails: <b>'.implode(', ',$thumbs).'</b>.');
		}
	}

	// include Linked products
	if (!empty($link) && !empty($master)) {
		$innerJoin[] = '{"class":"msProductLink","alias":"Link","on":"`msProduct`.`id` = `Link`.`slave` AND `Link`.`link` = '.$link.'"}';
		$where['Link.master'] = $master;
	}
	else if (!empty($link) && !empty($slave)) {
		$innerJoin[] = '{"class":"msProductLink","alias":"Link","on":"`msProduct`.`id` = `Link`.`master` AND `Link`.`link` = '.$link.'"}';
		$where['Link.slave'] = $slave;
	}
}

// Fields to select
$resourceColumns = !empty($includeContent) ?  $modx->getSelectColumns($class, $class) : $modx->getSelectColumns($class, $class, '', array('content'), true);
$select = array('"'.$class.'":"'.$resourceColumns.'"');
if ($class == 'msProduct') {
	$select[] = '"Data":"'.$modx->getSelectColumns('msProductData', 'Data', '', array('id'), true).'"';
	$select[] = '"Vendor":"'.$modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', array('id'), true).'"';
	if (!empty($thumbsSelect)) {$select = array_merge($select, $thumbsSelect);}
}
if (!empty($tvsSelect)) {$select = array_merge($select, $tvsSelect);}

// Default parameters
$default = array(
	'class' => $class
	,'where' => $modx->toJSON($where)
	,'leftJoin' => '['.implode(',',$leftJoin).']'
	,'innerJoin' => '['.implode(',',$innerJoin).']'
	,'select' => '{'.implode(',',$select).'}'
	,'sortby' => 'id'
	,'sortdir' => 'ASC'
	,'groupby' => $class.'.id'
	,'fastMode' => false
	,'return' => 'data'
);

// Merge all properties and run!
$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Initializing chunk for template rows
if (!empty($tpl)) {
	$pdoFetch->getChunk($tpl);
}

// Processing rows
$output = null;
if (!empty($rows) && is_array($rows)) {
	foreach ($rows as $k => $row) {
		// Processing main fields
		if ($class == 'msProduct') {
			$row['price'] = $miniShop2->formatPrice($row['price']);
			$row['old_price'] = $miniShop2->formatPrice($row['old_price']);
			$row['weight'] = $miniShop2->formatWeight($row['weight']);
		}

		// Processing quick fields
		if (!empty($tpl)) {
			$pl = $pdoFetch->makePlaceholders($row);
			$qfields = array_keys($pdoFetch->elements[$tpl]['placeholders']);
			foreach ($qfields as $field) {
				if (!empty($row[$field])) {
					$row[$field] = str_replace($pl['pl'], $pl['vl'], $pdoFetch->elements[$tpl]['placeholders'][$field]);
				}
				else {
					$row[$field] = '';
				}
			}
		}

		// Processing chunk
		$output[] = empty($tpl)
			? '<pre>'.str_replace(array('[',']','`'), array('&#91;','&#93;','&#96;'), htmlentities(print_r($row, true), ENT_QUOTES, 'UTF-8')).'</pre>'
			: $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
	}
	$pdoFetch->addTime('Returning processed chunks');
	if (empty($outputSeparator)) {$outputSeparator = "\n";}
	if (!empty($output)) {
		$output = implode($outputSeparator, $output);
	}
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre class="msProductsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}