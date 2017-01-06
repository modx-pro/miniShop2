<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $tmp = explode('/', MODX_ASSETS_URL);
            $assets = $tmp[count($tmp) - 2];

            $properties = array(
                'name' => 'MS2 Images',
                'description' => 'Default media source for images of miniShop2 products',
                'class_key' => 'sources.modFileMediaSource',
                'properties' => array(
                    'basePath' => array(
                        'name' => 'basePath',
                        'desc' => 'prop_file.basePath_desc',
                        'type' => 'textfield',
                        'lexicon' => 'core:source',
                        'value' => $assets . '/images/products/',
                    ),
                    'baseUrl' => array(
                        'name' => 'baseUrl',
                        'desc' => 'prop_file.baseUrl_desc',
                        'type' => 'textfield',
                        'lexicon' => 'core:source',
                        'value' => 'assets/images/products/',
                    ),
                    'imageExtensions' => array(
                        'name' => 'imageExtensions',
                        'desc' => 'prop_file.imageExtensions_desc',
                        'type' => 'textfield',
                        'lexicon' => 'core:source',
                        'value' => 'jpg,jpeg,png,gif',
                    ),
                    'allowedFileTypes' => array(
                        'name' => 'allowedFileTypes',
                        'desc' => 'prop_file.allowedFileTypes_desc',
                        'type' => 'textfield',
                        'lexicon' => 'core:source',
                        'value' => 'jpg,jpeg,png,gif',
                    ),
                    'thumbnailType' => array(
                        'name' => 'thumbnailType',
                        'desc' => 'prop_file.thumbnailType_desc',
                        'type' => 'list',
                        'lexicon' => 'core:source',
                        'options' => array(
                            array('text' => 'Png', 'value' => 'png'),
                            array('text' => 'Jpg', 'value' => 'jpg'),
                        ),
                        'value' => 'jpg',
                    ),
                    'thumbnails' => array(
                        'name' => 'thumbnails',
                        'desc' => 'ms2_source_thumbnails_desc',
                        'type' => 'textarea',
                        'lexicon' => 'minishop2:setting',
                        'value' => '{"small":{"w":120,"h":90,"q":90,"zc":"1","bg":"000000"}}',
                    ),
                    'maxUploadWidth' => array(
                        'name' => 'maxUploadWidth',
                        'desc' => 'ms2_source_maxUploadWidth_desc',
                        'type' => 'numberfield',
                        'lexicon' => 'minishop2:setting',
                        'value' => 1920,
                    ),
                    'maxUploadHeight' => array(
                        'name' => 'maxUploadHeight',
                        'desc' => 'ms2_source_maxUploadHeight_desc',
                        'type' => 'numberfield',
                        'lexicon' => 'minishop2:setting',
                        'value' => 1080,
                    ),
                    'maxUploadSize' => array(
                        'name' => 'maxUploadSize',
                        'desc' => 'ms2_source_maxUploadSize_desc',
                        'type' => 'numberfield',
                        'lexicon' => 'minishop2:setting',
                        'value' => 10485760,
                    ),
                    'imageNameType' => array(
                        'name' => 'imageNameType',
                        'desc' => 'ms2_source_imageNameType_desc',
                        'type' => 'list',
                        'lexicon' => 'minishop2:setting',
                        'options' => array(
                            array('text' => 'Hash', 'value' => 'hash'),
                            array('text' => 'Friendly', 'value' => 'friendly'),
                        ),
                        'value' => 'friendly',
                    ),
                ),
                'is_stream' => 1,
            );
            /** @var $source modMediaSource */
            if (!$source = $modx->getObject('sources.modMediaSource', array('name' => $properties['name']))) {
                $source = $modx->newObject('sources.modMediaSource', $properties);
            } else {
                $default = $source->get('properties');
                foreach ($properties['properties'] as $k => $v) {
                    if (!array_key_exists($k, $default)) {
                        $default[$k] = $v;
                    }
                }
                $source->set('properties', $default);
            }
            $source->save();

            if ($setting = $modx->getObject('modSystemSetting', array('key' => 'ms2_product_source_default'))) {
                if (!$setting->get('value')) {
                    $setting->set('value', $source->get('id'));
                    $setting->save();
                }
            }
            @mkdir(MODX_ASSETS_PATH . 'images/');
            @mkdir(MODX_ASSETS_PATH . 'images/products/');
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;