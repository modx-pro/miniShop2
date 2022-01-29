<?php

/**
 * @var MODX $modx
 */

if (file_exists(dirname(__FILE__, 6) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(__FILE__, 6) . '/config.core.php';
} else {
    require_once dirname(__FILE__, 5) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
$ms2 = $modx->getService('miniShop2');
$modx->getService('registry', 'registry.modRegistry');
$modx->registry->getRegister('minishop2', 'registry.modDbRegister');
//Выбираем нужные очереди
$queue = $modx->getObject('modDbRegisterQueue', ['name' => 'minishop2']);
if (!$queue) {
    return;
}
$q = $modx->newQuery('registry.db.modDbRegisterTopic');
$q->where(['queue' => $queue->get('id')]);
$q->limit($modx->getOption('ms2_queue_limit', '', 10));
$topics = $modx->getIterator('modDbRegisterTopic', $q);
$emails = [];
foreach ($topics as $topic) {
    $modx->registry->minishop2->subscribe($topic->name);
    $arRegistry = [
        'poll_limit' => 1,
        'msg_limit' => 10,
        'include_keys' => false
    ];
    $msgs = $modx->registry->minishop2->read($arRegistry);
    if (!$msgs) {
        continue;
    }

    $msgs = json_decode($msgs[0], true);
    $mail = $modx->getService('mail', 'mail.modPHPMailer');
    $mail->setHTML(true);

    $mail->address('to', $msgs['to']);
    $mail->set(modMail::MAIL_SUBJECT, $msgs['mail_subject']);
    $mail->set(modMail::MAIL_BODY, $msgs['mail_body']);
    $mail->set(modMail::MAIL_FROM, $msgs['mail_from']);
    $mail->set(modMail::MAIL_FROM_NAME, $msgs['mail_from_name']);
    if (!$mail->send()) {
        $ms2->queue($msgs['to'], $msgs['mail_subject'], $msgs['mail_body']);
        $modx->log(
            modX::LOG_LEVEL_ERROR,
            'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo
        );
    }
    $mail->reset();
    $topic->remove();
}
