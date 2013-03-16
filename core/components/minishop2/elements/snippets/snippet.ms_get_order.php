<?php
if (empty($id)) {return $modx->lexicon('ms2_err_order_nf');}

/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->config = array_merge($pdoFetch->config, array('nestedChunkPrefix' => 'minishop2_'));
$pdoFetch->addTime('pdoTools loaded.');

// Initializing chunk for template rows
if (!empty($tplRow)) {$pdoFetch->getChunk($tplRow);}

/* @var msOrder $order */
if (!$order = $modx->getObject('msOrder', $id)) {return $modx->lexicon('ms2_err_order_nf');}
if (!in_array($id, $_SESSION['minishop2']['orders']) && $order->get('user_id') != $modx->user->id && $modx->context->key != 'mgr') {
	return !empty($tplEmpty) ? $pdoFetch->getChunk($tplEmpty) : '';
}

$pls_order = $order->toArray();
$pls_user = $order->getOne('User')->getOne('Profile')->toArray('user.');
$pls_address = $order->getOne('Address')->toArray('address.');
$pls_delivery = $order->getOne('Delivery')->toArray('delivery.');
$pls_payment = $order->getOne('Payment')->toArray('payment.');
$outer = array_merge($pls_order, $pls_user, $pls_address, $pls_delivery, $pls_payment);

$outer['goods'] = '';
$outer['cart_count'] = 0;

// Include TVs
$tvsLeftJoin = '';
$tvsSelect = array();
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
$orderProductColumns = $modx->getSelectColumns('msOrderProduct', 'msOrderProduct', '', array('id'), true);

// Tables for joining
$leftJoin = '{"class":"msProduct","alias":"msProduct","on":"msProduct.id=msOrderProduct.product_id"},{"class":"msProductData","alias":"Data","on":"msProduct.id=Data.id"},{"class":"msVendor","alias":"Vendor","on":"Data.vendor=Vendor.id"}';
if (!empty($tvsLeftJoin)) {$leftJoin .= $tvsLeftJoin;}
if (!empty($thumbsLeftJoin)) {$leftJoin .= $thumbsLeftJoin;}
$select = '"msProduct":"'.$resourceColumns.'","OrderProduct":"'.$orderProductColumns.'","Vendor":"'.$vendorColumns.'"';
if (!empty($tvsSelect)) {$select .= ','.implode(',', $tvsSelect);}
if (!empty($thumbsSelect)) {$select .= ','.implode(',', $thumbsSelect);}
$pdoFetch->addTime('Query parameters are prepared.');

$default = array(
	'class' => 'msOrderProduct'
	,'where' => '{"msOrderProduct.order_id":"'.$id.'"}'
	,'leftJoin' => '['.$leftJoin.']'
	,'select' => '{'.$select.'}'
	,'sortby' => 'id'
	,'sortdir' => 'ASC'
	,'groupby' => 'msOrderProduct.id'
	,'fastMode' => false
	,'limit' => 0
	,'return' => 'data'
	,'nestedChunkPrefix' => 'minishop2_'
);
// Merge all properties and run!
$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$rows = $pdoFetch->run();

/* @var msOrderProduct $row */
foreach ($rows as $row) {
	$outer['cart_count'] += $row['count'];

	// Additional properties of product
	$options = json_decode($row['options'],1);
	if (!empty($options) && is_array($options)) {
		foreach ($options as $key => $value) {
			$row['option.'.$key] = $value;
		}
	}

	// Processing quick fields
	if (!empty($tplRow)) {
		$pl = $pdoFetch->makePlaceholders($row);
		$qfields = array_keys($pdoFetch->elements[$tplRow]['placeholders']);
		foreach ($qfields as $field) {
			if (!empty($row[$field])) {
				$row[$field] = str_replace($pl['pl'], $pl['vl'], $pdoFetch->elements[$tplRow]['placeholders'][$field]);
			}
		}
	}

	$outer['goods'] .= !empty($tplRow) ? $pdoFetch->getChunk($tplRow, $row) : str_replace(array('[[',']]'),array('&091;&091;','&093;&093;'), print_r($row,1));
}

unset($modx->services['pdofetch']);
if (empty($tplOuter)) {
	$modx->setPlaceholders($outer);
}
else {
	return !empty($tplOuter) ? $pdoFetch->getChunk($tplOuter, $outer) : str_replace(array('[[',']]'),array('&091;&091;','&093;&093;'), print_r($outer,1));
}