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

            $statuses = [
                [
                    'name' => !$lang ? 'Новый' : 'New',
                    'color' => '000000',
                    'email_user' => 1,
                    'email_manager' => 1,
                    'subject_user' => '[[%ms2_email_subject_new_user]]',
                    'subject_manager' => '[[%ms2_email_subject_new_manager]]',
                    'body_user' => 'tpl.msEmail.new.user',
                    'body_manager' => 'tpl.msEmail.new.manager',
                    'final' => 0,
                    'fixed' => 1,
                    'rank' => 1,
                    'id' => 1
                ],
                [
                    'name' => !$lang ? 'Оплачен' : 'Paid',
                    'color' => '008000',
                    'email_user' => 1,
                    'email_manager' => 1,
                    'subject_user' => '[[%ms2_email_subject_paid_user]]',
                    'subject_manager' => '[[%ms2_email_subject_paid_manager]]',
                    'body_user' => 'tpl.msEmail.paid.user',
                    'body_manager' => 'tpl.msEmail.paid.manager',
                    'final' => 0,
                    'fixed' => 1,
                    'rank' => 2,
                    'id' => 2
                ],
                [
                    'name' => !$lang ? 'Отправлен' : 'Sent',
                    'color' => '003366',
                    'email_user' => 1,
                    'email_manager' => 0,
                    'subject_user' => '[[%ms2_email_subject_sent_user]]',
                    'subject_manager' => '',
                    'body_user' => 'tpl.msEmail.sent.user',
                    'body_manager' => '',
                    'final' => 1,
                    'fixed' => 1,
                    'rank' => 3,
                    'id' => 3
                ],
                [
                    'name' => !$lang ? 'Отменён' : 'Cancelled',
                    'color' => '800000',
                    'email_user' => 1,
                    'email_manager' => 0,
                    'subject_user' => '[[%ms2_email_subject_cancelled_user]]',
                    'subject_manager' => '',
                    'body_user' => 'tpl.msEmail.cancelled.user',
                    'body_manager' => '',
                    'final' => 1,
                    'fixed' => 1,
                    'rank' => 4,
                    'id' => 4
                ],
                [
                    'name' => !$lang ? 'Черновик' : 'Draft',
                    'color' => 'C0C0C0',
                    'email_user' => 0,
                    'email_manager' => 0,
                    'subject_user' => '',
                    'subject_manager' => '',
                    'body_user' => '',
                    'body_manager' => '',
                    'final' => 0,
                    'fixed' => 0,
                    'rank' => 0,
                    'id' => 999
                ],
            ];

            foreach ($statuses as $properties) {
                $id = $properties['id'];
                unset($properties['id']);

                $status = $modx->getObject('msOrderStatus', [
                    'id' => $id,
                    'OR:name:=' => $properties['name']
                ]);
                if (!$status) {
                    $status = $modx->newObject(
                        'msOrderStatus',
                        array_merge([
                            'editable' => 0,
                            'active' => 1,
                        ], $properties)
                    );
                    /*@var modChunk $chunk */
                    if (!empty($properties['body_user'])) {
                        $chunk = $modx->getObject('modChunk', ['name' => $properties['body_user']]);
                        if ($chunk) {
                            $status->set('body_user', $chunk->get('id'));
                        }
                    }
                    if (!empty($properties['body_manager'])) {
                        $chunk = $modx->getObject('modChunk', ['name' => $properties['body_manager']]);
                        if ($chunk) {
                            $status->set('body_manager', $chunk->get('id'));
                        }
                    }
                } elseif ($id === 999) {
                    $status->set('name', $properties['name']);
                }
                $status->save();

                $status_id = $status->get('id');
                $status_name = $properties['name'];
                $key = '';
                switch ($status_name) {
                    case 'Новый':
                    case 'New':
                        $key = 'ms2_status_new';
                        break;
                    case 'Оплачен':
                    case 'Paid':
                        $key = 'ms2_status_paid';
                        break;
                    case 'Отменен':
                    case 'cancelled':
                        $key = 'ms2_status_canceled';
                        break;
                    case 'Черновик':
                    case 'Draft':
                        $key = 'ms2_status_draft';
                        break;
                }
                if (empty($key)) {
                    continue;
                }

                $setting = $modx->getObject('modSystemSetting', ['key' => $key]);
                if ($setting) {
                    $value = $setting->get('value');
                    if (empty($value)) {
                        $setting->set('value', $status_id);
                        $setting->save();
                    }
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modx->removeCollection('msOrderStatus', []);
            break;
    }
}
return true;
