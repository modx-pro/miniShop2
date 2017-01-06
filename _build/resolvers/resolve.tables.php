<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('minishop2.core_path', null,
                    $modx->getOption('core_path') . 'components/minishop2/') . 'model/';
            $modx->addPackage('minishop2', $modelPath);

            $manager = $modx->getManager();
            $tmp = array(
                'msProductData',
                'msVendor',
                'msCategoryMember',
                'msProductOption',
                'msProductFile',
                'msOrder',
                'msOrderStatus',
                'msOrderLog',
                'msPayment',
                'msDelivery',
                'msDeliveryMember',
                'msOrderAddress',
                'msOrderProduct',
                'msLink',
                'msProductLink',
                'msCustomerProfile',
                'msOption',
                'msCategoryOption',
            );
            foreach ($tmp as $v) {
                $manager->createObjectContainer($v);
            }

            $level = $modx->getLogLevel();
            $modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);

            $manager->addField('msProductFile', 'properties');
            $manager->addField('msProductFile', 'hash');
            $manager->addIndex('msProductFile', 'hash');
            $manager->addField('msProductFile', 'active');
            $manager->addIndex('msProductFile', 'active');

            $manager->addField('msOrderProduct', 'name');

            $manager->alterField('msDelivery', 'price');
            $manager->addField('msPayment', 'price', array('after' => 'description'));

            $manager->addField('msCustomerProfile', 'spent', array('after' => 'account'));
            $manager->addIndex('msCustomerProfile', 'spent');

            $manager->addField('msOrder', 'type');
            $manager->addIndex('msOrder', 'type');

            $manager->addField('msOption', 'description', array('after' => 'caption'));
            $manager->addField('msOption', 'category', array('after' => 'description'));
            $manager->addField('msOption', 'measure_unit', array('after' => 'description'));

            // Fix for wrong events
            /*
            if ($modx->getObject('modEvent', array('name' => '1', 'groupname' => 'miniShop2'))) {
                $modx->removeCollection('modEvent', array(
                    'groupname' => 'miniShop2',
                    'name:IN' => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27)
                ));
            }
            */
            $modx->setLogLevel($level);
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;