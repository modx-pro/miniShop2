<?php

/** @noinspection PhpIncludeInspection */

require_once dirname(__FILE__, 4) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';

/** @var modX $modx */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$modx->lexicon->load('minishop2:default', 'minishop2:manager');

$path = $modx->getOption('processorsPath', $miniShop2->config, MODX_CORE_PATH . 'components/minishop2/processors/');
/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);
