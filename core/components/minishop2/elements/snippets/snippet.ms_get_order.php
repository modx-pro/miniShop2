<?php

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}
$pdoFetch->addTime('pdoTools loaded.');

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msGetOrder');

if (empty($id) && !empty($_GET['msorder'])) {
    $id = (int)$_GET['msorder'];
}
if (empty($id)) {
    return;
}
/** @var msOrder $order */
if (!$order = $modx->getObject('msOrder', compact('id'))) {
    return $modx->lexicon('ms2_err_order_nf');
}
$canView = (!empty($_SESSION['minishop2']['orders']) && in_array($id, $_SESSION['minishop2']['orders'])) ||
    $order->get('user_id') == $modx->user->id || $modx->user->hasSessionContext(
        'mgr'
    ) || !empty($scriptProperties['id']);
if (!$canView) {
    return '';
}

// Select ordered products
$where = [
    'msOrderProduct.order_id' => $id,
];

// Include products properties
$leftJoin = [
    'msProduct' => [
        'class' => 'msProduct',
        'on' => 'msProduct.id = msOrderProduct.product_id',
    ],
    'Data' => [
        'class' => 'msProductData',
        'on' => 'msProduct.id = Data.id',
    ],
    'Vendor' => [
        'class' => 'msVendor',
        'on' => 'Data.vendor = Vendor.id',
    ],
];

// Select columns
$select = [
    'msProduct' => !empty($includeContent)
        ? $modx->getSelectColumns('msProduct', 'msProduct')
        : $modx->getSelectColumns('msProduct', 'msProduct', '', ['content'], true),
    'Data' => $modx->getSelectColumns(
            'msProductData',
            'Data',
            '',
            ['id'],
            true
        ) . ',`Data`.`price` as `original_price`',
    'Vendor' => $modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', ['id'], true),
    'OrderProduct' => $modx->getSelectColumns(
            'msOrderProduct',
            'msOrderProduct',
            '',
            ['id'],
            true
        ) . ', `msOrderProduct`.`id` as `order_product_id`',
];

// Include products thumbnails
if (!empty($includeThumbs)) {
    $thumbs = array_map('trim', explode(',', $includeThumbs));
    if (!empty($thumbs[0])) {
        foreach ($thumbs as $thumb) {
            $leftJoin[$thumb] = [
                'class' => 'msProductFile',
                'on' => "`{$thumb}`.product_id = msProduct.id AND `{$thumb}`.parent != 0 AND `{$thumb}`.path LIKE '%/{$thumb}/%'",
            ];
            $select[$thumb] = "`{$thumb}`.url as '{$thumb}'";
        }
        $pdoFetch->addTime('Included list of thumbnails: <b>' . implode(', ', $thumbs) . '</b>.');
    }
}

// Add user parameters
foreach (['where', 'leftJoin', 'select'] as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}
$pdoFetch->addTime('Conditions prepared');

// Tables for joining
$default = [
    'class' => 'msOrderProduct',
    'where' => $where,
    'leftJoin' => $leftJoin,
    'select' => $select,
    'joinTVsTo' => 'msProduct',
    'sortby' => 'msOrderProduct.id',
    'sortdir' => 'asc',
    'groupby' => 'msOrderProduct.id',
    'fastMode' => false,
    'limit' => 0,
    'return' => 'data',
    'decodeJSON' => true,
    'nestedChunkPrefix' => 'minishop2_',
];
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), true);
$rows = $pdoFetch->run();

$products = [];
$cart_count = 0;
$cart_discount_cost = 0;
foreach ($rows as $product) {
    $old_price = $product['original_price'] > $product['price']
        ? $product['original_price']
        : $product['old_price'];

    $discount_price = $old_price > 0 ? $old_price - $product['price'] : 0;

    $product['old_price'] = $miniShop2->formatPrice($old_price);
    $product['price'] = $miniShop2->formatPrice($product['price']);
    $product['cost'] = $miniShop2->formatPrice($product['cost']);
    $product['weight'] = $miniShop2->formatWeight($product['weight']);
    $product['discount_price'] = $miniShop2->formatPrice($discount_price);
    $product['discount_cost'] = $miniShop2->formatPrice($product['count'] * $discount_price);

    $product['id'] = (int)$product['id'];
    if (empty($product['name'])) {
        $product['name'] = $product['pagetitle'];
    } else {
        $product['pagetitle'] = $product['name'];
    }

    // Additional properties of product
    if (!empty($product['options']) && is_array($product['options'])) {
        foreach ($product['options'] as $option => $value) {
            $product['option.' . $option] = $value;
        }
    }

    // Add option values
    $options = $modx->call('msProductData', 'loadOptions', [$modx, $product['id']]);
    $products[] = array_merge($product, $options);

    // Count total
    $cart_count += $product['count'];
    $cart_discount_cost += $product['count'] * $discount_price;
}

$pls = array_merge($scriptProperties, [
    'order' => $order->toArray(),
    'products' => $products,
    'user' => ($tmp = $order->getOne('User'))
        ? array_merge($tmp->getOne('Profile')->toArray(), $tmp->toArray())
        : [],
    'address' => ($tmp = $order->getOne('Address'))
        ? $tmp->toArray()
        : [],
    'delivery' => ($tmp = $order->getOne('Delivery'))
        ? $tmp->toArray()
        : [],
    'payment' => ($payment = $order->getOne('Payment'))
        ? $payment->toArray()
        : [],
    'total' => [
        'cost' => $miniShop2->formatPrice($order->get('cost')),
        'cart_cost' => $miniShop2->formatPrice($order->get('cart_cost')),
        'delivery_cost' => $miniShop2->formatPrice($order->get('delivery_cost')),
        'weight' => $miniShop2->formatWeight($order->get('weight')),
        'cart_weight' => $miniShop2->formatWeight($order->get('weight')),
        'cart_count' => $cart_count,
        'cart_discount' => $cart_discount_cost
    ],
]);

// add "payment" link
if ($payment and $class = $payment->get('class')) {
    $status = $modx->getOption('payStatus', $scriptProperties, '1');
    $status = array_map('trim', explode(',', $status));
    if (in_array($order->get('status'), $status)) {
        $miniShop2->loadCustomClasses('payment');
        if (class_exists($class)) {
            /** @var msPaymentHandler|PayPal $handler */
            $handler = new $class($order);
            if (method_exists($handler, 'getPaymentLink')) {
                $link = $handler->getPaymentLink($order);
                $pls['payment_link'] = $link;
            }
        }
    }
}

$pls['scriptProperties'] = $scriptProperties;
$output = $pdoFetch->getChunk($tpl, $pls);

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="msGetOrderLog">' . print_r($pdoFetch->getTime(), true) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
