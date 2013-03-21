<?php
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);

$cart = $miniShop2->cart->status();
return !empty($tpl) ? $modx->getChunk($tpl, $cart) : print_r($cart,1);