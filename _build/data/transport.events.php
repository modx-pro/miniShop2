<?php

$events = array();

$tmp = array(
    'msOnBeforeAddToCart',
    'msOnAddToCart',
    'msOnBeforeChangeInCart',
    'msOnChangeInCart',
    'msOnBeforeRemoveFromCart',
    'msOnRemoveFromCart',
    'msOnBeforeEmptyCart',
    'msOnEmptyCart',

    'msOnBeforeAddToOrder',
    'msOnAddToOrder',
    'msOnBeforeRemoveFromOrder',
    'msOnRemoveFromOrder',
    'msOnBeforeEmptyOrder',
    'msOnEmptyOrder',

    'msOnBeforeGetOrderCost',
    'msOnGetOrderCost',

    'msOnBeforeChangeOrderStatus',
    'msOnChangeOrderStatus',

    'msOnBeforeCreateOrder',
    'msOnCreateOrder',
    'msOnBeforeUpdateOrder',
    'msOnUpdateOrder',
    'msOnBeforeRemoveOrder',
    'msOnRemoveOrder',

    'msOnBeforeCreateOrderProduct',
    'msOnCreateOrderProduct',
    'msOnBeforeUpdateOrderProduct',
    'msOnUpdateOrderProduct',
    'msOnBeforeRemoveOrderProduct',
    'msOnRemoveOrderProduct',

    'msOnSubmitOrder',
    'msOnManagerCustomCssJs',

    'msOnBeforeValidateOrderValue',
    'msOnValidateOrderValue',

    'msOnGetProductPrice',
    'msOnGetProductWeight',
);

foreach ($tmp as $k => $v) {
    /** @var modEvent $event */
    $event = $modx->newObject('modEvent');
    $event->fromArray(array(
        'name' => $v,
        'service' => 6,
        'groupname' => PKG_NAME,
    ), '', true, true);
    $events[] = $event;
}

return $events;