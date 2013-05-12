<?php

interface msOrderInterface {

	/* Initializes order to context
	 * Here you can load custom javascript or styles
	 *
	 * @param string $ctx Context for initialization
	 *
	 * @return boolean
	 * */
	public function initialize($ctx = 'web');

	/* Add one field to order
	 *
	 * @param string $key Name of the field
	 * @param string $value.Value of the field
	 *
	 * @return boolean
	 * */
	public function add($key, $value);

	/* Validates field before it set
	 *
	 * @param string $key The key of the field
	 * @param string $value.Value of the field
	 *
	 * @return boolean|mixed
	 * */
	public function validate($key, $value);

	/* Removes field from order
	 *
	 * @param string $key The key of the field
	 *
	 * @return boolean
	 * */
	public function remove($key);

	/* Returns the whole order
	 *
	 * @return array $order
	 * */
	public function get();

	/* Returns the one field of order
	 *
	 * @param array $order Whole order at one time
	 * @return array $order
	 * */
	public function set(array $order);

	/* Submit the order. It will create record in database and redirect user to payment, if set.
	 *
	 * @return array $status Array with order status
	 * */
	public function submit();

	/* Cleans the order
	 *
	 * @return boolean
	 * */
	public function clean();


	/* Returns the cost of delivery depending on its settings and the goods in a cart
	 *
	 * @return array $response
	 * */
	public function getcost();
}


class msOrderHandler implements msOrderInterface {
	/* @var modX $modx */
	public $modx;
	protected $order;
	/* @var miniShop2 $ms2  */
	public $ms2;

	function __construct(miniShop2 & $ms2, array $config = array()) {
		$this->ms2 = & $ms2;
		$this->modx = & $ms2->modx;

		$this->config = array_merge(array(
			'order' => & $_SESSION['minishop2']['order']
			,'json_response' => false
		),$config);

		$this->order = & $this->config['order'];
		$this->modx->lexicon->load('minishop2:order');

		if (empty($this->order) || !is_array($this->order)) {
			$this->order = array();
		}
	}


	/* @inheritdoc} */
	public function initialize($ctx = 'web') {
		return true;
	}


	/* @inheritdoc} */
	public function add($key, $value) {
		$this->modx->invokeEvent('msOnBeforeAddToOrder', array('key' => & $key, 'value' => & $value, 'order' => $this));
		if (empty($key)) {
			return $this->error('');
		}
		else if (empty($value)) {
			$this->order[$key] = $validated  = '';
		}
		else {
			$validated = $this->validate($key, $value);
			if ($validated !== false) {
				$this->order[$key] = $validated;
				$this->modx->invokeEvent('msOnAddToOrder', array('key' => & $key, 'value' => & $validated, 'order' => $this));
			}
		}
		return $this->success('', array($key => $validated));
	}


	/* @inheritdoc} */
	public function validate($key, $value) {
		if ($key != 'comment') {
			$value = preg_replace('/\s+/',' ', trim($value));
		}
		switch ($key) {
			case 'email': $value = preg_match('/.+@.+..+/i', $value) ? $value : @$this->order[$key]; break;
			case 'receiver':
				$value = preg_replace('/[^a-zа-я\s]/iu','',$value);
				$tmp = explode(' ',$value);
				$value = array();
				for ($i=0;$i<=2;$i++) {
					if (!empty($tmp[$i])) {
						$value[] = $this->ucfirst($tmp[$i]);
					}
				}
				$value = implode(' ', $value);
			break;
			case 'phone': $value = substr(preg_replace('/[^-+0-9]/iu','',$value),0,15); break;
			case 'delivery':
				/* @var msDelivery $delivery */
				if (!$delivery = $this->modx->getObject('msDelivery',array('id' => $value, 'active' => 1))) {
					$value = @$this->order['delivery'];
				}
				else if (!empty($this->order['payment'])) {
					if (!$this->hasPayment($value, $this->order['payment'])) {
						$this->order['payment'] = $delivery->getFirstPayment();
					};
				}
			break;
			case 'payment':
				if (!empty($this->order['delivery'])) {
					$value = $this->hasPayment($this->order['delivery'], $value) ? $value : @$this->order['payment'];
				}
			break;
			case 'index': $value = substr(preg_replace('/[^-0-9]/iu', '',$value),0,10); break;
			default: break;
		}

		if ($value === false) {$value = '';}
		return $value;
	}


	/* Checks accordance of payment and delivery
	 * */
	public function hasPayment($delivery, $payment) {
		$q = $this->modx->newQuery('msPayment', array('id' => $payment, 'active' => 1));
		$q->innerJoin('msDeliveryMember','Member','Member.payment_id = msPayment.id AND Member.delivery_id = '.$delivery);

		return $this->modx->getCount('msPayment', $q) ? true : false;
	}


	/* @inheritdoc} */
	public function remove($key) {
		if ($exists = array_key_exists($key, $this->order)) {
			$this->modx->invokeEvent('msOnBeforeRemoveFromOrder', array('key' => $key, 'order' => $this));
			unset($this->order[$key]);
			$this->modx->invokeEvent('msOnRemoveFromOrder', array('key' => $key, 'order' => $this));
		}
		return $exists;
	}


