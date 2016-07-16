<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            break;

        case xPDOTransport::ACTION_UPGRADE:
            /** @var modSystemSetting $setting */
            if ($setting = $modx->getObject('modSystemSetting', 'ms2_product_main_fields')) {
                $fields1 = array_map('trim', explode(',', $setting->get('value')));
                $fields2 = array_keys($modx->getFieldMeta('modResource'));
                if ($fields = array_diff($fields1, $fields2)) {
                    if ($setting = $modx->getObject('modSystemSetting', 'ms2_product_extra_fields')) {
                        $tmp = array_map('trim', explode(',', $setting->get('value')));
                        $value = array_unique(array_merge($fields, $tmp));
                        $setting->set('value', implode(',', $value));
                        $setting->save();
                    }
                }
            }

            $modx->removeCollection('modSystemSetting', array(
                'key:IN' => array(
                    'ms2_product_vertical_tabs',
                    'ms2_category_remember_grid',
                    'ms2_product_main_fields',
                ),
            ));
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            /*
            $c = $modx->newQuery('modResource');
            $c->command('UPDATE');
            $c->set(array(
                'class_key' => 'modDocument',
                'show_in_tree' => true,
            ));
            $c->where(array('class_key:IN' => array('msCategory', 'msProduct')));
            if ($c->prepare()) {
                $c->stmt->execute();
            }
            */
            break;
    }
}
return true;