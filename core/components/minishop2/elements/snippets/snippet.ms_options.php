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
    ? $modx->getObject('msProduct', array('id' => $product))
    : $modx->resource;
if (!($product instanceof msProduct)) {
    return "[msOptions] The resource with id = {$product->id} is not instance of msProduct.";
}

$names = array_map('trim', explode(',', $options));
$options = array();
foreach ($names as $name) {
    if (!empty($name) && $option = $product->get($name)) {
        if (!is_array($option)) {
            $option = array($option);
        }
        if (!empty($option[0])) {
            $options[$name] = $option;
        }
    }
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
            array_multisort($options[$key], $order, $type);

            if ($first && ($index = array_search($first, $options[$key])) !== false) {
                unset($options[$key][$index]);
                array_unshift($options[$key], $first);
            }
        }
    }
}

/** @var pdoTools $pdoTools */
$pdoTools = $modx->getService('pdoTools');

return $pdoTools->getChunk($tpl, array(
    'id' => $product->id,
    'options' => $options,
));
