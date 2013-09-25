<?php

if (empty($_REQUEST['action']) && empty($_REQUEST['ms2_action'])) {
	die('Access denied');
}

require dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';