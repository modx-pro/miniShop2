<?php
// Подключаем
define('MODX_API_MODE', true);
require dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

// Включаем обработку ошибок
$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('FILE');

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', array());
$miniShop2->loadCustomClasses('payment');

if (!class_exists('PayPal')) {exit( 'Error: could not load payment class "PayPal".');}
if (empty($_GET['token'])) {return 'Access denied';}

/* @var msPaymentInterface|PayPal $handler */
$handler = new PayPal($modx->newObject('msOrder'));
$response = $handler->request(array(
	'METHOD' => 'GetExpressCheckoutDetails'
	,'TOKEN' => $_GET['token']
));

$context = '';
$params = array();
if (!is_array($response)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2] Error on receive details of PayPal operation: ' . $response .'; '.print_r($_GET,1));
}
else if (!empty($response['PAYMENTREQUEST_0_INVNUM'])) {
	if ($order = $modx->getObject('msOrder', $response['PAYMENTREQUEST_0_INVNUM'])) {
		$handler->receive($order, $response);
		$context = $order->get('context');
		$params['msorder'] = $order->get('id');
	}
	else {
		$modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2] Could not retrieve order with id '.$response['PAYMENTREQUEST_0_INVNUM']);
	}
}
else {
	$modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2] Error on receive details of PayPal operation: ' . print_r($response,1) .'; '.print_r($_GET,1));
}



$success = $cancel = MODX_SITE_URL;
if ($id = $modx->getOption('ms2_payment_paypal_success_id', null, 0)) {
	$success = $modx->makeUrl($id, $context, $params, 'full');
}
if ($id = $modx->getOption('ms2_payment_paypal_cancel_id', null, 0)) {
	$cancel = $modx->makeUrl($id, $context, $params, 'full');
}

$redirect = !empty($_GET['action']) && $_GET['action'] == 'success' ? $success : $cancel;
header('Location: ' . $redirect);
