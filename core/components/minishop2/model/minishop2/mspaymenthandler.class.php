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


	/**
	 * Shorthand for MS2 error method
	 *
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 *
	 * @return array|string
	 */
	public function error($message = '', $data = array(), $placeholders = array()) {
		if (!is_object($this->ms2)) {
			$this->ms2 = $this->modx->getService('minishop2');
		}
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
	public function success($message = '', $data = array(), $placeholders = array()) {
		if (!is_object($this->ms2)) {
			$this->ms2 = $this->modx->getService('minishop2');
		}
		return $this->ms2->success($message, $data, $placeholders);
	}
}