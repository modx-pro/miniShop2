<?php

interface msOrderInterface
{

    /**
     * Initializes order to context
     * Here you can load custom javascript or styles
     *
     * @param string $ctx Context for initialization
     *
     * @return boolean
     */
    public function initialize($ctx = 'web');


    /**
     * Add one field to order
     *
     * @param string $key Name of the field
     * @param string $value .Value of the field
     *
     * @return boolean
     */
    public function add($key, $value);


    /**
     * Validates field before it set
     *
     * @param string $key The key of the field
     * @param string $value .Value of the field
     *
     * @return boolean|mixed
     */
    public function validate($key, $value);


    /**
     * Removes field from order
     *
     * @param string $key The key of the field
     *
     * @return boolean
     */
    public function remove($key);


    /**
     * Returns the whole order
     *
     * @return array $order
     */
    public function get();


    /**
     * Returns the one field of order
     *
     * @param array $order Whole order at one time
     *
     * @return array $order
     */
    public function set(array $order);


    /**
     * Submit the order. It will create record in database and redirect user to payment, if set.
     *
     * @return array $status Array with order status
     */
    public function submit();


    /**
     * Cleans the order
     *
     * @return boolean
     */
    public function clean();


    /**
     * Returns the cost of delivery depending on its settings and the goods in a cart
     *
     * @return array $response
     */
    public function getCost();
}


