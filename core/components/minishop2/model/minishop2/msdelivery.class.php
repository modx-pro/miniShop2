<?php

/**
 * @property int id
 * @property int rank
 */
class msDelivery extends xPDOSimpleObject
{
    /** @var msDeliveryHandler $handler */
    var $handler;
    /** @var miniShop2 $ms2 */
    var $ms2;


    /**
     * msDelivery constructor.
     *
     * @param xPDO $xpdo
     */
    public function __construct(xPDO $xpdo)
    {
        parent::__construct($xpdo);
        $this->ms2 = $this->xpdo->getService('miniShop2');
    }


    /**
     * Loads delivery handler class
     *
     * @return bool
     */
    public function loadHandler()
    {
        require_once dirname(__FILE__) . '/msdeliveryhandler.class.php';

        if (!$class = $this->get('class')) {
            $class = 'msDeliveryHandler';
        }

        if ($class != 'msDeliveryHandler') {
            $this->ms2->loadCustomClasses('delivery');
        }

        if (!class_exists($class)) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Delivery handler class "' . $class . '" not found.');
            $class = 'msDeliveryHandler';
        }

        $this->handler = new $class($this, array());
        if (!($this->handler instanceof msDeliveryInterface)) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Could not initialize delivery handler class: "' . $class . '"');

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
     * @return float
     */
    public function getCost(msOrderInterface $order, $cost = 0.0)
    {
        if (!is_object($this->handler) || !($this->handler instanceof msDeliveryInterface)) {
            if (!$this->loadHandler()) {
                return 0.0;
            }
        }

        return $this->handler->getCost($order, $this, $cost);
    }


    /**
     * Returns id of first active payment method for this delivery
     *
     * @return int|mixed
     */
    public function getFirstPayment()
    {
        $id = 0;
        $c = $this->xpdo->newQuery('msPayment');
        $c->leftJoin('msDeliveryMember', 'Member', 'msPayment.id = Member.payment_id');
        $c->leftJoin('msDelivery', 'Delivery', 'Member.delivery_id = Delivery.id');
        $c->sortby('msPayment.rank', 'ASC');
        $c->select('msPayment.id');
        $c->where(array('msPayment.active' => 1, 'Delivery.id' => $this->id));
        $c->limit(1);
        if ($c->prepare() && $c->stmt->execute()) {
            $id = $c->stmt->fetchColumn();
        }

        return $id;
    }


    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = array())
    {
        $this->xpdo->removeCollection('msDeliveryMember', array('delivery_id' => $this->id));

        return parent::remove($ancestors);
    }
}