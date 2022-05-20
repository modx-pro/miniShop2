<?php

if (!empty($this->modx->getOption('log_deprecated'))) {
    $this->modx->log(
        xPDO::LOG_LEVEL_ERROR,
        'Deprecated: use handlers from catalog core/components/minishop2/handlers/'
    );
}
require_once dirname(__FILE__, 3) . '/handlers/mscarthandler.class.php';
