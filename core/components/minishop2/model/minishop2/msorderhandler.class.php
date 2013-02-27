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

	/* Add field to order
	 *
	 * @param string $key Name of the field
	 * @param string $value.Value of the field
	 *
	 * @return array|string $response
	 * */
	public function add($key, $value);

	/* Removes field from order
	 *
	 * @param string $key The key of the field
	 *
	 * @return array|string $response
	 * */
	public function remove($key);

	/* Removes field from order
	 *
	 * @param string $key The key of the field
	 * @param string $value.Value of the field
	 *
	 * @return boolean
	 * */
	public function validate($key, $value);

	/* Cleans the order
	 *
	 * @return array|string $response
	 * */
	public function clean();

	/* Returns the whole order
	 *
	 * @return array $cart
	 * */
	public function get();

	/* Set the whole order by one array
	 *
	 * @return void
	 * */
	public function set($order = array());

}


class msOrderHandler implements msOrderInterface {
	private $order;

	function __construct(miniShop2 & $ms2, array $config = array()) {
		$this->ms2 = & $ms2;
		$this->modx = & $ms2->modx;

		$this->config = array_merge(array(
			'cart' => & $_SESSION['minishop2']['order']
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


	public function add($key, $value) {

	}


	/* @inheritdoc} */
	public function remove($key) {

	}


	/* @inheritdoc} */
	public function validate($key, $value) {

	}


	/* @inheritdoc} */
	public function clean() {
	//	$this->modx->invokeEvent('msOnBeforeEmptyCart', array('cart' => $this));
		$this->order = array();
	//	$this->modx->invokeEvent('msOnEmptyCart', array('cart' => $this));

		return $this->success('ms2_cart_clean_success', $this->get());
	}


	/* @inheritdoc} */
	public function get() {
		return true;
	}


	/* @inheritdoc} */
	public function set($order = array()) {

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
		if ($this->config['json_response']) {
			return json_encode($response);
		}
		else {
			return $response;
		}
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
		if ($this->config['json_response']) {
			return json_encode($response);
		}
		else {
			return $response;
		}
	}
}