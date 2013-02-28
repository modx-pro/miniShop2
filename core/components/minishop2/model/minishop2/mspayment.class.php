<?php
class msPayment extends xPDOSimpleObject {

	/**
	 * {@inheritdoc}
	 *
	 */
	public function remove(array $ancestors= array ()) {
		$id = $this->get('id');
		$table = $this->xpdo->getTableName('msDeliveryMember');
		$sql = "DELETE FROM {$table} WHERE `payment_id` = '$id';";
		$this->xpdo->exec($sql);

		return parent::remove();
	}

}