	/* @inheritdoc} */
	public function get() {
		return $this->order;
	}


	/* @inheritdoc} */
	public function set(array $order) {
		foreach ($order as $key => $value) {
			$this->add($key, $value);
		}
		return $this->order;
	}

	/* @inheritdoc} */
	public function submit($data = array()) {
		$this->modx->invokeEvent('msOnSubmitOrder', array('data' => & $data, 'order' => $this));
		if (!empty($data)) {
			$this->set($data);
		}

		/* @var msDelivery $delivery */
		if (!$delivery = $this->modx->getObject('msDelivery', array('id' => $this->order['delivery'], 'active' => 1))) {
			return $this->error('ms2_order_err_delivery', array('delivery'));
		}
		$requires = array_map('trim', explode(',',$delivery->get('requires')));
		$requires[] = 'email';
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
		$delivery_cost = $this->getcost(false, true);
		$createdon = date('Y-m-d H:i:s');
		/* @var msOrder $order */
		$order = $this->modx->newObject('msOrder');
		$order->fromArray(array(
			'user_id' => $user_id
			,'createdon' => $createdon
			,'num' => $this->getnum()
			,'delivery' => $this->order['delivery']
			,'payment' => $this->order['payment']
			,'cart_cost' => $cart_status['total_cost']
			,'weight' => $cart_status['total_weight']
			,'delivery_cost' => $delivery_cost
			,'cost' => $cart_status['total_cost'] + $delivery_cost
			,'status' => 0
			,'context' => $this->ms2->config['ctx']
		));

		// Adding address
		/* @var msOrderAddress $address */
		$address = $this->modx->newObject('msOrderAddress');
		$address->fromArray(array_merge($this->order,array(
			'user_id' => $user_id
			,'createdon' => $createdon
		)));
		$order->addOne($address);

		// Adding products
		$cart = $this->ms2->cart->get();
		$products = array();
		foreach ($cart as $v) {
			/* @var msOrderProduct $product */
			$product = $this->modx->newObject('msOrderProduct');
			$product->fromArray(array_merge($v, array(
				'product_id' => $v['id']
				,'cost' => $v['price'] * $v['count']
			)));
			$products[] = $product;
		}
		$order->addMany($products);

		$this->modx->invokeEvent('msOnBeforeCreateOrder', array('order' => $this));
		if ($order->save()) {
			$this->modx->invokeEvent('msOnCreateOrder', array('order' => $this));

			$this->ms2->cart->clean();
			$this->clean();
			if (empty($_SESSION['minishop2']['orders'])) {
				$_SESSION['minishop2']['orders'] = array();
			}
			$_SESSION['minishop2']['orders'][] = $order->get('id');

			$this->ms2->changeOrderStatus($order->get('id'), 1); // set status "new"
			/* @var msPayment $payment*/
			if ($payment = $this->modx->getObject('msPayment', array('id' => $order->get('payment'), 'active' => 1))) {
				$response = $payment->send($order);
				exit(is_array($response) ? $this->modx->toJSON($response) : $response);
			}
			else {
				return $this->success('', array('msorder' => $order->get('id')));
			}
		}
		return $this->error();
	}


	/* @inheritdoc} */
	public function clean() {
		$this->modx->invokeEvent('msOnBeforeEmptyOrder', array('order' => $this));
		$this->order = array();
		$this->modx->invokeEvent('msOnEmptyOrder', array('order' => $this));

		return $this->success('', array());
	}


	/* @inheritdoc} */
	public function getcost($with_cart = true, $only_cost = false) {
		$cost = 0;
		$cart = $this->ms2->cart->status();
		/* @var msDelivery $delivery */
		if ($delivery = $this->modx->getObject('msDelivery', $this->order['delivery'])) {
			$cost = $delivery->getcost($this);
		}

		if ($with_cart) {
			$cost += $cart['total_cost'];
		}

		return $only_cost ? $cost : $this->success('', array('cost' => $cost));
	}


	/* Return current number of order
	 *
	 * */
	public function getnum() {
		$table = $this->modx->getTableName('msOrder');
		$cur = date('ym');

		$sql = $this->modx->query("SELECT `num` FROM {$table} WHERE `num` LIKE '{$cur}%' ORDER BY `id` DESC LIMIT 1");
		$num = $sql->fetch(PDO::FETCH_COLUMN);

		if (empty($num)) {$num = date('ym').'/0';}
		$num = explode('/', $num);
		$num = $cur.'/'.($num[1] + 1);

		return $num;
	}


	/* This method returns an error of the order
	 *
	 * @param string $message A lexicon key for error message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
	public function error($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => false
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
	}


	/* This method returns an success of the order
	 *
	 * @param string $message A lexicon key for success message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
	public function success($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => true
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
	}


	public function ucfirst($str = '') {
		if (function_exists('mb_substr') && preg_match('/[а-я]/iu',$str)) {
			$tmp = mb_strtolower($str, 'utf-8');
			$str = mb_substr(mb_strtoupper($tmp, 'utf-8'), 0, 1, 'utf-8') . mb_substr($tmp, 1, mb_strlen($tmp)-1, 'utf-8');
		}
		else {
			$str = ucfirst(strtolower($str));
		}

		return $str;
	}
}