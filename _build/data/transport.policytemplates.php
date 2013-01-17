<?php
/**
 * Default miniShop2 Access Policy Templates
 *
 * @package minishop2
 * @subpackage build
 */
$templates = array();
$permissions = include dirname(__FILE__).'/transport.permissions.php';

$templates[0]= $modx->newObject('modAccessPolicyTemplate');
$templates[0]->fromArray(array(
	'id' => 0,
	'name' => 'miniShopManagerPolicyTemplate',
	'description' => 'A policy for miniShop2 managers.',
	'lexicon' => 'minishop2:permissions',
	'template_group' => 1,
));
if (is_array($permissions[0])) {
	$templates[0]->addMany($permissions[0]);
} else { $modx->log(modX::LOG_LEVEL_ERROR,'Could not load miniShop2 Policy Template.'); }


return $templates;