<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modAccessPolicy $policy */
            if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'miniShopManagerPolicy'))) {
                if ($template = $modx->getObject('modAccessPolicyTemplate',
                    array('name' => 'miniShopManagerPolicyTemplate'))
                ) {
                    $policy->set('template', $template->get('id'));
                    $policy->save();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,
                        '[miniShop2] Could not find miniShopManagerPolicyTemplate Access Policy Template!');
                }

                /** @var modUserGroup $adminGroup */
                if ($adminGroup = $modx->getObject('modUserGroup', array('name' => 'Administrator'))) {
                    $properties = array(
                        'target' => 'mgr',
                        'principal_class' => 'modUserGroup',
                        'principal' => $adminGroup->get('id'),
                        'authority' => 9999,
                        'policy' => $policy->get('id'),
                    );
                    if (!$modx->getObject('modAccessContext', $properties)) {
                        $access = $modx->newObject('modAccessContext');
                        $access->fromArray($properties);
                        $access->save();
                    }
                }
                break;
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2] Could not find miniShopManagerPolicy Access Policy!');
            }
            break;
    }
}
return true;