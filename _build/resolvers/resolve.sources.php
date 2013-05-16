<?php
	/**
	 * Resolve creating media sources
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

				$tmp = explode('/', MODX_ASSETS_URL);
				$assets = $tmp[count($tmp) - 2];

				$properties = array(
					'name' => 'MS2 Images'
					,'description' => 'Default media source for images of miniShop2 products'
					,'class_key' => 'sources.modFileMediaSource'
					,'properties' => array(
						'basePath' => array(
							'name' => 'basePath','desc' => 'prop_file.basePath_desc','type' => 'textfield','lexicon' => 'core:source'
							,'value' => $assets . '/images/products/'
						)
						,'baseUrl' => array(
							'name' => 'baseUrl','desc' => 'prop_file.baseUrl_desc','type' => 'textfield','lexicon' => 'core:source'
							,'value' => 'assets/images/products/'
						)
						,'imageExtensions' => array(
							'name' => 'imageExtensions','desc' => 'prop_file.imageExtensions_desc','type' => 'textfield','lexicon' => 'core:source'
							,'value' => 'jpg,jpeg,png'
						)
						,'allowedFileTypes' => array(
							'name' => 'allowedFileTypes','desc' => 'prop_file.allowedFileTypes_desc','type' => 'textfield','lexicon' => 'core:source'
							,'value' => 'jpg,jpeg,png'
						)
						,'thumbnailType' => array(
							'name' => 'thumbnailType','desc' => 'prop_file.thumbnailType_desc','type' => 'list','lexicon' => 'core:source'
							,'options' => array(array('value' => 'png','text' => 'png'), array('value' => 'jpg','text' => 'jpg'))
							,'value' => 'jpg'
						)
						,'thumbnails' => array(
							'name' => 'thumbnails','desc' => 'ms2_source_thumbnails_desc','type' => 'textarea','lexicon' => 'minishop2:setting'
							,'value' => '[{"w":120,"h":90,"q":90,"zc":"1","bg":"000000"},{"w":360,"h":270,"q":90,"zc":"1","bg":"000000"}]'
						)
					)
					,'is_stream' => 1
				);
				if (!$source = $modx->getObject('sources.modMediaSource', array('name' => $properties['name']))) {
					$source = $modx->newObject('sources.modMediaSource', $properties);
					$source->save();
				}
				if ($setting = $modx->getObject('modSystemSetting', array('key' => 'ms2_product_source_default'))) {
					$setting->set('value', $source->get('id'));
					$setting->save();
				}

				@mkdir(MODX_ASSETS_PATH . 'images/');
				@mkdir(MODX_ASSETS_PATH . 'images/products/');

				break;
			case xPDOTransport::ACTION_UNINSTALL:
				if ($modx instanceof modX) {
					//$modx->removeExtensionPackage('minishop2');
				}
				break;
		}
	}
	return true;