class msOrderHandler implements msOrderInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var array $config */
    public $config;
    /** @var array $order */
    protected $order;


    /**
     * @param miniShop2 $ms2
     * @param array $config
     */
    function __construct(miniShop2 & $ms2, array $config = array())
    {
        $this->ms2 = $ms2;
        $this->modx = $ms2->modx;

        $this->config = array_merge(array(
            'order' => & $_SESSION['minishop2']['order'],
        ), $config);

        $this->order = &$this->config['order'];
        $this->modx->lexicon->load('minishop2:order');

        if (empty($this->order) || !is_array($this->order)) {
            $this->order = array();
        }
    }


    /**
     * @param string $ctx
     *
     * @return bool
     */
    public function initialize($ctx = 'web')
    {
        return true;
    }


    /**
     * @param string $key
     * @param string $value
     *
     * @return array|string
     */
    public function add($key, $value)
    {
        $response = $this->ms2->invokeEvent('msOnBeforeAddToOrder', array(
            'key' => $key,
            'value' => $value,
            'order' => $this,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $value = $response['data']['value'];

        if (empty($value)) {
            $this->order[$key] = $validated = '';
        } else {
            $validated = $this->validate($key, $value);
            if ($validated !== false) {
                $this->order[$key] = $validated;
                $response = $this->ms2->invokeEvent('msOnAddToOrder', array(
                    'key' => $key,
                    'value' => $validated,
                    'order' => $this,
                ));
                if (!$response['success']) {
                    return $this->error($response['message']);
                }
                $validated = $response['data']['value'];
            } else {
                $this->order[$key] = '';
            }
        }

        return ($validated === false)
            ? $this->error('', array($key => $value))
            : $this->success('', array($key => $validated));
    }


    /**
     * @param string $key
     * @param string $value
     *
     * @return bool|mixed|string
     */
    public function validate($key, $value)
    {
        if ($key != 'comment') {
            $value = preg_replace('/\s+/', ' ', trim($value));
        }

        $response = $this->ms2->invokeEvent('msOnBeforeValidateOrderValue', array(
            'key' => $key,
            'value' => $value,
        ));
        $value = $response['data']['value'];

        $old_value = isset($this->order[$key]) ? $this->order[$key] : '';
        switch ($key) {
            case 'email':
                $value = preg_match('/^[^@а-яА-Я]+@[^@а-яА-Я]+(?<!\.)\.[^\.а-яА-Я]{2,}$/m', $value)
                    ? $value
                    : false;
                break;
            case 'receiver':
                // Transforms string from "nikolaj -  coster--Waldau jr." to "Nikolaj Coster-Waldau Jr."
                $tmp = preg_replace(
                    array('/[^-a-zа-яёЁ\s\.]/iu', '/\s+/', '/\-+/', '/\.+/'),
                    array('', ' ', '-', '.'),
                    $value
                );
                $tmp = preg_split('/\s/', $tmp, -1, PREG_SPLIT_NO_EMPTY);
                $tmp = array_map(array($this, 'ucfirst'), $tmp);
                $value = preg_replace('/\s+/', ' ', implode(' ', $tmp));
                if (empty($value)) {
                    $value = false;
                }
                break;
            case 'phone':
                $value = substr(preg_replace('/[^-+0-9]/iu', '', $value), 0, 15);
                break;
            case 'delivery':
                /** @var msDelivery $delivery */
                if (!$delivery = $this->modx->getObject('msDelivery', array('id' => $value, 'active' => 1))) {
                    $value = $old_value;
                } elseif (!empty($this->order['payment'])) {
                    if (!$this->hasPayment($value, $this->order['payment'])) {
                        $this->order['payment'] = $delivery->getFirstPayment();
                    };
                }
                break;
            case 'payment':
                if (!empty($this->order['delivery'])) {
                    $value = $this->hasPayment($this->order['delivery'], $value)
                        ? $value
                        : $old_value;
                }
                break;
            case 'index':
                $value = substr(preg_replace('/[^-0-9a-z]/iu', '', $value), 0, 10);
                break;
        }

        $response = $this->ms2->invokeEvent('msOnValidateOrderValue', array(
            'key' => $key,
            'value' => $value,
        ));
        $value = $response['data']['value'];

        return $value;
    }


    /**
     * Checks accordance of payment and delivery
     *
     * @param $delivery
     * @param $payment
     *
     * @return bool
     */
    public function hasPayment($delivery, $payment)
    {
        $q = $this->modx->newQuery('msPayment', array('id' => $payment, 'active' => 1));
        $q->innerJoin('msDeliveryMember', 'Member',
            'Member.payment_id = msPayment.id AND Member.delivery_id = ' . $delivery
        );

        return $this->modx->getCount('msPayment', $q) ? true : false;
    }


    /**
     * @param string $key
     *
     * @return array|bool|string
     */
    public function remove($key)
    {
        if ($exists = array_key_exists($key, $this->order)) {
            $response = $this->ms2->invokeEvent('msOnBeforeRemoveFromOrder', array(
                'key' => $key,
                'order' => $this,
            ));
            if (!$response['success']) {
                return $this->error($response['message']);
            }

            unset($this->order[$key]);
            $response = $this->ms2->invokeEvent('msOnRemoveFromOrder', array(
                'key' => $key,
                'order' => $this,
            ));
            if (!$response['success']) {
                return $this->error($response['message']);
            }
        }

        return $exists;
    }


    /**
     * @return array
     */
    public function get()
    {
        return $this->order;
    }


    /**
     * @param array $order
     *
     * @return array
     */
    public function set(array $order)
    {
        foreach ($order as $key => $value) {
            $this->add($key, $value);
        }

        return $this->order;
    }


    /**
     * Returns required fields for delivery
     *
     * @param $id
     *
     * @return array|string
     */
    public function getDeliveryRequiresFields($id = 0)
    {
        if (empty($id)) {
            $id = $this->order['delivery'];
        }
        /** @var msDelivery $delivery */
        if (!$delivery = $this->modx->getObject('msDelivery', array('id' => $id, 'active' => 1))) {
            return $this->error('ms2_order_err_delivery', array('delivery'));
        }
        $requires = $delivery->get('requires');
        $requires = empty($requires)
            ? array()
            : array_map('trim', explode(',', $requires));
        if (!in_array('email', $requires)) {
            $requires[] = 'email';
        }

        return $this->success('', array('requires' => $requires));
    }


    /**
     * @param array $data
     *
     * @return array|string
     */
    public function submit($data = array())
    {
        $response = $this->ms2->invokeEvent('msOnSubmitOrder', array(
            'data' => $data,
            'order' => $this,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        if (!empty($response['data']['data'])) {
            $this->set($response['data']['data']);
        }

        $response = $this->getDeliveryRequiresFields();
        if ($this->ms2->config['json_response']) {
            $response = json_decode($response, true);
        }
        $requires = $response['data']['requires'];

        $errors = array();
        foreach ($requires as $v) {
            if (!empty($v) && empty($this->order[$v])) {
                $errors[] = $v;
            }
        }
        if (!empty($errors)) {
            return $this->error('ms2_order_err_requires', $errors);
        }

        $user_id = $this->ms2->getCustomerId();
        $cart_status = $this->ms2->cart->status();
        $delivery_cost = $this->getCost(false, true);
        $cart_cost = $this->getCost(true, true) - $delivery_cost;
        $createdon = date('Y-m-d H:i:s');
        /** @var msOrder $order */
        $order = $this->modx->newObject('msOrder');
        $order->fromArray(array(
            'user_id' => $user_id,
            'createdon' => $createdon,
            'num' => $this->getNum(),
            'delivery' => $this->order['delivery'],
            'payment' => $this->order['payment'],
            'cart_cost' => $cart_cost,
            'weight' => $cart_status['total_weight'],
            'delivery_cost' => $delivery_cost,
            'cost' => $cart_cost + $delivery_cost,
            'status' => 0,
            'context' => $this->ms2->config['ctx'],
        ));

        // Adding address
        /** @var msOrderAddress $address */
        $address = $this->modx->newObject('msOrderAddress');
        $address->fromArray(array_merge($this->order, array(
            'user_id' => $user_id,
            'createdon' => $createdon,
        )));
        $order->addOne($address);

        // Adding products
        $cart = $this->ms2->cart->get();
        $products = array();
        foreach ($cart as $v) {
            if ($tmp = $this->modx->getObject('msProduct', $v['id'])) {
                $name = $tmp->get('pagetitle');
            } else {
                $name = '';
            }
            /** @var msOrderProduct $product */
            $product = $this->modx->newObject('msOrderProduct');
            $product->fromArray(array_merge($v, array(
                'product_id' => $v['id'],
                'name' => $name,
                'cost' => $v['price'] * $v['count'],
            )));
            $products[] = $product;
        }
        $order->addMany($products);

        $response = $this->ms2->invokeEvent('msOnBeforeCreateOrder', array(
            'msOrder' => $order,
            'order' => $this,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        if ($order->save()) {
            $response = $this->ms2->invokeEvent('msOnCreateOrder', array(
                'msOrder' => $order,
                'order' => $this,
            ));
            if (!$response['success']) {
                return $this->error($response['message']);
            }

            $this->ms2->cart->clean();
            $this->clean();
            if (empty($_SESSION['minishop2']['orders'])) {
                $_SESSION['minishop2']['orders'] = array();
            }
            $_SESSION['minishop2']['orders'][] = $order->get('id');

            // Trying to set status "new"
            $response = $this->ms2->changeOrderStatus($order->get('id'), 1);
            if ($response !== true) {
                return $this->error($response, array('msorder' => $order->get('id')));
            } /** @var msPayment $payment */
            elseif ($payment = $this->modx->getObject('msPayment',
                array('id' => $order->get('payment'), 'active' => 1))
            ) {
                $response = $payment->send($order);
                if ($this->config['json_response']) {
                    @session_write_close();
                    exit(is_array($response) ? json_encode($response) : $response);
                } else {
                    if (!empty($response['data']['redirect'])) {
                        $this->modx->sendRedirect($response['data']['redirect']);
                    } elseif (!empty($response['data']['msorder'])) {
                        $this->modx->sendRedirect(
                            $this->modx->context->makeUrl(
                                $this->modx->resource->id,
                                array('msorder' => $response['data']['msorder'])
                            )
                        );
                    } else {
                        $this->modx->sendRedirect($this->modx->context->makeUrl($this->modx->resource->id));
                    }

                    return $this->success();
                }
            } else {
                if ($this->ms2->config['json_response']) {
                    return $this->success('', array('msorder' => $order->get('id')));
                } else {
                    $this->modx->sendRedirect(
                        $this->modx->context->makeUrl(
                            $this->modx->resource->id,
                            array('msorder' => $response['data']['msorder'])
                        )
                    );

                    return $this->success();
                }
            }
        }

        return $this->error();
    }


    /**
     * @return array|string
     */
    public function clean()
    {
        $response = $this->ms2->invokeEvent('msOnBeforeEmptyOrder', array('order' => $this));
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $this->order = array();
        $response = $this->ms2->invokeEvent('msOnEmptyOrder', array('order' => $this));
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        return $this->success('', array());
    }


    /**
     * @param bool $with_cart
     * @param bool $only_cost
     *
     * @return array|string
     */
    public function getCost($with_cart = true, $only_cost = false)
    {
        $response = $this->ms2->invokeEvent('msOnBeforeGetOrderCost', array(
            'order' => $this,
            'cart' => $this->ms2->cart,
            'with_cart' => $with_cart,
            'only_cost' => $only_cost,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $cart = $this->ms2->cart->status();
        $cost = $with_cart
            ? $cart['total_cost']
            : 0;

        /** @var msDelivery $delivery */
        if (!empty($this->order['delivery']) && $delivery = $this->modx->getObject('msDelivery',
                $this->order['delivery'])
        ) {
            $cost = $delivery->getCost($this, $cost);
        }

        /** @var msPayment $payment */
        if (!empty($this->order['payment']) && $payment = $this->modx->getObject('msPayment',
                $this->order['payment'])
        ) {
            $cost = $payment->getCost($this, $cost);
        }

        $response = $this->ms2->invokeEvent('msOnGetOrderCost', array(
            'order' => $this,
            'cart' => $this->ms2->cart,
            'with_cart' => $with_cart,
            'only_cost' => $only_cost,
            'cost' => $cost,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
        $cost = $response['data']['cost'];

        return $only_cost
            ? $cost
            : $this->success('', array('cost' => $cost));
    }


    /**
     * Return current number of order
     *
     * @return string
     */
    public function getNum()
    {
        $cur = date('ym');
        $num = 0;

        $c = $this->modx->newQuery('msOrder');
        $c->where(array('num:LIKE' => "{$cur}%"));
        $c->select('num');
        $c->sortby('id', 'DESC');
        $c->limit(1);
        if ($c->prepare() && $c->stmt->execute()) {
            $num = $c->stmt->fetchColumn();
        }
        if (empty($num)) {
            $num = date('ym') . '/0';
        }
        $num = explode('/', $num);
        $num = $cur . '/' . ($num[1] + 1);

        return $num;
    }


    /**
     * Shorthand for MS2 error method
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function error($message = '', $data = array(), $placeholders = array())
    {
        return $this->ms2->error($message, $data, $placeholders);
    }


    /**
     * Shorthand for MS2 success method
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        return $this->ms2->success($message, $data, $placeholders);
    }


    /**
     * Ucfirst function with support of cyrillic
     *
     * @param string $str
     *
     * @return string
     */
    public function ucfirst($str = '')
    {
        if (!preg_match('/[a-zа-я]/iu', $str)) {
            return '';
        } elseif (strpos($str, '-') !== false) {
            $tmp = array_map(array($this, __FUNCTION__), explode('-', $str));

            return implode('-', $tmp);
        }

        if (function_exists('mb_substr') && preg_match('/[а-я]/iu', $str)) {
            $tmp = mb_strtolower($str, 'utf-8');
            $str = mb_substr(mb_strtoupper($tmp, 'utf-8'), 0, 1, 'utf-8') .
                mb_substr($tmp, 1, mb_strlen($tmp) - 1, 'utf-8');
        } else {
            $str = ucfirst(strtolower($str));
        }

        return $str;
    }
}