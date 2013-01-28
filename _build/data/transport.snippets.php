<?php
/**
 * Add snippets to build
 * 
 * @package minishop2
 * @subpackage build
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
	'id' => 0
	,'name' => 'miniShop2'
	,'description' => 'Main miniShop2 snippet'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.minishop2.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.minishop2.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.minishop2.php';
$snippets[0]->setProperties($properties);
unset($properties);

return $snippets;