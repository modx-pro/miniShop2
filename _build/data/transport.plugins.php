<?php
/**
 * Package in plugins
 *
 * @package miniShop2
 * @subpackage build
 */
$plugins = array();
$plugins['miniShop2'] = $modx->newObject('modPlugin');
$plugins['miniShop2']->set('name','miniShop2');
$plugins['miniShop2']->fromArray(array(
	'id' => 0
	,'category' => 0
	,'description' =>'Main plugin for miniShop2'
	,'plugincode' => getSnippetContent($sources['plugins'] . 'plugin.minishop2.php')
	//,'static' => 1
	//,'static_file' => 'minishop2/elements/plugins/plugin.minishop2.php'
));


$events['miniShop2']= $modx->newObject('modPluginEvent');
$events['miniShop2']->fromArray(array(
	'event' => 'OnManagerPageInit',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);


if (is_array($events) && !empty($events)) {
	$plugins['miniShop2']->addMany($events);
	//$modx->log(xPDO::LOG_LEVEL_INFO,'Added '.count($events).' events to plugin miniShop2.'); flush();
}

unset($events);
return $plugins;