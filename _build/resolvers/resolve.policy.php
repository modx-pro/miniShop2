<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modAccessPolicy $policy */
            $policy = $modx->getObject('modAccessPolicy', ['name' => 'miniShopManagerPolicy']);
            if ($policy) {
                $template = $modx->getObject(
                    'modAccessPolicyTemplate',
                    ['name' => 'miniShopManagerPolicyTemplate']
                );
                if ($template) {
                    $policy->set('template', $template->get('id'));
                    $policy->save();
                } else {
                    $modx->log(
                        xPDO::LOG_LEVEL_ERROR,
                        '[miniShop2] Could not find miniShopManagerPolicyTemplate Access Policy Template!'
                    );
                }

                /** @var modUserGroup $adminGroup */
                $adminGroup = $modx->getObject('modUserGroup', ['name' => 'Administrator']);
                if ($adminGroup) {
                    $properties = [
                        'target' => 'mgr',
                        'principal_class' => 'modUserGroup',
                        'principal' => $adminGroup->get('id'),
                        'authority' => 9999,
                        'policy' => $policy->get('id'),
                    ];
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
