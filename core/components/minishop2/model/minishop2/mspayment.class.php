<?php

/**
 * @property int id
 * @property int rank
 */
class msPayment extends xPDOSimpleObject
{
    /** @var msPaymentHandler $handler */
    var $handler;
    /** @var miniShop2 $ms2 */
    var $ms2;


    /**
     * msPayment constructor.
     *
     * @param xPDO $xpdo
     */
    public function __construct(xPDO $xpdo)
    {
        parent::__construct($xpdo);
        $this->ms2 = $this->xpdo->getService('miniShop2');
    }


    /**
     * Loads payment handler class
     *
     * @return bool
     */
    public function loadHandler()
    {
        require_once dirname(__FILE__) . '/mspaymenthandler.class.php';

        if (!$class = $this->get('class')) {
            $class = 'msPaymentHandler';
        }

        if ($class != 'msPaymentHandler') {
            $this->ms2->loadCustomClasses('payment');
        }

        if (!class_exists($class)) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Payment handler class "' . $class . '" not found.');
            $class = 'msPaymentHandler';
        }

        $this->handler = new $class($this, array());
        if (!($this->handler instanceof msPaymentInterface)) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Could not initialize payment handler class: "' . $class . '"');

            return false;
        }

        return true;
    }


    /**
     * Send user to payment service
     *
     * @param msOrder $order Object with an order
     *
     * @return array|boolean $response
     */
    public function send(msOrder $order)
    {
        if (!is_object($this->handler) || !($this->handler instanceof msPaymentInterface)) {
            if (!$this->loadHandler()) {
                return false;
            }
        }

        return $this->handler->send($order);
    }


    /**
     * Receives payment
     *
     * @param msOrder $order Object with an order
     *
     * @return array|boolean $response
     */
    public function receive(msOrder $order)
    {
        if (!is_object($this->handler) || !($this->handler instanceof msPaymentInterface)) {
            if (!$this->loadHandler()) {
                return false;
            }
        }

        return $this->handler->receive($order);
    }


    /**
     * Returns an additional cost depending on the method of payment
     *
     * @param msOrderInterface|msOrderHandler $order
     * @param float $cost Current cost of order
     *
     * @return float|integer
     */
    public function getCost(msOrderInterface $order, $cost = 0.0)
    {
        if (!is_object($this->handler) || !($this->handler instanceof msDeliveryInterface)) {
            if (!$this->loadHandler()) {
                return false;
            }
        }

        return $this->handler->getCost($order, $this, $cost);
    }


    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = array())
    {
        $this->xpdo->removeCollection('msDeliveryMember', array('payment_id' => $this->id));

        return parent::remove($ancestors);
    }
}