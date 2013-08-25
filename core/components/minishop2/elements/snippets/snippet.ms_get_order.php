<?php
if (empty($id)) {return $modx->lexicon('ms2_err_order_nf');}
/* @var array $scriptProperties */
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);
/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {return false;}
$pdoFetch = new pdoFetch($modx, $scriptProperties);

/* @var msOrder $order */
if (!$order = $modx->getObject('msOrder', $id)) {return $modx->lexicon('ms2_err_order_nf');}
if ((empty($_SESSION['minishop2']['orders']) || !in_array($id, $_SESSION['minishop2']['orders'])) && $order->get('user_id') != $modx->user->id && $modx->context->key != 'mgr') {
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
$outer['cost'] = $miniShop2->formatPrice($outer['cost']);
$outer['cart_cost'] = $miniShop2->formatPrice($outer['cart_cost']);
$outer['delivery_cost'] = $miniShop2->formatPrice($outer['delivery_cost']);
$outer['weight'] = $miniShop2->formatWeight($outer['weight']);

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
if (!empty($thumbsLeftJoin)) {$leftJoin .= $thumbsLeftJoin;}
$select = '"msProduct":"'.$resourceColumns.'","Data":"'.$dataColumns.'","OrderProduct":"'.$orderProductColumns.'","Vendor":"'.$vendorColumns.'"';
if (!empty($thumbsSelect)) {$select .= ','.implode(',', $thumbsSelect);}

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
$scriptProperties['tpl'] = $scriptProperties['tplRow'];
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$rows = $pdoFetch->run();

/* @var msOrderProduct $row */
foreach ($rows as $row) {
	$outer['cart_count'] += $row['count'];
	$row['price'] = $miniShop2->formatPrice($row['price']);
	$row['old_price'] = $miniShop2->formatPrice($row['old_price']);
	$row['cost'] = $miniShop2->formatPrice($row['cost']);
	$row['weight'] = $miniShop2->formatWeight($row['weight']);

	// Additional properties of product
	$options = json_decode($row['options'],1);
	if (!empty($options) && is_array($options)) {
		foreach ($options as $key => $value) {
			$row['option.'.$key] = $value;
		}
	}

	$row['idx'] = $pdoFetch->idx++;
	$tplRow = $pdoFetch->defineChunk($row);
	$outer['goods'] .= empty($tplRow)
		? $pdoFetch->getChunk('', $row)
		: $pdoFetch->getChunk($tplRow, $row, $pdoFetch->config['fastMode']);
}

if (empty($tplOuter)) {
	$modx->setPlaceholders($outer);
}
else {
	return $pdoFetch->getChunk($tplOuter, $outer);
}