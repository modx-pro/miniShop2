<?php
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);

$cart = $miniShop2->cart->status();
$cart['total_cost'] = $miniShop2->formatPrice($cart['total_cost']);
$cart['total_weight'] = $miniShop2->formatWeight($cart['total_weight']);

return !empty($tpl) ? $modx->getChunk($tpl, $cart) : print_r($cart,1);