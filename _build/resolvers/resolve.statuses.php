<?php
/**
 * Resolve creating needed statuses
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
			$lang = $modx->getOption('manager_language') == 'en' ? 1 : 0;

			$statuses = array(
				'1' => array(
					'name' => !$lang ? 'Новый' : 'New'
					,'color' => '000000'
					,'email_user' => 1
					,'email_manager' => 1
					,'subject_user' => !$lang ? 'Вы сделали заказ #[[+num]] на сайте [[++sitename]]' : 'You made the order #[[+num]] on the [[++sitename]]'
					,'subject_manager' => !$lang ? 'У вас новый заказ #[[+num]]' : 'You have a new order #[[+num]]'
					,'body_user' => 'tpl.msEmail.new.user'
					,'body_manager' => 'tpl.msEmail.new.manager'
					,'final' => 0
				)
				,'2' => array(
					'name' => !$lang ? 'Оплачен' : 'Paid'
					,'color' => '008000'
					,'email_user' => 1
					,'email_manager' => 1
					,'subject_user' => !$lang ? 'Вы оплатили заказ #[[+num]]' : 'You have paid for the order #[[+num]]'
					,'subject_manager' => !$lang ? 'Заказ #[[+num]] был оплачен' : 'Order #[[+num]] was paid'
					,'body_user' => 'tpl.msEmail.paid.user'
					,'body_manager' => 'tpl.msEmail.paid.manager'
					,'final' => 0
				)
				,'3' => array(
					'name' => !$lang ? 'Отправлен' : 'Sent'
					,'color' => '003366'
					,'email_user' => 1
					,'email_manager' => 0
					,'subject_user' => !$lang ? 'Ваш заказ #[[+num]] был отправлен' : 'Your order #[[+num]] was sent'
					,'subject_manager' => ''
					,'body_user' => 'tpl.msEmail.sent.user'
					,'body_manager' => ''
					,'final' => 1
				)
				,'4' => array(
					'name' => !$lang ? 'Отменён' : 'Cancelled'
					,'color' => '800000'
					,'email_user' => 1
					,'email_manager' => 0
					,'subject_user' => !$lang ? 'Ваш заказ #[[+num]] был отменён' : 'Your order #[[+num]] was cancelled'
					,'subject_manager' => ''
					,'body_user' => 'tpl.msEmail.cancelled.user'
					,'body_manager' => ''
					,'final' => 1
				)
			);

			foreach ($statuses as $id => $properties) {
				if (!$status = $modx->getCount('msOrderStatus', array('id' => $id))) {
					$status = $modx->newObject('msOrderStatus', array_merge(array(
						'editable' => 0
						,'active' => 1
						,'rank' => $id - 1
						,'fixed' => 1
					), $properties));
					$status->set('id', $id);
					/* @var modChunk $chunk */
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
			break;
		case xPDOTransport::ACTION_UNINSTALL:
			if ($modx instanceof modX) {
				$modx->removeExtensionPackage('minishop2');
			}
			break;
	}
}
return true;