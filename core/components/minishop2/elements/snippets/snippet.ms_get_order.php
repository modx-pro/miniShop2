<?php
if (empty($id) || empty($tplRow)) {return false;}

/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->config = array_merge($pdoFetch->config, array('nestedChunkPrefix' => 'minishop2_'));
$pdoFetch->addTime('pdoTools loaded.');

// Initializing chunk for template rows
$pdoFetch->getChunk($tplRow);

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
$pls = array_merge($pls_order, $pls_user, $pls_address, $pls_delivery, $pls_payment);

$pls['products'] = '';
$pls['cart_count'] = 0;
$ordered = $modx->getCollection('msOrderProduct', array('order_id' => $id));
/* @var msOrderProduct $row */
foreach ($ordered as $row) {
	if ($product = $row->getOne('Product')) {

		$item = array_merge($row->toArray(), $product->toArray());
		$pls['cart_count'] += $item['count'];
		/* @var modTemplateVar $tv */
		/*
		$tvs = $product->getMany('TemplateVars');
		foreach ($tvs as $tv) {
			$item[$tv->get('name')] = $tv->get('value');
		}
		*/

		// Additional properties of product
		$options = $row->get('options');
		if (!empty($options) && is_array($options)) {
			foreach ($options as $key => $value) {
				if (is_array($value)) {
					$item[$key] = '';
					foreach ($values as $value2) {
						$item[$key] .= str_replace('[[+value]]', $value2, @$pdoFetch->elements[$tplRow]['placeholders'][$key]);
					}
				}
				else {
					$item[$key] = str_replace('[[+value]]', $value, @$pdoFetch->elements[$tplRow]['placeholders'][$key]);
				}
			}
		}
		foreach ($item as $k => $v) {
			if (is_array($v)) {
				unset($item[$k]);
			}
		}

		$pls['products'] .= $pdoFetch->getChunk($tplRow, $item);
	}
}

//echo '<pre>';print_r($pls);die;

if (empty($tplOuter)) {
	$modx->setPlaceholders($pls);
}
else {
	return $pdoFetch->getChunk($tplOuter, $pls);
}