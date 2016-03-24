<?php

$snippets = array();

$tmp = array(
    'msProducts' => 'ms_products',
    'msCart' => 'ms_cart',
    'msMiniCart' => 'ms_minicart',
    'msGallery' => 'ms_gallery',
    'msOptions' => 'ms_options',
    'msOrder' => 'ms_order',
    'msGetOrder' => 'ms_get_order',
    'msProductOptions' => 'ms_product_options',
);

foreach ($tmp as $k => $v) {
    /** @var modSnippet $snippet */
    $snippet = $modx->newObject('modSnippet');
    $snippet->fromArray(array(
        'id' => 0,
        'name' => $k,
        'description' => '',
        'snippet' => getSnippetContent($sources['source_core'] . '/elements/snippets/snippet.' . $v . '.php'),
        'static' => BUILD_SNIPPET_STATIC,
        'source' => 1,
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/snippet.' . $v . '.php',
    ), '', true, true);

    $properties = include $sources['build'] . 'properties/properties.' . $v . '.php';
    $snippet->setProperties($properties);

    $snippets[] = $snippet;
}
unset($properties);

return $snippets;
