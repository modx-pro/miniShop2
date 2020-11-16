<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msOptions');
if (!empty($input) && empty($product)) {
    $product = $input;
}

$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', array('id' => $product))
    : $modx->resource;
if (!($product instanceof msProduct)) {
    return "[msProductOptions] The resource with id = {$product->id} is not instance of msProduct.";
}

$ignoreOptions = array_diff(array_map('trim', explode(',', $modx->getOption('ignoreOptions', $scriptProperties, ''))), array(''));
$onlyOptions = array_diff(array_map('trim', explode(',', $modx->getOption('onlyOptions', $scriptProperties, ''))), array(''));
$sortOptions = array_diff(array_map('trim', explode(',', $modx->getOption('sortOptions', $scriptProperties, ''))), array(''));
if (empty($sortOptions) && !empty($onlyOptions)) {
    $sortOptions = $onlyOptions;
}
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
    // Filter by key
    if (!empty($onlyOptions) && $onlyOptions[0] != '' && !in_array($key, $onlyOptions)) {
        continue;
    } elseif (in_array($key, $ignoreOptions)) {
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

if (!empty($sortOptions) && !empty($options)) {
    $sortOptions = array_map('mb_strtolower', $sortOptions);
    uksort($options, function($a, $b) use ($sortOptions) {
        $ai = array_search(mb_strtolower($a, 'utf-8'), $sortOptions, true);
        $bi = array_search(mb_strtolower($b, 'utf-8'), $sortOptions, true);
        if ($ai === false && $bi === false) {
            return 0;
        } elseif ($ai === false) {
            return 1;
        } elseif ($bi === false) {
            return -1;
        } elseif ($ai < $bi) {
            return -1;
        } elseif ($ai > $bi) {
            return 1;
        }
        return 0;
    });
}

$options = $miniShop2->sortOptionValues($options, $scriptProperties['sortOptionValues']);

if (in_array($scriptProperties['return'], array('data', 'array'), true)) {
    return $options;
}

/** @var pdoTools $pdoTools */
$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, array(
    'options' => $options,
));
