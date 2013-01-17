<?php
/**
 * The default Permission scheme for the miniShop2.
 *
 * @package minishop2
 * @subpackage build
 */
$permissions = array();

$permissions[0][] = $modx->newObject('modAccessPermission',array(
	'name' => 'mscategory_save',
	'description' => 'mscategory_save',
	'value' => true,
));

$permissions[0][] = $modx->newObject('modAccessPermission',array(
	'name' => 'msproduct_save',
	'description' => 'msproduct_save',
	'value' => true,
));


return $permissions;