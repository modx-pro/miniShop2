<?php
/* @var miniShop2 $miniShop2 */
/* @var pdoFetch $pdoFetch */
$miniShop2 = $modx->getService('minishop2','miniShop2',$modx->getOption('minishop2.core_path',null,$modx->getOption('core_path').'components/minishop2/').'model/minishop2/', $scriptProperties);
$miniShop2->initialize($modx->context->key);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);
$pdoFetch->config = array_merge($pdoFetch->config, array('nestedChunkPrefix' => 'minishop2_'));
$pdoFetch->addTime('pdoTools loaded.');

$cart = $miniShop2->cart->get();
if (empty($cart)) {
	return $pdoFetch->getChunk($tplEmpty);
}
// Initializing chunk for template rows
$pdoFetch->getChunk($tplRow);

// Working
$outer = array('goods' => '', 'total_count' => 0, 'total_weight' => 0, 'total_cost' => 0);
foreach ($cart as $k => $v) {
	/* @var msProduct $product */
	if ($product = $modx->getObject('msProduct', array('id' => $v['id'], 'class_key' => 'msProduct', 'published' => 1, 'deleted' => 0))) {
		$item = $product->toArray();
		$item['key'] = $k;
		$item['count'] = $v['count'];
		$item['price'] = $v['price'];
		$item['weight'] = $v['weight'];
		$item['cost'] = $v['count'] * $v['price'];

		$tvs = $product->getMany('TemplateVars');
		/* @var modTemplateVar $tv */
		foreach ($tvs as $tv) {
			$item[$tv->get('name')] = $tv->get('value');
		}

		// Additional properties of product
		if (!empty($v['options']) && is_array($v['options'])) {
			foreach ($v['options'] as $key => $value) {
				if (is_array($value)) {
					$item[$key] = '';
					foreach ($values as $value2) {
						$item[$key] .= str_replace('[[+value]]', $value2, @$pdoFetch->elements[$tplRow]['placeholders'][$key]);
					}
				}
				else {
					$item[$key] = str_replace('[[+value]]', $value, @$pdoFetch->elements[$tplRow]['placeholders'][$key]);
				}
			}
		}
		unset($v['data']);

		// Unset json options
		foreach ($item as $key => $value) {
			if (is_array($value)) {
				unset($item[$key]);
			}
		}

		$outer['goods'] .= $pdoFetch->getChunk($tplRow, $item);
		$outer['total_count'] += $v['count'];
		$outer['total_weight'] += $v['count'] * $v['weight'];
		$outer['total_cost'] += $item['cost'];
	}
}

return $pdoFetch->getChunk($tplOuter, $outer);