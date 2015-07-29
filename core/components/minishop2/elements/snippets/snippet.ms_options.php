<?php
/* @var array $scriptProperties */
/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {return false;}
$pdoFetch = new pdoFetch($modx, $scriptProperties);

if (empty($product) && !empty($input)) {$product = $input;}
if (empty($selected)) {$selected = '';}
if (empty($outputSeparator)) {$outputSeparator = "\n";}
if ((empty($name) || $name == 'id') && !empty($options)) {$name = $options;}

$output = '';
$product = !empty($product) ? $modx->getObject('msProduct', $product) : $product = $modx->resource;
if (!($product instanceof msProduct)) {
	$output = 'This resource is not instance of msProduct class.';
}
elseif (!empty($name) && $options = $product->get($name)) {
    if (!is_array($options)) {
        $options = array($options);
    }
	if ($options[0] == '') {
		$output = !empty($tplEmpty)
			? $pdoFetch->getChunk($tplEmpty, $scriptProperties)
			: '';
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

		$rows = implode($outputSeparator, $rows);
		$output = empty($tplOuter)
			? $pdoFetch->getChunk('', array('name' => $name, 'rows' => $rows))
			: $pdoFetch->getChunk($tplOuter, array_merge($scriptProperties, array('name' => $name, 'rows' => $rows)));
	}
}

return $output;