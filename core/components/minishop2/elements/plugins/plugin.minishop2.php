<?php

/** @var modX $modx */
switch ($modx->event->name) {
    case 'OnMODXInit':
        // Load extensions
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $modx->getService('miniShop2')) {
            $miniShop2->loadMap();
        }
        break;

    case 'OnHandleRequest':
        // Handle ajax requests
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (empty($_REQUEST['ms2_action']) || !$isAjax) {
            return;
        }
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $modx->getService('miniShop2')) {
            $response = $miniShop2->handleRequest($_REQUEST['ms2_action'], @$_POST);
            @session_write_close();
            exit($response);
        }
        break;

    case 'OnManagerPageBeforeRender':
        /** @var miniShop2 $miniShop2 */
        if ($miniShop2 = $modx->getService('miniShop2')) {
            $modx->controller->addLexiconTopic('minishop2:default');
            $modx->regClientStartupScript($miniShop2->config['jsUrl'] . 'mgr/misc/ms2.manager.js');
        }
        break;

    case 'OnLoadWebDocument':
        /** @var miniShop2 $miniShop2 */
        $miniShop2 = $modx->getService('miniShop2');
        $registerFrontend = $modx->getOption('ms2_register_frontend', null, '1');
        if ($miniShop2 && $registerFrontend) {
            $miniShop2->registerFrontend($modx->context->get('key'));
        }
        // Handle non-ajax requests
        if (!empty($_REQUEST['ms2_action'])) {
            if ($miniShop2) {
                $miniShop2->handleRequest($_REQUEST['ms2_action'], @$_POST);
            }
        }
        // Set product fields as [[*resource]] tags
        if ($modx->resource->get('class_key') == 'msProduct') {
            if ($dataMeta = $modx->getFieldMeta('msProductData')) {
                unset($dataMeta['id']);
                $modx->resource->_fieldMeta = array_merge(
                    $modx->resource->_fieldMeta,
                    $dataMeta
                );
            }
        }
        break;

    case 'OnWebPageInit':
        // Set referrer cookie
        /** @var msCustomerProfile $profile */
        $referrerVar = $modx->getOption('ms2_referrer_code_var', null, 'msfrom', true);
        $cookieVar = $modx->getOption('ms2_referrer_cookie_var', null, 'msreferrer', true);
        $cookieTime = $modx->getOption('ms2_referrer_time', null, 86400 * 365, true);

        if (!$modx->user->isAuthenticated() && !empty($_REQUEST[$referrerVar])) {
            $code = trim($_REQUEST[$referrerVar]);
            if ($profile = $modx->getObject('msCustomerProfile', ['referrer_code' => $code])) {
                $referrer = $profile->get('id');
                setcookie($cookieVar, $referrer, time() + $cookieTime);
            }
        }
        break;

    case 'OnUserSave':
        // Save referrer id
        /** @var string $mode */
        if ($mode == modSystemEvent::MODE_NEW) {
            /** @var modUser $user */
            $cookieVar = $modx->getOption('ms2_referrer_cookie_var', null, 'msreferrer', true);
            $cookieTime = $modx->getOption('ms2_referrer_time', null, 86400 * 365, true);
            if ($modx->context->key != 'mgr' && !empty($_COOKIE[$cookieVar])) {
                if ($profile = $modx->getObject('msCustomerProfile', ['id' => $user->get('id')])) {
                    if (!$profile->get('referrer_id') && $_COOKIE[$cookieVar] != $user->get('id')) {
                        $profile->set('referrer_id', (int)$_COOKIE[$cookieVar]);
                        $profile->save();
                    }
                }
                setcookie($cookieVar, '', time() - $cookieTime);
            }
        }
        break;

    case 'msOnChangeOrderStatus':
        // Update customer stat
        if (empty($status) || $status != 2) {
            return;
        }

        /** @var modUser $user */
        /** @var msOrder $order */
        if ($user = $order->getOne('User')) {
            $q = $modx->newQuery('msOrder', ['type' => 0]);
            $q->innerJoin('modUser', 'modUser', ['modUser.id = msOrder.user_id']);
            $q->innerJoin('msOrderLog', 'msOrderLog', [
                'msOrderLog.order_id = msOrder.id',
                'msOrderLog.action' => 'status',
                'msOrderLog.entry' => $status,
            ]);
            $q->where(['msOrder.user_id' => $user->get('id')]);
            $q->groupby('msOrder.user_id');
            $q->select('SUM(msOrder.cost)');
            if ($q->prepare() && $q->stmt->execute()) {
                $spent = $q->stmt->fetchColumn();
                /** @var msCustomerProfile $profile */
                if ($profile = $modx->getObject('msCustomerProfile', ['id' => $user->get('id')])) {
                    $profile->set('spent', $spent);
                    $profile->save();
                }
            }
        }
        break;
}
