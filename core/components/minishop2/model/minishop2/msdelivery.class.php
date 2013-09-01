<?php
class msDelivery extends xPDOSimpleObject {
	/* @var msDeliveryHandler $handler */
	var $handler;
	/* @var miniShop2 $ms2 */
	var $ms2;


	/**
	 * Loads delivery handler class
	 *
	 * @return bool
	 */
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


	/**
	 * Returns an additional cost depending on the method of delivery
	 *
	 * @param msOrderInterface|msOrderHandler $order
	 * @param float $cost Current cost of order
	 *
	 * @return float|integer
	 */
	public function getCost(msOrderInterface $order, $cost = 0) {
		if (is_object($order->ms2) && $order->ms2 instanceof miniShop2) {
			$this->ms2 = & $order->ms2;
		}
		if (!is_object($this->handler) || !($this->handler instanceof msDeliveryInterface)) {
			if (!$this->loadHandler()) {
				return 0;
			}
		}
		return $this->handler->getCost($order, $this, $cost);
	}


	/**
	 * Returns id of first active payment method for this delivery
	 *
	 * @return int|mixed
	 */
	public function getFirstPayment() {
		$id = 0;
		$q = $this->xpdo->newQuery('msPayment');
		$q->leftJoin('msDeliveryMember', 'Member', '`msPayment`.`id` = `Member`.`payment_id`');
		$q->leftJoin('msDelivery', 'Delivery', '`Member`.`delivery_id` = `Delivery`.`id`');
		$q->sortby('`msPayment`.`rank`', 'ASC');
		$q->select('`msPayment`.`id`');
		$q->where(array('msPayment.active' => 1, 'Delivery.id' => $this->get('id')));
		$q->limit(1);
		if ($q->prepare() && $q->stmt->execute()) {
			$id = $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $id;
	}


	/** {@inheritdoc} */
	public function remove(array $ancestors= array ()) {
		$id = $this->get('id');
		$table = $this->xpdo->getTableName('msDeliveryMember');
		$sql = "DELETE FROM {$table} WHERE `delivery_id` = '$id';";
		$this->xpdo->exec($sql);

		return parent::remove();
	}
}