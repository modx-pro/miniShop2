<?php
/* @var array $scriptProperties */
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);
/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {return false;}
$pdoFetch = new pdoFetch($modx, $scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

if (!empty($_GET['msorder'])) {
	if ($order = $modx->getObject('msOrder', $_GET['msorder'])) {
		if ((!empty($_SESSION['minishop2']['orders']) && in_array($_GET['msorder'], $_SESSION['minishop2']['orders'])) || $order->get('user_id') == $modx->user->id || $modx->context->key == 'mgr') {
			if (empty($tplSuccess)) {$tplSuccess = 'tpl.msOrder.success';}
			return $pdoFetch->getChunk($tplSuccess, array('id' => $_GET['msorder']));
		}
	}
}

$cart = $miniShop2->cart->get();
$order = $miniShop2->order->get();
if (empty($cart)) {
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
$pdoFetch->addTime('Query parameters are prepared.');
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$deliveries = $pdoFetch->run();
$pdoFetch->addTime('Fetched deliveries.');

$arrays = array('deliveries' => array(),'payments' => array());
if (!empty($deliveries)) {
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
					$payment['checked'] = !empty($order['payment']) && $order['payment'] == $pid ? 'checked' : '';
					$arrays['payments'][$pid] = empty($tplPayment)
						? $pdoFetch->getChunk('', $payment)
						: $pdoFetch->getChunk($tplPayment, $payment);
				}
				$delivery['payments'][] = $pid;
			}
		}

		$pdoFetch->addTime('Processing delivery '.$delivery['name'].'.');
		$delivery['checked'] = !empty($order['delivery']) && $order['delivery'] == $did ? 'checked' : '';
		$delivery['payments'] = json_encode($delivery['payments']);
		$arrays['deliveries'][$did] = empty($tplDelivery)
			? $pdoFetch->getChunk('', $delivery)
			: $pdoFetch->getChunk($tplDelivery, $delivery);
	}
}

if (!empty($tplOuter)) {$pdoFetch->getChunk($tplOuter);}
//$cart_status = $miniShop2->cart->status();
$order_cost = $miniShop2->order->getcost();
$deliveries = implode('', $arrays['deliveries']);
$payments = implode('', $arrays['payments']);
$form = array(
	'deliveries' => $deliveries
	,'payments' => $payments
	,'order_cost' => $miniShop2->formatPrice($order_cost['data']['cost'])
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
		unset($order[$key]);
	}
	else if (!empty($profile) && !empty($value)) {
		$tmp = $miniShop2->order->add($key, $profile[$value]);
		if ($tmp['success'] && !empty($tmp['data'][$key])) {
			$form[$key] = $tmp['data'][$key];
		}
	}
}
$form = array_merge($order, $form);
$form['errors'] = array();

if (!empty($_POST)) {
	$response = $miniShop2->order->getDeliveryRequiresFields();
	$requires = $response['data']['requires'];

	foreach ($_POST as $field => $val) {
		$validated = $miniShop2->order->validate($field, $val);
		if ((in_array($field, $requires) && empty($validated))) {
			$form['errors'][$field] = 'error';
		}
	}
}

$output = empty($tplOuter)
	? $pdoFetch->getChunk('', $form)
	: $pdoFetch->getChunk($tplOuter, $form);

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre class="msOrderLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

return $output;