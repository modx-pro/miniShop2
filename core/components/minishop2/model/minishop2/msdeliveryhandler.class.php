<?php

interface msDeliveryInterface {

	/**
	 * Get the cost of delivery
	 *
	 * @param msOrderInterface $order
	 * @param msDelivery $delivery
	 *
	 * @return integer
	 */
	public function getcost(msOrderInterface $order, msDelivery $delivery);
}


class msDeliveryHandler implements msDeliveryInterface {
	/** @var modX $modx */
	public $modx;
	/** @var miniShop2 $ms2 */
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
	public function getcost(msOrderInterface $order, msDelivery $delivery) {
		$cart = $this->ms2->cart->status();
		$min_price = $delivery->get('price');
		$weight_price = $delivery->get('weight_price');
		//$distance_price = $delivery->get('distance_price');

		$cart_weight = $cart['total_weight'];
		$cost = $min_price + ($weight_price * $cart_weight);

		return $cost;
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