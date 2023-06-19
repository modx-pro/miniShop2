<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;
    // fix for removing installed old incorrect menu
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $menu = $modx->getObject('modMenu', ['text' => 'ms2_utilites']);
            if ($menu) {
                $menu->remove();
                $modx->log(modX::LOG_LEVEL_ERROR, "Removed menu item \"ms2_utilites\"");
                unlink(MODX_ASSETS_PATH . 'components/minishop2/js/mgr/utilites/gallery/panel.js');
                rmdir(MODX_ASSETS_PATH . 'components/minishop2/js/mgr/utilites/gallery/');
                unlink(MODX_ASSETS_PATH . 'components/minishop2/js/mgr/utilites/panel.js');
                rmdir(MODX_ASSETS_PATH . 'components/minishop2/js/mgr/utilites/');
                $modx->log(modX::LOG_LEVEL_ERROR, "Removed all files in \"utilites\"");
            }
            break;
    }
}
return true;
