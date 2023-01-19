<?php

/** @var modX $modx */
/** @var sFileTask $task */
/** @var sTaskRun $run */
/** @var array $scriptProperties */

if (empty($scriptProperties['email']) || empty($scriptProperties['subject']) || empty($scriptProperties['body'])) {
    $run->addError('empty required fields');
    $modx->log(1, '[ms2\cli\sendEmail] empty required params');
    return false;
}

/** @var miniShop2 $ms2 */
$ms2 = $modx->getService('minishop2');
$result = $ms2->sendEmail($scriptProperties['email'], $scriptProperties['subject'], $scriptProperties['body']);
if (!$result) {
    $run->addError('email sending error');
}
