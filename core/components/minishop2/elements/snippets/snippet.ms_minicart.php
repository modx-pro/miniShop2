<?php
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);

$cart = $miniShop2->cart->status();
$cart['total_cost'] = $miniShop2->formatPrice($cart['total_cost']);
$cart['total_weight'] = $miniShop2->formatWeight($cart['total_weight']);

if(!isset($pdo)) $pdo = $modx->getService('pdoTools');
return !empty($tpl) ? $pdo->getChunk($tpl, $cart) : print_r($cart,1);
