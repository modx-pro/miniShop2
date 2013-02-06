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
			/* @var modX $modx */
			$modx =& $object->xpdo;
			$modelPath = $modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/';
			$modx->addPackage('minishop2',$modelPath);

			$manager = $modx->getManager();

			$manager->createObjectContainer('msProductData');
			$manager->createObjectContainer('msProductTag');
			$manager->createObjectContainer('msCategoryMember');
			$manager->createObjectContainer('msVendor');

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('minishop2', '[[++core_path]]components/minishop2/model/');
			}

			break;
		case xPDOTransport::ACTION_UNINSTALL:
			if ($modx instanceof modX) {
				$modx->removeExtensionPackage('minishop2');
			}
			break;
	}
}
return true;