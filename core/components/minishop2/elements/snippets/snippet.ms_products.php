<?php
/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
if (!empty($modx->services['pdofetch'])) {unset($modx->services['pdofetch']);}
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->config['nestedChunkPrefix'] = 'minishop2_';
$pdoFetch->addTime('pdoTools loaded.');

// Start building "Where" expression
$where = array('class_key' => 'msProduct');
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
		$scriptProperties['where'] = $modx->toJSON(array_merge($where, $tmp));
	}
}
unset($scriptProperties['where']);
$pdoFetch->addTime('"Where" expression built.');
// End of building "Where" expression

// Include TVs
$tvsLeftJoin = '';
$tvsSelect = array();
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
					$tvsLeftJoin .= ',{"class":"modTemplateVarResource","alias":"TV'.$tv['name'].'","on":"TV'.$tv['name'].'.contentid = msProduct.id AND TV'.$tv['name'].'.tmplvarid = '.$tv['id'].'"}';
					$tvsSelect[] = ' "TV'.$tv['name'].'":"TV'.$tv['name'].'.value as '.$tv['name'].'" ';
				}
			}
		}
		$pdoFetch->addTime('Included list of tvs: <b>'.implode(', ',$tvs).'</b>.');
	}
}
// End of including TVs

// Include Thumbnails
$thumbsLeftJoin = '';
$thumbsSelect = array();
if (!empty($includeThumbs)) {
	$thumbs = array_map('trim',explode(',',$includeThumbs));
	if(!empty($thumbs[0])){
		foreach ($thumbs as $thumb) {
			$thumbsLeftJoin .= ',{"class":"msProductFile","alias":"'.$thumb.'","on":"'.$thumb.'.product_id = msProduct.id AND '.$thumb.'.parent != 0 AND '.$thumb.'.path LIKE \'%/'.$thumb.'/\'"}';
			$thumbsSelect[] = ' "'.$thumb.'":"'.$thumb.'.url as '.$thumb.'" ';
		}
		$pdoFetch->addTime('Included list of thumbnails: <b>'.implode(', ',$thumbs).'</b>.');
	}
}
// End of including Thumbnails

// Fields to select
$resourceColumns = !empty($includeContent) ?  $modx->getSelectColumns('msProduct', 'msProduct') : $modx->getSelectColumns('msProduct', 'msProduct', '', array('content'), true);
$dataColumns = $modx->getSelectColumns('msProductData', 'Data', '', array('id'), true);
$vendorColumns = $modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', array('id'), true);

// Tables for joining
$leftJoin = '{"class":"msProductData","alias":"Data","on":"msProduct.id=Data.id"},{"class":"msVendor","alias":"Vendor","on":"Data.vendor=Vendor.id"}';
if (!empty($tvsLeftJoin)) {$leftJoin .= $tvsLeftJoin;}
if (!empty($thumbsLeftJoin)) {$leftJoin .= $thumbsLeftJoin;}
$select = '"msProduct":"'.$resourceColumns.'","Data":"'.$dataColumns.'","Vendor":"'.$vendorColumns.'"';
if (!empty($tvsSelect)) {$select .= ','.implode(',', $tvsSelect);}
if (!empty($thumbsSelect)) {$select .= ','.implode(',', $thumbsSelect);}

// Default parameters
$default = array(
	'class' => 'msProduct'
	,'where' => $modx->toJSON($where)
	,'leftJoin' => '['.$leftJoin.']'
	,'select' => '{'.$select.'}'
	,'sortby' => 'id'
	,'sortdir' => 'ASC'
	,'groupby' => 'msProduct.id'
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
foreach ($rows as $k => $row) {
	// Processing main fields
	$row['price'] = round($row['price'], 2);
	$row['old_price'] = round($row['old_price'], 2);
	$row['weight'] = round($row['weight'], 3);


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