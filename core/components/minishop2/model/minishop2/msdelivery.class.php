<?php
class msDelivery extends xPDOSimpleObject {
	/* @var msDeliveryHandler $handler */
	var $handler;
	/* @var miniShop2 $ms2 */
	var $ms2;


	public function loadHandler() {
		require_once dirname(__FILE__).'/msdeliveryhandler.class.php';

		if (!$class = $this->get('class')) {
			$class = 'msDeliveryHandler';
		}

		if ($class != 'msDeliveryHandler') {
			if (!is_object($this->ms2) || !($this->ms2 instanceof miniShop2)) {
				require_once dirname(__FILE__).'/minishop2.class.php';
				$this->ms2 = new miniShop2($this->xpdo, array());
			}
			$this->ms2->loadCustomClasses('delivery');
		}

		if (!class_exists($class)) {
			$this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Delivery handler class "'.$class.'" not found.');
			$class = 'msDeliveryHandler';
		}

		$this->handler = new $class($this, array());
		if (!($this->handler instanceof msDeliveryInterface)) {
			$this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Could not initialize delivery handler class: "'.$class.'"');
			return false;
		}

		return true;
	}


	public function getcost(msOrderInterface $order) {
		if (is_object($order->ms2) && $order->ms2 instanceof miniShop2) {
			$this->ms2 = & $order->ms2;
		}
		if (!is_object($this->handler) || !($this->handler instanceof msDeliveryHandler)) {
			if (!$this->loadHandler()) {
				return false;
			}
		}
		return $this->handler->getcost($order, $this);
	}


	/**
	 * {@inheritdoc}
	 *
	 */
	public function remove(array $ancestors= array ()) {
		$id = $this->get('id');
		$table = $this->xpdo->getTableName('msDeliveryMember');
		$sql = "DELETE FROM {$table} WHERE `delivery_id` = '$id';";
		$this->xpdo->exec($sql);

		return parent::remove();
	}
}