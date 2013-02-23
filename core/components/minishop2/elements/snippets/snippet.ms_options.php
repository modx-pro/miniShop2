<?php
/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->config = array_merge($pdoFetch->config, array('nestedChunkPrefix' => 'minishop2_'));
$pdoFetch->addTime('pdoTools loaded.');

if (!empty($product)) {
	$product = $modx->getObject('msProduct', $product);
}
else {
	$product = $modx->resource;
}

if (!$options = $product->get($name)) {return false;}
if (!is_array($options) || empty($options[0])) {return $pdoFetch->getChunk($tplEmpty);}

$rows = array();
foreach ($options as $value) {
	$pls = array(
		'value' => $value
		,'selected' => $value == $selected ? 'selected' : ''
	);
	$rows[] = empty($tplRow) ? $value : $pdoFetch->getChunk($tplRow, $pls);
}
if (!empty($rows)) {
	$rows = empty($tplRow) ? implode(', ', $rows) : implode('', $rows);
	$output = empty($tplOuter) ? $rows : $pdoFetch->getChunk($tplOuter, array_merge($scriptProperties, array('rows' => $rows)));
	return $output;
}