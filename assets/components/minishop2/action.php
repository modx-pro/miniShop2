<?php

if (empty($_REQUEST['action'])) {
	die('Access denied');
}
else {
	$action = $_REQUEST['action'];
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';

$modx->getService('error','error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
if ($ctx != 'web') {$modx->switchContext($ctx);}

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/',array());
if ($modx->error->hasError() || !($miniShop2 instanceof miniShop2)) {die('Error');}
$miniShop2->initialize($ctx, array('json_response' => true));

switch ($action) {
	case 'cart/add': $response = $miniShop2->cart->add(@$_POST['id'], @$_POST['count'], @$_POST['options']); break;
	case 'cart/change': $response = $miniShop2->cart->change(@$_POST['key'], @$_POST['count']); break;
	case 'cart/remove': $response = $miniShop2->cart->remove(@$_POST['key']); break;
	case 'cart/clean': $response = $miniShop2->cart->clean(); break;
	case 'cart/get': $response = $miniShop2->cart->get(); break;
	case 'order/add': $response = $miniShop2->order->add(@$_POST['key'], @$_POST['value']); break;
	case 'order/submit': $response = $miniShop2->order->submit(@$_POST['order']); break;
	case 'order/getcost': $response = $miniShop2->order->getcost(); break;
	case 'order/clean': $response = $miniShop2->order->clean(); break;
	case 'order/get': $response = $miniShop2->order->get(); break;
	default:
		$message = $_REQUEST['action'] != $action ? 'ms2_err_register_globals' : 'ms2_err_unknown';
		$response = json_encode(array('success' => false, 'message' => $modx->lexicon($message)));
}

exit($response);