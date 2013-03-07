<?php
/**
 * miniShop2 Connector
 *
 * @package minishop2
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/');
require_once $corePath.'model/minishop2/minishop2.class.php';
$modx->miniShop2 = new miniShop2($modx);

$modx->lexicon->load('minishop2:default');

/* handle request */
$path = $modx->getOption('processorsPath', $modx->miniShop2->config, $corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));