<?php
/**
 * The default Policy scheme for the miniShop2.
 *
 * @package minishop2
 * @subpackage build
 */
$policies = array();

$policies[0]= $modx->newObject('modAccessPolicy');
$policies[0]->fromArray(array (
	'id' => 0,
	'name' => 'miniShopManagerPolicy',
	'description' => 'A policy for create and update miniShop categories and products.',
	'parent' => 0,
	'class' => '',
	'lexicon' => 'minishop2:permissions',
	'data' => '{"mscategory_save":true,"msproduct_save":true,"msorder_save":true,"msorder_view":true}',
), '', true, true);


return $policies;
