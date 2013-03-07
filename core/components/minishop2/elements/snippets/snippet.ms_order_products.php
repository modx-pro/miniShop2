<?php
if (empty($id) || empty($tpl)) {return false;}

/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->config = array_merge($pdoFetch->config, array('nestedChunkPrefix' => 'minishop2_'));
$pdoFetch->addTime('pdoTools loaded.');

$where = array(
	'order_id' => $id
);

// Initializing chunk for template rows
$pdoFetch->getChunk($tpl);
$output = '';
$ordered = $modx->getCollection('msOrderProduct', array('order_id' => $id));
/* @var msOrderProduct $row */
foreach ($ordered as $row) {
	if ($product = $row->getOne('Product')) {

		$item = array_merge($row->toArray(), $product->toArray());
		$tvs = $product->getMany('TemplateVars');
		/* @var modTemplateVar $tv */
		foreach ($tvs as $tv) {
			$item[$tv->get('name')] = $tv->get('value');
		}

		// Additional properties of product
		$options = $row->get('options');
		if (!empty($options) && is_array($options)) {
			foreach ($options as $key => $value) {
				if (is_array($value)) {
					$item[$key] = '';
					foreach ($values as $value2) {
						$item[$key] .= str_replace('[[+value]]', $value2, @$pdoFetch->elements[$tpl]['placeholders'][$key]);
					}
				}
				else {
					$item[$key] = str_replace('[[+value]]', $value, @$pdoFetch->elements[$tpl]['placeholders'][$key]);
				}
			}
		}
		foreach ($item as $k => $v) {
			if (is_array($v)) {
				unset($item[$k]);
			}
		}

		$outer .= $pdoFetch->getChunk($tpl, $item);
	}
}
return $outer;