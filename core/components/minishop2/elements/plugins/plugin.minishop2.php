<?php
switch ($modx->event->name) {

	case 'OnManagerPageBeforeRender':
		$modx23 = !empty($modx->version) && version_compare($modx->version['full_version'], '2.3.0', '>=');
		$modx->controller->addHtml('<script type="text/javascript">
			Ext.onReady(function() {
				MODx.modx23 = '.(int)$modx23.';
			});
		</script>');
		if (!$modx23) {
			$modx->controller->addCss(MODX_ASSETS_URL . 'components/minishop2/css/mgr/bootstrap.min.css');
		}
		$modx->controller->addCss(MODX_ASSETS_URL . 'components/minishop2/css/mgr/main.css');
		break;

	case 'OnHandleRequest':
	case 'OnLoadWebDocument':
		$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

		if (empty($_REQUEST['ms2_action']) || ($isAjax && $modx->event->name != 'OnHandleRequest') || (!$isAjax && $modx->event->name != 'OnLoadWebDocument')) {return;}
		$action = trim($_REQUEST['ms2_action']);
		$ctx = !empty($_REQUEST['ctx']) ? (string) $_REQUEST['ctx'] : 'web';
		if ($ctx != 'web') {$modx->switchContext($ctx);}

		/* @var miniShop2 $miniShop2 */
		$miniShop2 = $modx->getService('minishop2');
		$miniShop2->initialize($ctx, array('json_response' => $isAjax));
		if (!($miniShop2 instanceof miniShop2)) {
			@session_write_close();
			exit('Could not initialize miniShop2');
		}

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
				$message = ($_REQUEST['ms2_action'] != $action)
					? 'ms2_err_register_globals'
					: 'ms2_err_unknown';
				$response = $miniShop2->error($message);
		}

		if ($isAjax) {
			@session_write_close();
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

	case 'msOnChangeOrderStatus':
		if (empty($status) || $status != 2) {return;}

		/** @var modUser $user */
		if ($user = $order->getOne('User')) {
			$q = $modx->newQuery('msOrder', array('type' => 0));
			$q->innerJoin('modUser', 'modUser', array('`modUser`.`id` = `msOrder`.`user_id`'));
			$q->innerJoin('msOrderLog', 'msOrderLog', array(
				'`msOrderLog`.`order_id` = `msOrder`.`id`',
				'msOrderLog.action' => 'status',
				'msOrderLog.entry' => $status,
			));
			$q->where(array('msOrder.user_id' => $user->id));
			$q->groupby('msOrder.user_id');
			$q->select('SUM(`msOrder`.`cost`)');
			if ($q->prepare() && $q->stmt->execute()) {
				$spent = $q->stmt->fetch(PDO::FETCH_COLUMN);
				/** @var msCustomerProfile $profile */
				if ($profile = $modx->getObject('msCustomerProfile', $user->id)) {
					$profile->set('spent', $spent);
					$profile->save();
				}
			}
		}
		break;
}
