<?php

/** @var modX $modx */
$events = [];

$tmp = [
    'msOnBeforeAddToCart',
    'msOnAddToCart',
    'msOnBeforeChangeInCart',
    'msOnChangeInCart',
    'msOnBeforeRemoveFromCart',
    'msOnRemoveFromCart',
    'msOnBeforeEmptyCart',
    'msOnEmptyCart',
    'msOnGetStatusCart',

    'msOnBeforeAddToOrder',
    'msOnAddToOrder',
    'msOnBeforeValidateOrderValue',
    'msOnValidateOrderValue',
    'msOnBeforeRemoveFromOrder',
    'msOnRemoveFromOrder',
    'msOnBeforeEmptyOrder',
    'msOnEmptyOrder',
    'msOnBeforeGetOrderCost',
    'msOnGetOrderCost',
    'msOnSubmitOrder',
    'msOnBeforeChangeOrderStatus',
    'msOnChangeOrderStatus',
    'msOnBeforeGetOrderCustomer',
    'msOnGetOrderCustomer',

    'msOnBeforeCreateOrder',
    'msOnBeforeMgrCreateOrder',
    'msOnCreateOrder',
    'msOnMgrCreateOrder',
    'msOnBeforeUpdateOrder',
    'msOnUpdateOrder',
    'msOnBeforeSaveOrder',
    'msOnSaveOrder',
    'msOnBeforeRemoveOrder',
    'msOnRemoveOrder',

    'msOnBeforeCreateOrderProduct',
    'msOnCreateOrderProduct',
    'msOnBeforeUpdateOrderProduct',
    'msOnUpdateOrderProduct',
    'msOnBeforeRemoveOrderProduct',
    'msOnRemoveOrderProduct',

    'msOnGetProductPrice',
    'msOnGetProductWeight',
    'msOnGetProductFields',

    'msOnManagerCustomCssJs',

    'msOnBeforeVendorCreate',
    'msOnAfterVendorCreate',
    'msOnBeforeVendorUpdate',
    'msOnAfterVendorUpdate',
    'msOnBeforeVendorDelete',
    'msOnAfterVendorDelete',
];

foreach ($tmp as $k => $v) {
    /** @var modEvent $event */
    $event = $modx->newObject('modEvent');
    $event->fromArray([
        'name' => $v,
        'service' => 6,
        'groupname' => PKG_NAME,
    ], '', true, true);
    $events[] = $event;
}

return $events;
