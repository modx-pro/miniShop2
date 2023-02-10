<?php

/** @var modX $modx */
$chunks = [];

$tmp = [
    'msProduct.content' => 'ms_product_content',
    'msProduct.content.fenom' => 'ms_product_content_fenom',
    'tpl.msProducts.row' => 'ms_products_row',

    'tpl.msCart' => 'ms_cart',
    'tpl.msCartNew' => 'ms_cart_new',
    'tpl.msCartProductRow' => 'ms_cart_product_row',
    'tpl.msMiniCart' => 'ms_minicart',
    'tpl.msOrder' => 'ms_order',
    'tpl.msOrderNew' => 'ms_order_new',
    'tpl.msGetOrder' => 'ms_get_order',
    'tpl.msOptions' => 'ms_options',
    'tpl.msOptionsCart' => 'ms_options_cart',
    'tpl.msProductOptions' => 'ms_product_options',
    'tpl.msGallery' => 'ms_gallery',
    'tpl.msGalleryNew' => 'ms_gallery_new',

    'tpl.msEmail' => 'ms_email',
    'tpl.msEmail.new.user' => 'ms_email_new_user',
    'tpl.msEmail.new.manager' => 'ms_email_new_manager',
    'tpl.msEmail.paid.user' => 'ms_email_paid_user',
    'tpl.msEmail.paid.manager' => 'ms_email_paid_manager',
    'tpl.msEmail.sent.user' => 'ms_email_sent_user',
    'tpl.msEmail.cancelled.user' => 'ms_email_cancelled_user',
];

foreach ($tmp as $k => $v) {
    /** @var modChunk $chunk */
    $chunk = $modx->newObject('modChunk');
    /** @var array $sources */
    $chunk->fromArray([
        'id' => 0,
        'name' => $k,
        'description' => '',
        'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/chunk.' . $v . '.tpl'),
        'static' => BUILD_CHUNK_STATIC,
        'source' => 1,
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/chunk.' . $v . '.tpl',
    ], '', true, true);
    $chunks[] = $chunk;
}

return $chunks;
