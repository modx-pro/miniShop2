<?php
switch($modx->event->name) {

	case 'OnManagerPageInit':
		$cssFile = $modx->getOption('minishop2.assets_url',null,$modx->getOption('assets_url').'components/minishop2/').'css/mgr/main.css';
		$modx->regClientCSS($cssFile);
		break;
	case 'OnHandleRequest':
		if (isset($_REQUEST['ms2_action']) && !empty($_REQUEST['ms2_action'])) {
			$action = $_REQUEST['ms2_action'];
			unset($_REQUEST['ms2_action']);
		} else {
			return;
		}
		$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
					$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

		$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
		if ($ctx != 'web') {$modx->switchContext($ctx);}

		/* @var miniShop2 $miniShop2 */
		$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/',array());
		if (($modx->error->hasError() || !($miniShop2 instanceof miniShop2)) && $isAjax) {die('Error');}
		
		$miniShop2->initialize($ctx, array('json_response' => $isAjax));

		switch ($action) {
			case 'cart/add'				: $response = $miniShop2->cart->add(@$_POST['id'], @$_POST['count'], @$_POST['options']); break;
			case 'cart/change'			: $response = $miniShop2->cart->change(@$_POST['key'], @$_POST['count']); break;
			case 'cart/remove'			: $response = $miniShop2->cart->remove(@$_POST['key']); break;
			case 'cart/clean'			: $response = $miniShop2->cart->clean(); break;
			case 'cart/get'				: $response = $miniShop2->cart->get(); break;
			case 'order/add'			: $response = $miniShop2->order->add(@$_POST['key'], @$_POST['value'], true); break;
			case 'order/submit'			: $response = $miniShop2->order->submit($_POST); break;
			case 'order/getcost'		: $response = $miniShop2->order->getcost(); break;
			case 'order/getrequired'	: $response = $miniShop2->order->getDeliveryRequiresFields(@$_POST['id']); break;
			case 'order/clean'			: $response = $miniShop2->order->clean(); break;
			case 'order/get'			: $response = $miniShop2->order->get(); break;
			default:
				$message	= $_REQUEST['ms2_action'] != $action ? 'ms2_err_register_globals' : 'ms2_err_unknown';
				$response	= json_encode(array('success' => false, 'message' => $modx->lexicon($message)));
		}

		if ($isAjax) die($response);
}