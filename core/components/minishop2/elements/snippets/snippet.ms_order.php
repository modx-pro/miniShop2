<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);
/** @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {
    return false;
}
$pdoFetch = new pdoFetch($modx, $scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msOrder');

// Do not show order form when displaying details of existing order
if (!empty($_GET['msorder'])) {
    return '';
}

$cart = $miniShop2->cart->get();
$order = $miniShop2->order->get();
$status = $miniShop2->cart->status();
if (empty($status['total_count'])) {
    return '';
}
$cost = $miniShop2->order->getCost();
$order['cost'] = $miniShop2->formatPrice($cost['data']['cost']);

// We need only active methods
$where = array(
    'msDelivery.active' => true,
    'msPayment.active' => true,
);

// Join payments to deliveries
$leftJoin = array(
    'Payments' => array(
        'class' => 'msDeliveryMember',
    ),
    'msPayment' => array(
        'class' => 'msPayment',
        'on' => 'Payments.payment_id = msPayment.id',
    ),
);

// Select columns
$select = array(
    'msDelivery' => $modx->getSelectColumns('msDelivery', 'msDelivery', 'delivery_'),
    'msPayment' => $modx->getSelectColumns('msPayment', 'msPayment', 'payment_'),
);

// Add user parameters
foreach (array('where', 'leftJoin', 'select') as $v) {
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

// Default parameters
$default = array(
    'class' => 'msDelivery',
    'where' => $where,
    'leftJoin' => $leftJoin,
    'select' => $select,
    'sortby' => 'msDelivery.rank asc, msPayment.rank',
    'sortdir' => 'asc',
    'limit' => 0,
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
);
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$rows = $pdoFetch->run();

$deliveries = $payments = array();
foreach ($rows as $row) {
    $delivery = array();
    $payment = array();
    foreach ($row as $key => $value) {
        if (strpos($key, 'delivery_') === 0) {
            $delivery[substr($key, 9)] = $value;
        } else {
            $payment[substr($key, 8)] = $value;
        }
    }

    if (!isset($deliveries[$delivery['id']])) {
        $delivery['payments'] = array();
        $deliveries[$delivery['id']] = $delivery;
    }
    if (!empty($payment['id'])) {
        $deliveries[$delivery['id']]['payments'][] = (int)$payment['id'];
        if (!isset($payments[$payment['id']])) {
            $payments[$payment['id']] = $payment;
        }
    }
}

$form = array();
// Get user data
$profile = array();
if ($modx->user->isAuthenticated($modx->context->key)) {
    $profile = array_merge($modx->user->Profile->toArray(), $modx->user->toArray());
}
$fields = array(
    'receiver' => 'fullname',
    'phone' => 'phone',
    'email' => 'email',
    'comment' => 'extended[comment]',
    'index' => 'zip',
    'country' => 'country',
    'region' => 'state',
    'city' => 'city',
    'street' => 'address',
    'building' => 'extended[building]',
    'room' => 'extended[room]',
);
// Apply custom fields
if (!empty($userFields)) {
    if (!is_array($userFields)) {
        $userFields = json_decode($userFields, true);
    }
    if (is_array($userFields)) {
        $fields = array_merge($fields, $userFields);
    }
}
// Set user fields
foreach ($fields as $key => $value) {
    if (!empty($profile) && !empty($value)) {
        if (strpos($value, 'extended') !== false) {
            $tmp = substr($value, 9, -1);
            $value = !empty($profile['extended'][$tmp])
                ? $profile['extended'][$tmp]
                : '';
        } else {
            $value = $profile[$value];
        }
        $response = $miniShop2->order->add($key, $value);
        if ($response['success'] && !empty($response['data'][$key])) {
            $form[$key] = $response['data'][$key];
        }
    }
    if (empty($form[$key]) && !empty($order[$key])) {
        $form[$key] = $order[$key];
        unset($order[$key]);
    }
}

// Check for errors
$errors = array();
if (!empty($_POST)) {
    $response = $miniShop2->order->getDeliveryRequiresFields();
    if ($requires = $response['data']['requires']) {
        foreach ($_POST as $field => $val) {
            $validated = $miniShop2->order->validate($field, $val);
            if ((in_array($field, $requires) && empty($validated))) {
                $errors[] = $field;
            }
        }
    }

}

$output = $pdoFetch->getChunk($tpl, array(
    'order' => $order,
    'form' => $form,
    'deliveries' => $deliveries,
    'payments' => $payments,
    'errors' => $errors,
));

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="msOrderLog">' . print_r($pdoFetch->getTime(), true) . '</pre>';
}

return $output;