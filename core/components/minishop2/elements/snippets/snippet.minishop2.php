<?php
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
if (!($miniShop2 instanceof miniShop2)) return '';

$miniShop2->initialize($modx->context->key, $scriptProperties);