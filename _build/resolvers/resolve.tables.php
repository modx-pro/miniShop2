<?php
/**
 * Resolve creating db tables
 *
 * @package minishop2
 * @subpackage build
 */
if ($object->xpdo) {
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			/* @var modX $modx */
			$modx =& $object->xpdo;
			$modelPath = $modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/';
			$modx->addPackage('minishop2',$modelPath);

			$manager = $modx->getManager();

			$manager->createObjectContainer('msProductData');
			$manager->createObjectContainer('msVendor');
			$manager->createObjectContainer('msCategoryMember');
			$manager->createObjectContainer('msProductOption');
			$manager->createObjectContainer('msProductFile');
			$manager->createObjectContainer('msOrder');
			$manager->createObjectContainer('msOrderStatus');
			$manager->createObjectContainer('msOrderLog');
			$manager->createObjectContainer('msPayment');
			$manager->createObjectContainer('msDelivery');
			$manager->createObjectContainer('msDeliveryMember');
			$manager->createObjectContainer('msOrderAddress');
			$manager->createObjectContainer('msOrderProduct');
			$manager->createObjectContainer('msLink');
			$manager->createObjectContainer('msProductLink');

			$msProductData = $modx->getTableName('msProductData');
			$modx->exec("ALTER TABLE {$msProductData} CHANGE `price` `price` DECIMAL(12,2) NOT NULL DEFAULT '0';");
			$modx->exec("ALTER TABLE {$msProductData} CHANGE `old_price` `old_price` DECIMAL(12,2) NOT NULL DEFAULT '0';");
			$modx->exec("ALTER TABLE {$msProductData} CHANGE `weight` `weight` DECIMAL(13,3) NOT NULL DEFAULT '0';");

			$msVendor = $modx->getTableName('msVendor');
			$modx->exec("ALTER TABLE {$msVendor} CHANGE `image` `logo` VARCHAR(255) NULL DEFAULT NULL;");
			$modx->exec("ALTER TABLE {$msVendor} ADD `email` VARCHAR(255) NULL DEFAULT NULL AFTER `country`;");

			$msPayment = $modx->getTableName('msPayment');
			$modx->exec("ALTER TABLE {$msPayment} ADD `logo` VARCHAR(255) NULL DEFAULT NULL AFTER `description`;");
			$modx->exec("ALTER TABLE {$msPayment} ADD `rank` TINYINT(1) NOT NULL DEFAULT '0' AFTER `logo`;");
			$modx->exec("ALTER TABLE {$msPayment} ADD `properties` TEXT NULL DEFAULT NULL AFTER `class`;");

			$msDelivery = $modx->getTableName('msDelivery');
			$modx->exec("ALTER TABLE {$msDelivery} CHANGE `add_price` `weight_price` VARCHAR(10) NOT NULL DEFAULT '0';");
			$modx->exec("ALTER TABLE {$msDelivery} ADD `distance_price` VARCHAR(10) NOT NULL DEFAULT '0' AFTER `weight_price`;");
			$modx->exec("ALTER TABLE {$msDelivery} ADD `logo` VARCHAR(255) NULL DEFAULT NULL AFTER `distance_price`;");
			$modx->exec("ALTER TABLE {$msDelivery} ADD `rank` TINYINT(1) NOT NULL DEFAULT '0' AFTER `logo`;");
			$modx->exec("ALTER TABLE {$msDelivery} ADD `properties` TEXT NULL DEFAULT NULL AFTER `class`;");

			$msVendor = $modx->getTableName('msVendor');
			$modx->exec("ALTER TABLE {$msVendor} ADD `resource` INT(10) UNSIGNED NULL DEFAULT '0' AFTER `name`;");

			$msProductFile = $modx->getTableName('msProductFile');
			$modx->exec("ALTER TABLE {$msProductFile} ORDER BY `rank`");

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('minishop2', '[[++core_path]]components/minishop2/model/');
			}

			break;
		case xPDOTransport::ACTION_UNINSTALL:
			if ($modx instanceof modX) {
				//$modx->removeExtensionPackage('minishop2');
			}
			break;
	}
}
return true;