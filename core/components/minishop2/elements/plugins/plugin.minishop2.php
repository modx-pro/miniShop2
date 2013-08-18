<?php
switch ($modx->event->name) {

	case 'OnManagerPageBeforeRender':
		$cssFile = MODX_ASSETS_URL . 'components/minishop2/css/mgr/main.css';
		$modx->controller->addCss($cssFile);
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