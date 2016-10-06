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

if (!empty($scriptProperties['sortOptions'])) {
    $sorts = array_map('trim', explode(',', $scriptProperties['sortOptions']));
    foreach ($sorts as $sort) {
        $sort = explode(':', $sort);
        $key = $sort[0];

        $order = SORT_ASC;
        if (!empty($sort[1])) {
            $order = constant($sort[1]);
        }
        $type = SORT_STRING;
        if (!empty($sort[2])) {
            $type = constant($sort[2]);
        }

        $first = null;
        if (!empty($sort[3])) {
            $first = $sort[3];
        }

        if (array_key_exists($key, $options)) {
            array_multisort($options[$key]['value'], $order, $type);

            if ($first && ($index = array_search($first, $options[$key]['value'])) !== false) {
                unset($options[$key]['value'][$index]);
                array_unshift($options[$key]['value'], $first);
            }
        }
    }
}

/** @var pdoTools $pdoTools */
$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, array(
    'options' => $options,
));