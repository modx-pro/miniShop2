<?php

if (empty($_REQUEST['action']) && empty($_REQUEST['ms2_action'])) {
    http_response_code(403);
}

if (!empty($_REQUEST['action'])) {
    $_REQUEST['ms2_action'] = $_REQUEST['action'];
}

/** @noinspection PhpIncludeInspection */
require dirname(__FILE__, 4) . '/index.php';
