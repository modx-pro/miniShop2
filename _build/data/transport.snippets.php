<?php
/**
 * Add snippets to build
 * 
 * @package minishop2
 * @subpackage build
 */
$snippets = array();

$snippets['msGetProducts']= $modx->newObject('modSnippet');
$snippets['msGetProducts']->fromArray(array(
	'id' => 0
	,'name' => 'msGetProducts'
	,'description' => 'Snippet for fast retrieving miniShop2 goods'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.get_products.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.get_products.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.get_products.php';
$snippets['msGetProducts']->setProperties($properties);
unset($properties);

return $snippets;