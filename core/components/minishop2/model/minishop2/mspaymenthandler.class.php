<?php

interface msPaymentInterface {

	/* Initializes payment method to context
	 *
	 * @param string $ctx Context for initialization
	 * @return boolean
	 * */
	public function initialize($ctx = 'web');

	/* Creates new operation of payment
	 *
	 * @param msOrder $order Object with a new order
	 * @return array|boolean $response
	 * */
	public function create($order);
}