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
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/',array());

if ($modx->error->hasError() || !($miniShop2 instanceof miniShop2)) {die('Error');}

$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
$miniShop2->initialize($ctx, array('json_response' => true));

switch ($action) {
	case 'add': $response = $miniShop2->cart->add(@$_POST['id'], @$_POST['count'], @$_POST['data']); break;
	default: $response = '[]';
}

exit($response);