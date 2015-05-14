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

$query = $modx->newQuery('msProductOption');
//$query->select($modx->getSelectColumns('msProductOption'));
$query->select(array(
    'msProductOption.key',
    'msProductOption.value',
    'msProductOption.product_id',
    'msOption.caption',
    'msOption.type'
));
$query->where(array(
    'msProductOption.product_id'=>$product->id
));
$query->leftJoin('msOption','msOption','`msProductOption`.`key` = `msOption`.`key`');
$msProductOptions = $modx->getCollection('msProductOption',$query);

if($msProductOptions){
    $productOptions = array();
    foreach ($msProductOptions as $msProductOption) {
        $option = $msProductOption->toArray();
        $key = $option['key'];

        if(isset($productOptions[$key])){
            $productOptions[$key]['values'][] = $option['value'];
        }
        else{
            $productOptions[$key] = $option;
            $productOptions[$key]['values'] = array($option['value']);
        }
    }

    $rows = array();
    foreach($productOptions as $productOption){
        $productOption['value'] = implode($valuesSeparator,$productOption['values']);
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