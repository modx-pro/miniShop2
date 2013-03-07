<?php

interface msDeliveryInterface {

	/* Get the cost of delivery
	 *
	 * @param msOrderHandler $order
	 * @param msDelivery $delivery
	 * @return integer
	 * */
	public function getcost(msOrderInterface $order, msDelivery $delivery);
}

class msDeliveryHandler implements msDeliveryInterface {

	public function getcost(msOrderInterface $order, msDelivery $delivery) {
		$cart = $order->ms2->cart->status();
		$min_price = $delivery->get('price');
		$weight_price = $delivery->get('weight_price');
		//$distance_price = $delivery->get('distance_price');

		$cart_weight = $cart['total_weight'];
		$cost = $min_price + ($weight_price * $cart_weight);

		return $cost;
	}

}