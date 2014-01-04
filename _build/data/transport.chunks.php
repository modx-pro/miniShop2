<?php

$chunks = array();

$tmp = array(
	'tpl.msProducts.row' => 'ms_products_row'
	,'tpl.msCart.row' => 'ms_cart_row'
	,'tpl.msCart.outer' => 'ms_cart_outer'
	,'tpl.msCart.empty' => 'ms_cart_empty'
	,'tpl.msMiniCart' => 'ms_minicart'
	,'msProduct.content' => 'ms_product_content'
	,'tpl.msGallery.row' => 'ms_gallery_row'
	,'tpl.msGallery.outer' => 'ms_gallery_outer'
	,'tpl.msGallery.empty' => 'ms_gallery_empty'
	,'tpl.msOptions.row' => 'ms_options_row'
	,'tpl.msOptions.outer' => 'ms_options_outer'
	,'tpl.msEmail.new.user' => 'ms_email_new_user'
	,'tpl.msEmail.new.manager' => 'ms_email_new_manager'
	,'tpl.msEmail.paid.user' => 'ms_email_paid_user'
	,'tpl.msEmail.paid.manager' => 'ms_email_paid_manager'
	,'tpl.msEmail.sent.user' => 'ms_email_sent_user'
	,'tpl.msEmail.cancelled.user' => 'ms_email_cancelled_user'
	,'tpl.msOrder.outer' => 'ms_order_outer'
	,'tpl.msOrder.payment' => 'ms_order_payment'
	,'tpl.msOrder.delivery' => 'ms_order_delivery'
	,'tpl.msOrder.success' => 'ms_order_success'
	,'tpl.msGetOrder.row' => 'ms_get_order_row'
);

// Save chunks for setup options
$BUILD_CHUNKS = array();

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'id' => 0,
		'name' => $k,
		'description' => '',
		'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v.'.tpl'),
		'static' => BUILD_CHUNK_STATIC,
		'source' => 1,
		'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v.'.tpl',
	),'',true,true);
	$chunks[] = $chunk;

	$BUILD_CHUNKS[$k] = file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v.'.tpl');
}

ksort($BUILD_CHUNKS);
return $chunks;