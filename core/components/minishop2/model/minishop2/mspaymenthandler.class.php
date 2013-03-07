<?php

interface msPaymentInterface {

	/* Send user to payment service
	 *
	 * @param msOrderInterface $order Object with an order
	 * @return array|boolean $response
	 * */
	public function send(msOrder $order);

	/* Receives payment
	 *
	 * @param msOrderInterface $order Object with an order
	 * @return array|boolean $response
	 * */
	public function receive(msOrder $order);
}

class msPaymentHandler implements msPaymentInterface {

	public function send(msOrder $order) {
		return array('msorder' => $order->get('id'));
	}

	public function receive(msOrder $order) {
		return true;
	}
}