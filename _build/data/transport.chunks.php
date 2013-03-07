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
	'static_file' => 'minishop2/elements/chunks/chunk.ms_products_row.tpl'
),'',true,true);

$chunks['tpl.msCart.row']= $modx->newObject('modChunk');
$chunks['tpl.msCart.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msCart.row',
	'description' => 'Chunk for one item in shopping cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_cart_row.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_cart_row.tpl'
),'',true,true);

$chunks['tpl.msCart.outer']= $modx->newObject('modChunk');
$chunks['tpl.msCart.outer']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msCart.outer',
	'description' => 'Wrapper chunk for shopping cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_cart_outer.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_cart_outer.tpl'
),'',true,true);

$chunks['tpl.msCart.empty']= $modx->newObject('modChunk');
$chunks['tpl.msCart.empty']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msCart.empty',
	'description' => 'Chunk for empty shopping cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_cart_empty.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_cart_empty.tpl'
),'',true,true);

$chunks['tpl.msMiniCart']= $modx->newObject('modChunk');
$chunks['tpl.msMiniCart']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msMiniCart',
	'description' => 'Chunk for mini cart.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_minicart.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_minicart.tpl'
),'',true,true);

$chunks['msProduct.content']= $modx->newObject('modChunk');
$chunks['msProduct.content']->fromArray(array(
	'id' => 0,
	'name' => 'msProduct.content',
	'description' => 'Chunk for displaying card of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_product_content.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_product_content.tpl'
),'',true,true);

$chunks['tpl.msGallery.row']= $modx->newObject('modChunk');
$chunks['tpl.msGallery.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msGallery.row',
	'description' => 'Chunk for displaying one image of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_gallery_row.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_gallery_row.tpl'
),'',true,true);

$chunks['tpl.msGallery.outer']= $modx->newObject('modChunk');
$chunks['tpl.msGallery.outer']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msGallery.outer',
	'description' => 'Wrapper chunk for product gallery.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_gallery_outer.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_gallery_outer.tpl'
),'',true,true);

$chunks['tpl.msGallery.empty']= $modx->newObject('modChunk');
$chunks['tpl.msGallery.empty']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msGallery.empty',
	'description' => 'Chunk for empty gallery of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_gallery_empty.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_gallery_empty.tpl'
),'',true,true);

$chunks['tpl.msOptions.row']= $modx->newObject('modChunk');
$chunks['tpl.msOptions.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOptions.row',
	'description' => 'Chunk for one product option.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_options_row.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_options_row.tpl'
),'',true,true);

$chunks['tpl.msOptions.outer']= $modx->newObject('modChunk');
$chunks['tpl.msOptions.outer']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOptions.outer',
	'description' => 'Wrapper chunk for options of the product.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_options_outer.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_options_outer.tpl'
),'',true,true);

$chunks['tpl.msEmail.new.user']= $modx->newObject('modChunk');
$chunks['tpl.msEmail.new.user']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msEmail.new.user',
	'description' => '',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_email_new_user.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_email_new_user.tpl'
),'',true,true);

$chunks['tpl.msEmail.new.manager']= $modx->newObject('modChunk');
$chunks['tpl.msEmail.new.manager']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msEmail.new.manager',
	'description' => '',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_email_new_manager.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_email_new_manager.tpl'
),'',true,true);

$chunks['tpl.msEmail.paid.user']= $modx->newObject('modChunk');
$chunks['tpl.msEmail.paid.user']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msEmail.paid.user',
	'description' => '',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_email_paid_user.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_email_paid_user.tpl'
),'',true,true);

$chunks['tpl.msEmail.paid.manager']= $modx->newObject('modChunk');
$chunks['tpl.msEmail.paid.manager']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msEmail.paid.manager',
	'description' => '',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_email_paid_manager.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_email_paid_manager.tpl'
),'',true,true);

$chunks['tpl.msEmail.sent.user']= $modx->newObject('modChunk');
$chunks['tpl.msEmail.sent.user']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msEmail.sent.user',
	'description' => '',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_email_sent_user.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_email_sent_user.tpl'
),'',true,true);

$chunks['tpl.msEmail.cancelled.user']= $modx->newObject('modChunk');
$chunks['tpl.msEmail.cancelled.user']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msEmail.cancelled.user',
	'description' => '',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_email_cancelled_user.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_email_cancelled_user.tpl'
),'',true,true);

$chunks['tpl.msOrder.outer']= $modx->newObject('modChunk');
$chunks['tpl.msOrder.outer']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOrder.outer',
	'description' => 'Wrapper for template ordering form.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_order_outer.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_order_outer.tpl'
),'',true,true);

$chunks['tpl.msOrder.payment']= $modx->newObject('modChunk');
$chunks['tpl.msOrder.payment']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOrder.payment',
	'description' => 'Chunk to process a one payment method',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_order_payment.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_order_payment.tpl'
),'',true,true);

$chunks['tpl.msOrder.delivery']= $modx->newObject('modChunk');
$chunks['tpl.msOrder.delivery']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOrder.delivery',
	'description' => 'Chunk to process a one delivery method',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_order_delivery.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_order_delivery.tpl'
),'',true,true);

$chunks['tpl.msOrder.success']= $modx->newObject('modChunk');
$chunks['tpl.msOrder.success']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msOrder.success',
	'description' => 'Chunk with message about successfull order',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_order_success.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_order_success.tpl'
),'',true,true);

$chunks['tpl.msGetOrder.row']= $modx->newObject('modChunk');
$chunks['tpl.msGetOrder.row']->fromArray(array(
	'id' => 0,
	'name' => 'tpl.msGetOrder.row',
	'description' => 'Chunk for templating one row of ordered product',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.ms_get_order_row.tpl'),
	'static_file' => 'minishop2/elements/chunks/chunk.ms_get_order_row.tpl'
),'',true,true);

/*
foreach ($chunks as $key => $chunk) {
	$chunks[$key]->set('static', 1);
}
*/

return $chunks;