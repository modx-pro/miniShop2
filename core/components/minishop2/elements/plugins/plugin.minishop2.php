<?php
switch($modx->event->name) {

	case 'OnManagerPageInit':
		$cssFile = $modx->getOption('minishop2.assets_url',null,$modx->getOption('assets_url').'components/minishop2/').'css/mgr/main.css';
		$modx->regClientCSS($cssFile);
	break;

}