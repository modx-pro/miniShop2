<?php

class orderTest extends msOrderHandler {
	
	public function changeOrderStatus($order_id, $status_id) {
		$this->modx->log(1, $order_id .'--'. $status_id);
		
		return true;
	}
	
}