<?php

ini_set('display_errors', 1);
ini_set('error_reporting', 1);

define('MODX_API_MODE', true);
require dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_FATAL);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->error->message = null;

$miniShop = $modx->getService('minishop','miniShop', MODX_CORE_PATH . 'components/minishop/model/minishop/', array());
if (!($miniShop instanceof miniShop)) return 'miniShop1 not found';

$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH .'components/pdotools/model/pdotools/', array());
if (!($pdoFetch instanceof pdoFetch)) return 'You need to install pdoTools';
$pdoFetch->addTime('pdoTools initialized');