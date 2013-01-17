<?php
/**
 * Resolve creating policies
 * @var xPDOObject $object
 * @var array $options
 * @package minishop2
 * @subpackage build
 */

if ($object->xpdo) {
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:

			if ($policy = $modx->getObject('modAccessPolicy',array('name' => 'miniShopManagerPolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate',array('name' => 'miniShopManagerPolicyTemplate'))) {
					$policy->set('template',$template->get('id'));
					$policy->save();
				} else {
					$modx->log(xPDO::LOG_LEVEL_ERROR,'[miniShop2] Could not find miniShopManagerPolicyTemplate Access Policy Template!');
				}
			} else {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not find miniShopManagerPolicy Access Policy!');
			}

			break;
	}
}
return true;