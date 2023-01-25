<?php

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msOptions');
if (!empty($input) && empty($product)) {
    $product = $input;
}
if (!empty($name) && empty($options)) {
    $options = $name;
}

$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', ['id' => $product])
    : $modx->resource;
if (!($product instanceof msProduct)) {
    return "[msOptions] The resource with id = {$product->id} is not instance of msProduct.";
}

$names = array_map('trim', explode(',', $options));
$options = [];
foreach ($names as $name) {
    if (!empty($name) && $option = $product->get($name)) {
        if (!is_array($option)) {
            $option = [$option];
        }
        if (isset($option[0]) and (trim($option[0]) != '')) {
            $options[$name] = $option;
        }
    }
}

$options = $miniShop2->sortOptionValues($options, $scriptProperties['sortOptionValues']);

/** @var pdoTools $pdoTools */
$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, [
    'id' => $product->id,
    'options' => $options,
    'scriptProperties' => $scriptProperties
]);
