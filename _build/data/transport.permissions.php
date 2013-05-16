<?php
/**
 * The default Permission scheme for the miniShop2.
 *
 * @package minishop2
 * @subpackage build
 */
$permissions = array();

$tmp = array(
	array(
		'mscategory_save' => array()
		,'msproduct_save' => array()
		,'msorder_save' => array()
		,'msorder_view' => array()
	)
);

foreach ($tmp as $k => $v) {
	foreach ($v as $k2 => $v2) {
		/* @var modAccessPermission $event */
		$permission = $modx->newObject('modAccessPermission');
		$permission->fromArray(array_merge(array(
				'name' => $k2
				,'description' => $k2
				,'value' => true
			), $v2)
			,'', true, true);
		$permissions[$k][] = $permission;
	}
}

return $permissions;