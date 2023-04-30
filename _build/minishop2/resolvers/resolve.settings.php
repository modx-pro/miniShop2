<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption(
                    'minishop2.core_path',
                    null,
                    $modx->getOption('core_path') . 'components/minishop2/'
                ) . 'model/';
            $modx->addPackage('minishop2', $modelPath);
            $lang = $modx->getOption('manager_language') === 'en' ? 1 : 0;

            /** @var msDelivery $delivery */
            $delivery = $modx->getObject('msDelivery', 1);
            if (!$delivery) {
                $delivery = $modx->newObject('msDelivery');
                $delivery->fromArray([
                    'id' => 1,
                    'name' => !$lang ? 'Самовывоз' : 'Self-delivery',
                    'price' => 0,
                    'weight_price' => 0,
                    'distance_price' => 0,
                    'active' => 1,
                    'requires' => 'email,receiver',
                    'rank' => 0,
                ], '', true);
                $delivery->save();
            }

            /** @var msPayment $payment */
            $payment = $modx->getObject('msPayment', 1);
            if (!$payment) {
                $payment = $modx->newObject('msPayment');
                $payment->fromArray([
                    'id' => 1,
                    'name' => !$lang ? 'Оплата наличными' : 'Cash',
                    'active' => 1,
                    'rank' => 0,
                ], '', true);
                $payment->save();
            }

            /** @var msDeliveryMember $member */
            $member = $modx->getObject('msDeliveryMember', ['payment_id' => 1, 'delivery_id' => 1]);
            if (!$member) {
                $member = $modx->newObject('msDeliveryMember');
                $member->fromArray([
                    'payment_id' => 1,
                    'delivery_id' => 1,
                ], '', true);
                $member->save();
            }

            $setting = $modx->getObject('modSystemSetting', ['key' => 'ms2_order_product_fields']);
            if ($setting) {
                $value = $setting->get('value');
                if (strpos($value, 'product_pagetitle') !== false) {
                    $value = str_replace('product_pagetitle', 'name', $value);
                    $setting->set('value', $value);
                    $setting->save();
                }
            }

            /** @var modSystemSetting $setting */
            $setting = $modx->getObject('modSystemSetting', ['key' => 'ms2_chunks_categories']);
            if ($setting) {
                if (!$setting->get('editedon')) {
                    /** @var modCategory $category */
                    if ($category = $modx->getObject('modCategory', ['category' => 'miniShop2'])) {
                        $setting->set('value', $category->get('id'));
                        $setting->save();
                    }
                }
            }

            $setting = $modx->getObject('modSystemSetting', ['key' => 'ms2_order_address_fields']);
            if ($setting) {
                $fields = explode(',', $setting->get('value'));
                $fields = array_unique(array_merge($fields, ['entrance', 'floor', 'text_address']));
                $setting->set('value', implode(',', $fields));
                $setting->save();
            }

            $chunks_descriptions = [
                'msProduct.content' => !$lang ? 'Чанк вывода карточки товара.' : 'Chunk for displaying card of miniShop2 product.',
                'tpl.msProducts.row' => !$lang ? 'Чанк товара miniShop2.' : 'Chunk for listing miniShop2 catalog.',

                'tpl.msCart' => !$lang ? 'Чанк вывода корзины miniShop2.' : 'Chunk for miniShop2 cart.',
                'tpl.msMiniCart' => !$lang ? 'Чанк вывода мини корзины miniShop2.' : 'Chunk for miniShop2 mini cart.',
                'tpl.msOrder' => !$lang ? 'Чанк вывода формы оформления заказа miniShop2.' : 'Chunk for displaying order form of miniShop2.',
                'tpl.msGetOrder' => !$lang ? 'Чанк вывода заказа miniShop2.' : 'Chunk for displaying order of miniShop2.',
                'tpl.msOptions' => !$lang ? 'Чанк вывода дополнительных свойств товара miniShop2.' : 'Chunk for displaying additional product characteristics of miniShop2 product.',
                'tpl.msProductOptions' => !$lang ? 'Чанк вывода дополнительных опций товара miniShop2.' : 'Chunk for displaying additional product options of miniShop2 product.',
                'tpl.msGallery' => !$lang ? 'Чанк вывода галереи товара miniShop2.' : 'Chunk for displaying gallery of miniShop2 product.',

                'tpl.msEmail' => !$lang ? 'Базовый чанк оформления писем miniShop2.' : 'Basic mail chunk of miniShop2 mail.',
                'tpl.msEmail.new.user' => !$lang ? 'Чанк письма нового заказа пользователю.' : 'User new order mail chunk.',
                'tpl.msEmail.new.manager' => !$lang ? 'Чанк письма нового заказа менеджеру.' : 'Manager new order mail chunk.',
                'tpl.msEmail.paid.user' => !$lang ? 'Чанк письма оплаченного заказа пользователю.' : 'User paid order mail chunk.',
                'tpl.msEmail.paid.manager' => !$lang ? 'Чанк письма оплаченного заказа менеджеру.' : 'Manager paid order mail chunk.',
                'tpl.msEmail.sent.user' => !$lang ? 'Чанк письма отправленного заказа пользователю.' : 'User sent order mail chunk.',
                'tpl.msEmail.cancelled.user' => !$lang ? 'Чанк письма отмененного заказа пользователю.' : 'User cancelled order mail chunk.',
            ];

            foreach ($chunks_descriptions as $name => $description) {
                /** @var modChunk $chunk */
                if ($chunk = $modx->getObject('modChunk', ['name' => $name])) {
                    if (!$chunk->get('locked') && empty($chunk->get('description'))) {
                        $chunk->set('description', $description);
                        $chunk->save();
                    }
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modx->removeCollection('modSystemSetting', [
                'namespace' => 'minishop2',
            ]);
            break;
    }
}
return true;
