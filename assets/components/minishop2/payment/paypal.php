<?php
define('MODX_API_MODE', true);
/** @noinspection PhpIncludeInspection */
require dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

/** @var modX $modx */
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->loadCustomClasses('payment');

if (!class_exists('PayPal')) {
    exit('Error: could not load payment class "PayPal".');
}
/** @var msOrder $order */
$order = $modx->newObject('msOrder');
/** @var msPaymentInterface|PayPal $handler */
$handler = new PayPal($order);

if (isset($_GET['action']) && $_GET['action'] == 'continue' && !empty($_GET['msorder']) && !empty($_GET['mscode'])) {
    if ($order = $modx->getObject('msOrder', $_GET['msorder'])) {
        if ($_GET['mscode'] == $handler->getOrderHash($order)) {
            $response = $handler->send($order);
            if ($response['success'] && !empty($response['data']['redirect'])) {
                $modx->sendRedirect($response['data']['redirect']);
            } else {
                exit($response['message']);
            }
        }
    }
    exit('Error when continuing order');
} elseif (empty($_GET['token'])) {
    exit('Access denied');
}

$response = $handler->request(array(
    'METHOD' => 'GetExpressCheckoutDetails',
    'TOKEN' => $_GET['token'],
));

$context = '';
$params = array();
if (!is_array($response)) {
    $modx->log(modX::LOG_LEVEL_ERROR,
        '[miniShop2] Error on receive details of PayPal operation: ' . $response . '; ' . print_r($_GET, 1)
    );
} elseif (!empty($response['PAYMENTREQUEST_0_INVNUM'])) {
    if ($order = $modx->getObject('msOrder', $response['PAYMENTREQUEST_0_INVNUM'])) {
        $handler->receive($order, $response);
        $context = $order->get('context');
        $params['msorder'] = $order->get('id');
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR,
            '[miniShop2] Could not retrieve order with id ' . $response['PAYMENTREQUEST_0_INVNUM']);
    }
} else {
    $modx->log(modX::LOG_LEVEL_ERROR,
        '[miniShop2] Error on receive details of PayPal operation: ' . print_r($response, 1) . '; ' . print_r($_GET, 1)
    );
}

$success = $cancel = $modx->getOption('site_url');
if ($id = $modx->getOption('ms2_payment_paypal_success_id', null, 0)) {
    $success = $modx->makeUrl($id, $context, $params, 'full');
}
if ($id = $modx->getOption('ms2_payment_paypal_cancel_id', null, 0)) {
    $cancel = $modx->makeUrl($id, $context, $params, 'full');
}

$redirect = !empty($_GET['action']) && $_GET['action'] == 'success'
    ? $success
    : $cancel;
$modx->sendRedirect($redirect);