<?php
/** @var modX $modx */
/** @var array $scriptProperties */

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msOptions');
if (!empty($input) && empty($product)) {
    $product = $input;
}
if (!empty($name) && empty($options)) {
    $options = $name;
}

$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', $product)
    : $modx->resource;
if (!($product instanceof msProduct)) {
    return "[msProductOptions] The resource with id = {$product->id} is not instance of msProduct.";
}

$ignoreOptions = array_map('trim', explode(',', $modx->getOption('ignoreOptions', $scriptProperties, '')));
$groups = !empty($groups)
    ? array_map('trim', explode(',', $groups))
    : array();
/** @var msProductData $data */
if ($data = $product->getOne('Data')) {
    $optionKeys = $data->getOptionKeys();
}
if (empty($optionKeys)) {
    return '';
}
$productData = $product->loadOptions();

$options = array();
foreach ($optionKeys as $key) {
    if (in_array($key, $ignoreOptions)) {
        continue;
    }
    $option = array();
    foreach ($productData as $dataKey => $dataValue) {
        $dataKey = explode('.', $dataKey);
        if ($dataKey[0] == $key && (count($dataKey) > 1)) {
            $option[$dataKey[1]] = $dataValue;
        }
    }
    $option['value'] = $product->get($key);

    // Filter by groups
    $skip = !empty($groups) && !in_array($option['category'], $groups) && !in_array($option['category_name'], $groups);
    if ($skip || empty($option['value'])) {
        continue;
    }
    $options[$key] = $option;
}

/** @var pdoTools $pdoTools */
$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, array(
    'options' => $options,
));