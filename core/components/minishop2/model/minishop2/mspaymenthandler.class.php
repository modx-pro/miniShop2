<?php

interface msPaymentInterface {

	/**
	 * Send user to payment service
	 *
	 * @param msOrder $order Object with an order
	 *
	 * @return array|boolean $response
	 */
	public function send(msOrder $order);


	/**
	 * Receives payment
	 *
	 * @param msOrder $order Object with an order
	 *
	 * @return array|boolean $response
	 */
	public function receive(msOrder $order);


	/**
	 * Returns an additional cost depending on the method of payment
	 *
	 * @param msOrderInterface $order
	 * @param msPayment $delivery
	 * @param float $cost
	 *
	 * @return integer
	 */
	public function getCost(msOrderInterface $order, msPayment $payment, $cost = 0);


	/**
	 * Returns failure response
	 *
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 *
	 * @return array|string
	 */
	public function error($message = '', $data = array(), $placeholders = array());


	/**
	 * Returns success response
	 *
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 *
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array());
}


class msPaymentHandler implements msPaymentInterface {
	/* @var modX $modx */
	public $modx;
	/* @var miniShop2 $ms2 */
	public $ms2;


	/**
	 * @param xPDOObject $object
	 * @param array $config
	 */
	function __construct(xPDOObject $object, $config = array()) {
		$this->modx = & $object->xpdo;
		$this->ms2 = & $object->xpdo->getService('minishop2');
	}


	/** @inheritdoc} */
	public function send(msOrder $order) {
		return $this->success('', array('msorder' => $order->get('id')));
	}


	/** @inheritdoc} */
	public function receive(msOrder $order) {
		return $this->success('');
	}


	/** @inheritdoc} */
	public function getCost(msOrderInterface $order, msPayment $payment, $cost = 0) {
		$add_price = $payment->get('price');
		if (preg_match('/%$/', $add_price)) {
			$add_price = str_replace('%', '', $add_price);
			$add_price = $cost / 100 * $add_price;
		}
		$cost += $add_price;
		return $cost;
	}


	/** @inheritdoc} */
	public function error($message = '', $data = array(), $placeholders = array()) {
		if (!is_object($this->ms2)) {
			$this->ms2 = $this->modx->getService('minishop2');
		}
		return $this->ms2->error($message, $data, $placeholders);
	}


	/** @inheritdoc} */
	public function success($message = '', $data = array(), $placeholders = array()) {
		if (!is_object($this->ms2)) {
			$this->ms2 = $this->modx->getService('minishop2');
		}
		return $this->ms2->success($message, $data, $placeholders);
	}


	/**
	 * Returns hash of order for various checks
	 *
	 * @param msOrder $order
	 *
	 * @return string
	 */
	public function getOrderHash(msOrder $order) {
		return md5(
			$order->get('id') .
			$order->get('cart_cost') .
			$order->get('delivery_cost') .
			$order->get('createdon')
		);
	}
}