<?php
/**
 * Add chunks to build
 * 
 * @package minishop2
 * @subpackage build
 */
$chunks = array();

$chunks['tpl.msProducts.row']= $modx->newObject('modChunk');
$chunks['tpl.msProducts.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msProducts.row',
	'description' => 'Chunk for listing miniShop2 catalogue.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_products_row.tpl'),
),'',true,true);

$chunks['tpl.msCart.row']= $modx->newObject('modChunk');
$chunks['tpl.msCart.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msCart.row',
	'description' => 'Chunk for one item in shopping cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_cart_row.tpl'),
),'',true,true);

$chunks['tpl.msCart.outer']= $modx->newObject('modChunk');
$chunks['tpl.msCart.outer']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msCart.outer',
	'description' => 'Wrapper chunk for shopping cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_cart_outer.tpl'),
),'',true,true);

$chunks['tpl.msCart.empty']= $modx->newObject('modChunk');
$chunks['tpl.msCart.empty']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msCart.empty',
	'description' => 'Chunk for empty shopping cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_cart_empty.tpl'),
),'',true,true);

$chunks['tpl.msMiniCart']= $modx->newObject('modChunk');
$chunks['tpl.msMiniCart']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msMiniCart',
	'description' => 'Chunk for mini cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_minicart.tpl'),
),'',true,true);

$chunks['msProduct.content']= $modx->newObject('modChunk');
$chunks['msProduct.content']->fromArray(array(
	'id' => 0,
	'name' => 'msProduct.content',
	'description' => 'Chunk for displaying card of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_product_content.tpl'),
),'',true,true);

$chunks['tpl.msGallery.row']= $modx->newObject('modChunk');
$chunks['tpl.msGallery.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msGallery.row',
	'description' => 'Chunk for displaying one image of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_gallery_row.tpl'),
),'',true,true);

$chunks['tpl.msGallery.outer']= $modx->newObject('modChunk');
$chunks['tpl.msGallery.outer']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msGallery.outer',
	'description' => 'Wrapper chunk for product gallery.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_gallery_outer.tpl'),
),'',true,true);

$chunks['tpl.msGallery.empty']= $modx->newObject('modChunk');
$chunks['tpl.msGallery.empty']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msGallery.empty',
	'description' => 'Chunk for empty gallery of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_gallery_empty.tpl'),
),'',true,true);


$chunks['tpl.msOptions.row']= $modx->newObject('modChunk');
$chunks['tpl.msOptions.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOptions.row',
	'description' => 'Chunk for one product option.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_options_row.tpl'),
),'',true,true);

$chunks['tpl.msOptions.outer']= $modx->newObject('modChunk');
$chunks['tpl.msOptions.outer']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOptions.outer',
	'description' => 'Wrapper chunk for options of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_options_outer.tpl'),
),'',true,true);


return $chunks;