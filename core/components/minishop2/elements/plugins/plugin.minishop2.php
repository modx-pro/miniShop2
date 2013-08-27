<?php
switch ($modx->event->name) {

	case 'OnManagerPageBeforeRender':
		$cssFile = MODX_ASSETS_URL . 'components/minishop2/css/mgr/main.css';
		$modx->controller->addCss($cssFile);
		break;

	case 'OnHandleRequest':
		if (!empty($_REQUEST['ms2_action'])) {
			$action = trim($_REQUEST['ms2_action']);
		}
		else {
			return;
		}

		$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

		/* @var miniShop2 $miniShop2 */
		$miniShop2 = $modx->getService('minishop2');
		if (($modx->error->hasError() || !($miniShop2 instanceof miniShop2)) && $isAjax) {die('Error');}

		$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
		if ($ctx != 'web') {$modx->switchContext($ctx);}

		$miniShop2->initialize($ctx, array('json_response' => $isAjax));

		switch ($action) {
			case 'cart/add': $response = $miniShop2->cart->add(@$_POST['id'], @$_POST['count'], @$_POST['options']); break;
			case 'cart/change': $response = $miniShop2->cart->change(@$_POST['key'], @$_POST['count']); break;
			case 'cart/remove': $response = $miniShop2->cart->remove(@$_POST['key']); break;
			case 'cart/clean': $response = $miniShop2->cart->clean(); break;
			case 'cart/get': $response = $miniShop2->cart->get(); break;
			case 'order/add': $response = $miniShop2->order->add(@$_POST['key'], @$_POST['value']); break;
			case 'order/submit': $response = $miniShop2->order->submit($_POST); break;
			case 'order/getcost': $response = $miniShop2->order->getcost(); break;
			case 'order/getrequired': $response = $miniShop2->order->getDeliveryRequiresFields(@$_POST['id']); break;
			case 'order/clean': $response = $miniShop2->order->clean(); break;
			case 'order/get': $response = $miniShop2->order->get(); break;
			default:
				$message = $_REQUEST['ms2_action'] != $action ? 'ms2_err_register_globals' : 'ms2_err_unknown';
				$response = $modx->toJSON(array('success' => false, 'message' => $modx->lexicon($message)));
		}

		if ($isAjax) {
			exit($response);
		}
		break;

	case 'OnWebPageInit':
		/* @var msCustomerProfile $profile */
		$referrerVar = $modx->getOption('ms2_referrer_code_var', null, 'msfrom', true);
		$cookieVar = $modx->getOption('ms2_referrer_cookie_var', null, 'msreferrer', true);
		$cookieTime = $modx->getOption('ms2_referrer_time', null, 86400 * 365, true);

		if (!$modx->user->isAuthenticated() && !empty($_REQUEST[$referrerVar])) {
			$code = trim($_REQUEST[$referrerVar]);
			if ($profile = $modx->getObject('msCustomerProfile', array('referrer_code' => $code))) {
				$referrer = $profile->id;
				setcookie($cookieVar, $referrer, time() + $cookieTime);
			}
		}
		elseif ($modx->user->isAuthenticated() && !empty($_COOKIE[$cookieVar])) {
			if ($profile = $modx->getObject('msCustomerProfile', $modx->user->id)) {
				if (!$profile->get('referrer_id') && $_COOKIE[$cookieVar] != $modx->user->id) {
					$profile->set('referrer_id', $_COOKIE[$cookieVar]);
					$profile->save();
				}
			}
			setcookie($cookieVar, '', time() - $cookieTime);
		}
		break;
}