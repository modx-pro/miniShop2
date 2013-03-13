<?php
/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

foreach ($scriptProperties as $k => $v) {
	if ($v === 'false') {
		$scriptProperties[$k] = false;
	}
	$$k = $scriptProperties[$k];
}

// Start building "Where" expression
	$where = array('class_key' => 'msProduct');
	if (empty($showUnpublished)) {$where['published'] = 1;}
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
		if (!empty($parents)){
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
					$tvsSelect[] = ' "TV'.$tv['name'].'":"IFNULL(TV'.$tv['name'].'.value,\'\') as '.$tv['name'].'" ';
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
	,'nestedChunkPrefix' => 'minishop2_'
);

// Merge all properties and run!
$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Get json fields of msProductData
$meta = $modx->getFieldMeta('msProductData');
$jsonFields = array();
foreach ($meta as $k => $v) {
	if ($v['phptype'] == 'json') {
		$jsonFields[] = $k;
	}
}

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

	// Processing JSON fields
	foreach ($jsonFields as $field) {
		$array = $modx->fromJSON($row[$field]);

		if (!empty($array[0]) && !empty($tpl) && !empty($pdoFetch->elements[$tpl]['placeholders'][$field])) {
			$row[$field] = '';
			
			foreach ($array as $value) {
				$pl = $pdoFetch->makePlaceholders(array_merge($row, array('value' => $value)));
				$row[$field] .= str_replace($pl['pl'], $pl['vl'], $pdoFetch->elements[$tpl]['placeholders'][$field]);
			}
			$row[$field] = substr($row[$field], 1);
		}
		else {
			//$row[$field] = '';
		}
	}

	// Processing product flags
	foreach (array('favorite','new','popular') as $field) {
		if (!empty($row[$field]) && !empty($tpl) && !empty($pdoFetch->elements[$tpl]['placeholders'][$field])) {
			$pl = $pdoFetch->makePlaceholders($row);
			$row[$field] = str_replace($pl['pl'], $pl['vl'], $pdoFetch->elements[$tpl]['placeholders'][$field]);
		}
		else {
			//$row[$field] = '';
		}
	}

	// Processing chunk
	if (empty($tpl)) {
		$output[] = '<pre>'.str_replace(array('[',']','`'), array('&#91;','&#93;','&#96;'), htmlentities(print_r($row, true), ENT_QUOTES, 'UTF-8')).'</pre>';
	}
	else {
		$output[] = $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
	}
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