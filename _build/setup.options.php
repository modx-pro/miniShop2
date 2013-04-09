<?php
/**
 * Build the setup options form.
 */
$exists = false;
$output = null;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:

	case xPDOTransport::ACTION_UPGRADE:
		$exists = $modx->getObject('transport.modTransportPackage', array('package_name' => 'pdoTools'));
		break;

	case xPDOTransport::ACTION_UNINSTALL: break;
}

if (!$exists) {
	switch ($modx->getOption('manager_language')) {
		case 'ru':
			$output = 'Этот компонент требует <b>pdoTools</b> для быстрой работы сниппетов.<br/><br/>Могу я автоматически скачать и установить его?';
			break;
		default:
			$output = 'This component requires <b>pdoTools</b> for fast work of snippets.<br/><br/>Can i automaticly download and install it?';
	}

}

return $output;