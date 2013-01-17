<?php
/**
 * Package in plugins
 *
 * @package miniShop2
 * @subpackage build
 */
$plugins = array();
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('name','miniShop2');
$plugins[0]->fromArray(array(
	'id' => 0
	,'category' => 0
	,'description' =>'Main plugin for miniShop2'
	,'plugincode' => getSnippetContent($sources['plugins'] . 'plugin.minishop2.php')
	,'source' => 1
	,'static' => 1
	,'static_file' => 'core/components/minishop2/elements/plugins/plugin.minishop2.php'
));


$events[0]= $modx->newObject('modPluginEvent');
$events[0]->fromArray(array(
	'event' => 'OnManagerPageInit',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);


if (is_array($events) && !empty($events)) {
	$plugins[0]->addMany($events);
	$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' plugin events.'); flush();
} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events!');
}

unset($events);
return $plugins;