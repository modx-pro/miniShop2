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

if(count($optionKeys) > 0){
    $rows = array();
    foreach ($optionKeys as $key) {
        $productOption = array();
        foreach ($productData as $dataKey => $dataValue) {
            $dataKey = explode('.', $dataKey);
            if ($dataKey[0] == $key && (count($dataKey) > 1)) {
                $productOption[$dataKey[1]] = $dataValue;
            }
        }
        $productOption['value'] = implode($valuesSeparator,$productData[$key]);
        $rows[] = $pdoFetch->getChunk($tplRow, $productOption);
    }
    $rows = implode($outputSeparator, $rows);

    $output = empty($tplOuter)
        ? $pdoFetch->getChunk('', array('rows' => $rows))
        : $pdoFetch->getChunk($tplOuter, array_merge($scriptProperties, array('rows' => $rows)));
}
else{
    $output = !empty($tplEmpty)
        ? $pdoFetch->getChunk($tplEmpty, $scriptProperties)
        : '';
}

return $output;