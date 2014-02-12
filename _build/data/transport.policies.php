<?php

$policies = array();

/* @var modAccessPolicy $policy */
$policy= $modx->newObject('modAccessPolicy');
$policy->fromArray(array (
	'name' => 'miniShopManagerPolicy',
	'description' => 'A policy for create and update miniShop categories and products.',
	'parent' => 0,
	'class' => '',
	'lexicon' => 'minishop2:permissions',
	'data' => json_encode(array(
		'mscategory_save' => true,
		'msproduct_save' => true,
		'msorder_save' => true,
		'msorder_view' => true,
		'msorder_list' => true,
		'mssetting_save' => true,
		'mssetting_view' => true,
		'mssetting_list' => true,
		'msproductfile_save' => true,
		'msproductfile_generate' => true,
		'msproductfile_list' => true,
	))
), '', true, true);

$policies[] = $policy;

return $policies;
