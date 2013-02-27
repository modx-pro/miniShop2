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


return $events;