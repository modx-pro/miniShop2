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
            $lang = $modx->getOption('manager_language') == 'en' ? 1 : 0;

            $statuses = array(
                1 => array(
                    'name' => !$lang ? 'Новый' : 'New',
                    'color' => '000000',
                    'email_user' => 1,
                    'email_manager' => 1,
                    'subject_user' => '[[%ms2_email_subject_new_user]]',
                    'subject_manager' => '[[%ms2_email_subject_new_manager]]',
                    'body_user' => 'tpl.msEmail.new.user',
                    'body_manager' => 'tpl.msEmail.new.manager',
                    'final' => 0,
                ),
                2 => array(
                    'name' => !$lang ? 'Оплачен' : 'Paid',
                    'color' => '008000',
                    'email_user' => 1,
                    'email_manager' => 1,
                    'subject_user' => '[[%ms2_email_subject_paid_user]]',
                    'subject_manager' => '[[%ms2_email_subject_paid_manager]]',
                    'body_user' => 'tpl.msEmail.paid.user',
                    'body_manager' => 'tpl.msEmail.paid.manager',
                    'final' => 0,
                ),
                3 => array(
                    'name' => !$lang ? 'Отправлен' : 'Sent',
                    'color' => '003366',
                    'email_user' => 1,
                    'email_manager' => 0,
                    'subject_user' => '[[%ms2_email_subject_sent_user]]',
                    'subject_manager' => '',
                    'body_user' => 'tpl.msEmail.sent.user',
                    'body_manager' => '',
                    'final' => 1,
                ),
                4 => array(
                    'name' => !$lang ? 'Отменён' : 'Cancelled',
                    'color' => '800000',
                    'email_user' => 1,
                    'email_manager' => 0,
                    'subject_user' => '[[%ms2_email_subject_cancelled_user]]',
                    'subject_manager' => '',
                    'body_user' => 'tpl.msEmail.cancelled.user',
                    'body_manager' => '',
                    'final' => 1,
                ),
            );

            foreach ($statuses as $id => $properties) {
                if (!$status = $modx->getCount('msOrderStatus', array('id' => $id))) {
                    $status = $modx->newObject('msOrderStatus', array_merge(array(
                        'editable' => 0,
                        'active' => 1,
                        'rank' => $id - 1,
                        'fixed' => 1,
                    ), $properties));
                    $status->set('id', $id);
                    /*@var modChunk $chunk */
                    if (!empty($properties['body_user'])) {
                        if ($chunk = $modx->getObject('modChunk', array('name' => $properties['body_user']))) {
                            $status->set('body_user', $chunk->get('id'));
                        }
                    }
                    if (!empty($properties['body_manager'])) {
                        if ($chunk = $modx->getObject('modChunk', array('name' => $properties['body_manager']))) {
                            $status->set('body_manager', $chunk->get('id'));
                        }
                    }
                    $status->save();
                }
            }

            /** @var msDelivery $delivery */
            if (!$delivery = $modx->getObject('msDelivery', 1)) {
                $delivery = $modx->newObject('msDelivery');
                $delivery->fromArray(array(
                    'id' => 1,
                    'name' => !$lang ? 'Самовывоз' : 'Self-delivery',
                    'price' => 0,
                    'weight_price' => 0,
                    'distance_price' => 0,
                    'active' => 1,
                    'requires' => 'email,receiver',
                    'rank' => 0,
                ), '', true);
                $delivery->save();
            }

            /** @var msPayment $payment */
            if (!$payment = $modx->getObject('msPayment', 1)) {
                $payment = $modx->newObject('msPayment');
                $payment->fromArray(array(
                    'id' => 1,
                    'name' => !$lang ? 'Оплата наличными' : 'Cash',
                    'active' => 1,
                    'rank' => 0,
                ), '', true);
                $payment->save();
            }

            /** @var msPayment $payment */
            if (!$payment = $modx->getObject('msPayment', 2)) {
                $payment = $modx->newObject('msPayment');
                $payment->fromArray(array(
                    'id' => 2,
                    'name' => 'PayPal',
                    'active' => $lang,
                    'class' => 'PayPal',
                    'rank' => 1,
                ), '', true);
                $payment->save();
            }

            /** @var msDeliveryMember $member */
            if (!$member = $modx->getObject('msDeliveryMember', array('payment_id' => 1, 'delivery_id' => 1))) {
                $member = $modx->newObject('msDeliveryMember');
                $member->fromArray(array(
                    'payment_id' => 1,
                    'delivery_id' => 1,
                ), '', true);
                $member->save();
            }

            if ($setting = $modx->getObject('modSystemSetting', array('key' => 'ms2_order_product_fields'))) {
                $value = $setting->get('value');
                if (strpos($value, 'product_pagetitle') !== false) {
                    $value = str_replace('product_pagetitle', 'name', $value);
                    $setting->set('value', $value);
                    $setting->save();
                }
            }

            $old_settings = array(
                'ms2_category_remember_grid',
                'ms2_product_thumbnail_size',
            );
            foreach ($old_settings as $key) {
                if ($item = $modx->getObject('modSystemSetting', $key)) {
                    $item->remove();
                }
            }

            /** @var modSystemSetting $setting */
            if ($setting = $modx->getObject('modSystemSetting', array('key' => 'ms2_chunks_categories'))) {
                if (!$setting->get('editedon')) {
                    /** @var modCategory $category */
                    if ($category = $modx->getObject('modCategory', array('category' => 'miniShop2'))) {
                        $setting->set('value', $category->get('id'));
                        $setting->save();
                    }
                }
            }

            if ($setting = $modx->getObject('modSystemSetting', array('key' => 'ms2_order_address_fields'))) {
                $fields = explode(',', $setting->get('value'));
                $fields = array_unique(array_merge($fields, ['entrance', 'floor', 'text_address']));
                $setting->set('value', implode(',', $fields));
                $setting->save();
            }

            $chunks_descriptions = array(
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
            );

            foreach ($chunks_descriptions as $name => $description) {
                /** @var modChunk $chunk */
                if ($chunk = $modx->getObject('modChunk', array('name' => $name))) {
                    if (!$chunk->get('locked') && empty($chunk->get('description'))) {
                        $chunk->set('description', $description);
                        $chunk->save();
                    }
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modx->removeCollection('modSystemSetting', array(
                'namespace' => 'minishop2',
            ));
            break;
    }
}
return true;
