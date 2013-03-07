<?php
/**
 * Add miniShop2 events for plugins to build
 *
 * @package tickets
 * @subpackage build
 */
$events = array();

$events['msOnBeforeAddToCart']= $modx->newObject('modEvent');
$events['msOnBeforeAddToCart']->fromArray(array (
	'name' => 'msOnBeforeAddToCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnAddToCart']= $modx->newObject('modEvent');
$events['msOnAddToCart']->fromArray(array (
	'name' => 'msOnAddToCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnBeforeChangeInCart']= $modx->newObject('modEvent');
$events['msOnBeforeChangeInCart']->fromArray(array (
	'name' => 'msOnBeforeChangeInCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnChangeInCart']= $modx->newObject('modEvent');
$events['msOnChangeInCart']->fromArray(array (
	'name' => 'msOnChangeInCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnBeforeRemoveFromCart']= $modx->newObject('modEvent');
$events['msOnBeforeRemoveFromCart']->fromArray(array (
	'name' => 'msOnBeforeRemoveFromCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnRemoveFromCart']= $modx->newObject('modEvent');
$events['msOnRemoveFromCart']->fromArray(array (
	'name' => 'msOnRemoveFromCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnBeforeEmptyCart']= $modx->newObject('modEvent');
$events['msOnBeforeEmptyCart']->fromArray(array (
	'name' => 'msOnBeforeEmptyCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnEmptyCart']= $modx->newObject('modEvent');
$events['msOnEmptyCart']->fromArray(array (
	'name' => 'msOnEmptyCart',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnManagerCustomCssJs']= $modx->newObject('modEvent');
$events['msOnManagerCustomCssJs']->fromArray(array (
	'name' => 'msOnManagerCustomCssJs',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnBeforeAddToOrder']= $modx->newObject('modEvent');
$events['msOnBeforeAddToOrder']->fromArray(array (
	'name' => 'msOnBeforeAddToOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnAddToOrder']= $modx->newObject('modEvent');
$events['msOnAddToOrder']->fromArray(array (
	'name' => 'msOnAddToOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnBeforeRemoveFromOrder']= $modx->newObject('modEvent');
$events['msOnBeforeRemoveFromOrder']->fromArray(array (
	'name' => 'msOnBeforeRemoveFromOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnRemoveFromOrder']= $modx->newObject('modEvent');
$events['msOnRemoveFromOrder']->fromArray(array (
	'name' => 'msOnRemoveFromOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnSubmitOrder']= $modx->newObject('modEvent');
$events['msOnSubmitOrder']->fromArray(array (
	'name' => 'msOnBeforeSubmitOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnBeforeCreateOrder']= $modx->newObject('modEvent');
$events['msOnBeforeCreateOrder']->fromArray(array (
	'name' => 'msOnBeforeCreateOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnCreateOrder']= $modx->newObject('modEvent');
$events['msOnCreateOrder']->fromArray(array (
	'name' => 'msOnCreateOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnBeforeEmptyOrder']= $modx->newObject('modEvent');
$events['msOnBeforeEmptyOrder']->fromArray(array (
	'name' => 'msOnBeforeEmptyOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

$events['msOnEmptyOrder']= $modx->newObject('modEvent');
$events['msOnEmptyOrder']->fromArray(array (
	'name' => 'msOnEmptyOrder',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);


$events['msOnBeforeChangeOrderStatus']= $modx->newObject('modEvent');
$events['msOnBeforeChangeOrderStatus']->fromArray(array (
	'name' => 'msOnBeforeChangeOrderStatus',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);


$events['msOnChangeOrderStatus']= $modx->newObject('modEvent');
$events['msOnChangeOrderStatus']->fromArray(array (
	'name' => 'msOnChangeOrderStatus',
	'service' => 6,
	'groupname' => 'miniShop2',
), '', true, true);

return $events;