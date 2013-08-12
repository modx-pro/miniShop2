<?php

$events = array();

$tmp = array(
	'msOnBeforeAddToCart' => array()
	,'msOnAddToCart' => array()
	,'msOnBeforeChangeInCart' => array()
	,'msOnChangeInCart' => array()
	,'msOnBeforeRemoveFromCart' => array()
	,'msOnRemoveFromCart' => array()
	,'msOnBeforeEmptyCart' => array()
	,'msOnEmptyCart' => array()

	,'msOnBeforeAddToOrder' => array()
	,'msOnAddToOrder' => array()
	,'msOnBeforeRemoveFromOrder' => array()
	,'msOnRemoveFromOrder' => array()
	,'msOnBeforeEmptyOrder' => array()
	,'msOnEmptyOrder' => array()

	,'msOnBeforeChangeOrderStatus' => array()
	,'msOnChangeOrderStatus' => array()

	,'msOnBeforeUpdateOrder' => array()
	,'msOnUpdateOrder' => array()
	,'msOnBeforeCreateOrder' => array()
	,'msOnCreateOrder' => array()
	,'msOnBeforeRemoveOrder' => array()
	,'msOnRemoveOrder' => array()

	,'msOnSubmitOrder' => array()
	,'msOnManagerCustomCssJs' => array()
);

foreach ($tmp as $k => $v) {
	/* @var modEvent $event */
	$event = $modx->newObject('modEvent');
	$event->fromArray(array_merge(array(
		'name' => $k
		,'service' => 6
		,'groupname' => PKG_NAME
	), $v)
	,'', true, true);

	$events[] = $event;
}

return $events;