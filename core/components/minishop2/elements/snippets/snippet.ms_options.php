<?php
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

if (empty($product) && !empty($input)) {$product = $input;}
if (empty($selected)) {$selected = '';}
if ((empty($name) || $name == 'id') && !empty($options)) {$name = $options;}
if (empty($outputSeparator)) {$outputSeparator = "\n";}

$output = '';
$product = !empty($product) ? $modx->getObject('msProduct', $product) : $product = $modx->resource;
if (!($product instanceof msProduct)) {
	$output = 'Wrong class_key';
}
else if ($options = $product->get($name)) {
	if ((!is_array($options) || $options[0] == '') && !empty($tplEmpty)) {
		$output = $pdoFetch->getChunk($tplEmpty, $scriptProperties);
	}
	else {
		$rows = array();
		foreach ($options as $value) {
			$pls = array(
				'value' => $value
				,'selected' => $value == $selected ? 'selected' : ''
			);
			$rows[] = empty($tplRow) ? $value : $pdoFetch->getChunk($tplRow, $pls);
		}
		if (!empty($rows)) {
			$rows = implode($outputSeparator, $rows);
			$output = empty($tplOuter) ? $rows : $pdoFetch->getChunk($tplOuter, array_merge($scriptProperties, array('name' => $name, 'rows' => $rows)));
		}
	}
}

return $output;