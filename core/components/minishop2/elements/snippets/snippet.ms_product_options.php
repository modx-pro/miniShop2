<?php
/* @var array $scriptProperties */
/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize($modx->context->key);

/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {return false;}
$pdoFetch = new pdoFetch($modx, $scriptProperties);

$output = '';

if (empty($product) && !empty($input)) {$product = $input;}
if (empty($outputSeparator)) {$outputSeparator = "\n";}
$options = explode(",",$modx->getOption('options',$scriptProperties,''));

$product = !empty($product) ? $modx->getObject('msProduct', $product) : $product = $modx->resource;
if (!($product instanceof msProduct)) {
    $output = 'This resource is not instance of msProduct class.';
}

$optionKeys = $product->getOptionKeys();
$productData = $product->toArray();

$ignoreOptions = explode(',', trim($modx->getOption('ignoreOptions', $scriptProperties, '')));

if(count($optionKeys) > 0){
    $rows = array();
    foreach ($optionKeys as $key) {
        if (in_array($key, $ignoreOptions)) continue;
        $productOption = array();
        foreach ($productData as $dataKey => $dataValue) {
            $dataKey = explode('.', $dataKey);
            if ($dataKey[0] == $key && (count($dataKey) > 1)) {
                $productOption[$dataKey[1]] = $dataValue;
            }
        }
        if (is_array($productData[$key])) {
            $values = array();
            foreach ($productData[$key] as $value) {
                $params = array_merge($productData, $productOption, array('value' => $value));
                $values[] = $pdoFetch->getChunk($tplValue, $params);
            }
            $productOption['value'] = implode($valuesSeparator, $values);
        } else {
            $productOption['value'] = $productData[$key];
        }

        if ($hideEmpty && empty($productOption['value'])) continue;
        $rows[] = $pdoFetch->getChunk($tplRow, array_merge($productData, $productOption));
    }
    $rows = implode($outputSeparator, $rows);

    $output = empty($tplOuter)
        ? $pdoFetch->getChunk('', array_merge($productData, array('rows' => $rows)))
        : $pdoFetch->getChunk($tplOuter, array_merge($scriptProperties, $productData, array('rows' => $rows)));
}
else{
    $output = !empty($tplEmpty)
        ? $pdoFetch->getChunk($tplEmpty, array_merge($scriptProperties, $productData))
        : '';
}

return $output;