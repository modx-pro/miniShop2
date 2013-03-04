<?php
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);

$cart = $miniShop2->cart->status();
if (!empty($tpl)) {
	return $modx->getChunk($tpl, $cart);
}
else {
	return print_r($cart,1);
}