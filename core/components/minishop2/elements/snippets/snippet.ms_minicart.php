<?php
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);

$cart = $miniShop2->cart->status();
return !empty($tpl) ? $modx->getChunk($tpl, $cart) : print_r($cart,1);