<?php
/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->config = array_merge($pdoFetch->config, array('nestedChunkPrefix' => 'minishop2_'));
$pdoFetch->addTime('pdoTools loaded.');

$cart = $miniShop2->cart->get();
$order = $miniShop2->order->get();
if (!empty($_GET['msorder'])) {
	if ($order = $modx->getObject('msOrder', $_GET['msorder'])) {
		if (in_array($_GET['msorder'], $_SESSION['minishop2']['orders']) || $order->get('user_id') == $modx->user->id) {
			return $pdoFetch->getChunk($tplSuccess, $order->toArray());
		}
	}
}
else if (empty($cart)) {
	return !empty($tplEmpty) ? $pdoFetch->getChunk($tplEmpty) : '';
}


$deliveryColumns = $modx->getSelectColumns('msDelivery', 'msDelivery', 'delivery_');
$paymentColumns = $modx->getSelectColumns('msPayment', 'msPayment', 'payment_');

// Default parameters
$default = array(
	'class' => 'msDelivery'
	,'where' => '{"active":1}'
	,'select' => '{"msDelivery":"all"}'
	,'sortby' => 'rank'
	,'sortdir' => 'ASC'
	,'return' => 'data'
	,'fastMode' => false
	,'nestedChunkPrefix' => 'minishop2_'
);

// Merge all properties and run!
$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$pdoFetch->addTime('Query parameters are prepared.');
$deliveries = $pdoFetch->run();
$pdoFetch->addTime('Fetched deliveries.');

$arrays = array('deliveries' => array(),'payments' => array());
if (!empty($deliveries)) {
	// Initializing chunk for template rows
	if (!empty($tplDelivery)) {$pdoFetch->getChunk($tplDelivery);}
	if (!empty($tplPayment)) {$pdoFetch->getChunk($tplPayment);}

	foreach ($deliveries as $di => $delivery) {
		$did = $delivery['id'];
		if (empty($order['delivery']) && $di == 0) {
			$miniShop2->order->add('delivery', $did);
			$order = $miniShop2->order->get();
		}

		$delivery['payments'] = array();
		$pdoFetch->config = array_merge($pdoFetch->config, array(
			'class' => 'msPayment'
			,'innerJoin' => '[{"class":"msDeliveryMember","alias":"Member","on":"Member.delivery_id='.$delivery['id'].' AND Member.payment_id=msPayment.id"}]'
			,'select' => '{"msPayment":"all"}'
		));
		$pdoFetch->addTime('Fetched payments for delivery '.$delivery['name'].'.');
		$payments = $pdoFetch->run();
		if (!empty($payments)) {
			foreach ($payments as $pi => $payment) {
				$pdoFetch->addTime('Processing payment '.$payment['name'].'.');
				$pid = $payment['id'];
				if (empty($order['payment']) && $pi == 0) {
					$miniShop2->order->add('payment', $pid);
					$order = $miniShop2->order->get();
				}
				if (!array_key_exists($pid, $arrays['payments'])) {
					if (!empty($pdoFetch->elements[$tplPayment]['placeholders']['logo']) && !empty($payment['logo'])) {
						$payment['logo'] = str_replace('[[+value]]', $payment['logo'], $pdoFetch->elements[$tplPayment]['placeholders']['logo']);
					}
					if (!empty($pdoFetch->elements[$tplPayment]['placeholders']['description']) && !empty($payment['description'])) {
						$payment['description'] = str_replace('[[+value]]', $payment['description'], $pdoFetch->elements[$tplPayment]['placeholders']['description']);
					}
					$payment['checked'] = !empty($order['payment']) && $order['payment'] == $pid ? 'checked' : '';
					$arrays['payments'][$pid] = $pdoFetch->getChunk($tplPayment, $payment);
				}
				$delivery['payments'][] = $pid;
			}
		}

		$pdoFetch->addTime('Processing delivery '.$delivery['name'].'.');

		if (!empty($pdoFetch->elements[$tplDelivery]['placeholders']['logo']) && !empty($delivery['logo'])) {
			$delivery['logo'] = str_replace('[[+value]]', $delivery['logo'], $pdoFetch->elements[$tplDelivery]['placeholders']['logo']);
		}
		if (!empty($pdoFetch->elements[$tplDelivery]['placeholders']['description']) && !empty($payment['description'])) {
			$delivery['description'] = str_replace('[[+value]]', $delivery['description'], $pdoFetch->elements[$tplDelivery]['placeholders']['description']);
		}
		$delivery['checked'] = !empty($order['delivery']) && $order['delivery'] == $did ? 'checked' : '';
		$delivery['payments'] = json_encode($delivery['payments']);
		$arrays['deliveries'][$did] = $pdoFetch->getChunk($tplDelivery, $delivery);
	}
}

if (!empty($tplOuter)) {$pdoFetch->getChunk($tplOuter);}
$cart_status = $miniShop2->cart->status();
$order_cost = $miniShop2->order->getcost();
$form = array(
	'deliveries' => !empty($arrays['deliveries']) ? str_replace('[[+value]]', implode('',$arrays['deliveries']), @$pdoFetch->elements[$tplOuter]['placeholders']['deliveries']) : ''
	,'payments' => !empty($arrays['payments']) ? str_replace('[[+value]]', implode('',$arrays['payments']), @$pdoFetch->elements[$tplOuter]['placeholders']['payments']) : ''
	,'order_cost' => @$order_cost['data']['cost']
);

// Setting user fields
if ($isAuthenticated = $modx->user->isAuthenticated()) {
	$profile = $modx->user->Profile->toArray();
}
$user_fields = array(
	'receiver' => 'fullname'
	,'phone' => 'phone'
	,'email' => 'email'
	,'comment' => ''
	,'index' => 'zip'
	,'country' => 'country'
	,'region' => 'state'
	,'city' => 'city'
	,'street' => 'address'
	,'building' => ''
	,'room' => ''
);
foreach ($user_fields as $key => $value) {
	if (!empty($order[$key])) {
		$form[$key] = $order[$key];
	}
	else if (!empty($profile) && !empty($value)) {
		//$form[$key] = $profile[$value];
		$tmp = $miniShop2->order->add($key, $profile[$value]);
		if ($tmp['success'] && !empty($tmp['data'][$key])) {
			$form[$key] = $tmp['data'][$key];
		}
	}

}

if (!empty($tplOuter)) {
	$pdoFetch->getChunk($tplOuter);
	$output = $pdoFetch->getChunk($tplOuter, $form);
}
else {return 'no outer chunk';}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre class="msOrderLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

return $output;