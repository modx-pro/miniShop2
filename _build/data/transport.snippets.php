<?php
/**
 * Add snippets to build
 * 
 * @package minishop2
 * @subpackage build
 */
$snippets = array();

$snippets['msProducts']= $modx->newObject('modSnippet');
$snippets['msProducts']->fromArray(array(
	'id' => 0
	,'name' => 'msProducts'
	,'description' => 'Snippet for fast retrieving miniShop2 goods'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.ms_products.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.ms_products.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.ms_products.php';
$snippets['msProducts']->setProperties($properties);
unset($properties);

$snippets['msCart']= $modx->newObject('modSnippet');
$snippets['msCart']->fromArray(array(
	'id' => 0
	,'name' => 'msCart'
	,'description' => 'Snippet for work with shopping cart'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.ms_cart.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.ms_cart.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.ms_cart.php';
$snippets['msCart']->setProperties($properties);
unset($properties);

$snippets['msMiniCart']= $modx->newObject('modSnippet');
$snippets['msMiniCart']->fromArray(array(
	'id' => 0
	,'name' => 'msMiniCart'
	,'description' => 'Simple snippet for displaying cart status'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.ms_minicart.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.ms_minicart.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.ms_minicart.php';
$snippets['msMiniCart']->setProperties($properties);
unset($properties);

$snippets['msGallery']= $modx->newObject('modSnippet');
$snippets['msGallery']->fromArray(array(
	'id' => 0
	,'name' => 'msGallery'
	,'description' => 'Simple snippet for displaying cart status'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.ms_gallery.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.ms_gallery.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.ms_gallery.php';
$snippets['msGallery']->setProperties($properties);
unset($properties);

$snippets['msOptions']= $modx->newObject('modSnippet');
$snippets['msOptions']->fromArray(array(
	'id' => 0
	,'name' => 'msOptions'
	,'description' => 'Simple snippet for displaying cart status'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.ms_options.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.ms_options.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.ms_options.php';
$snippets['msOptions']->setProperties($properties);
unset($properties);

$snippets['msOrder']= $modx->newObject('modSnippet');
$snippets['msOrder']->fromArray(array(
	'id' => 0
	,'name' => 'msOrder'
	,'description' => 'Snippet for ordering of goods'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.ms_order.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.ms_order.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.ms_order.php';
$snippets['msOrder']->setProperties($properties);
unset($properties);


$snippets['msGetOrder']= $modx->newObject('modSnippet');
$snippets['msGetOrder']->fromArray(array(
	'id' => 0
	,'name' => 'msGetOrder'
	,'description' => 'Snippet for retrieving users orders'
	,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.ms_get_order.php')
	,'static' => 1
	,'static_file' => 'minishop2/elements/snippets/snippet.ms_get_order.php'
),'',true,true);
$properties = include $sources['build'].'properties/properties.ms_get_order.php';
$snippets['msGetOrder']->setProperties($properties);
unset($properties);


return $snippets;
