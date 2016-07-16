<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msMiniCart');

$cart = $miniShop2->cart->status();
$cart['total_cost'] = $miniShop2->formatPrice($cart['total_cost']);
$cart['total_weight'] = $miniShop2->formatWeight($cart['total_weight']);

return $miniShop2->pdoTools->getChunk($tpl, $cart);