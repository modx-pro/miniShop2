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
	/* @var modX $modx */
	public $modx;
	protected $config = array(
		'json_response' => false
	);

	function __construct(xPDOObject $object, $config = array()) {
		$this->modx = & $object->xpdo;
	}

	/* @inheritdoc} */
	public function send(msOrder $order) {
		return $this->success('', array('msorder' => $order->get('id')));
	}

	/* @inheritdoc} */
	public function receive(msOrder $order) {
		return $this->success('');
	}

	/* This method returns an error of the cart
	 *
	 * @param string $message A lexicon key for error message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
	protected function error($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => false
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
	}


	/* This method returns an success of the cart
	 *
	 * @param string $message A lexicon key for success message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
	protected function success($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => true
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
	}

}