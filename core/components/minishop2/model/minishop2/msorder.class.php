<?php

/**
 * @property int id
 */
class msOrder extends xPDOSimpleObject
{

    /**
     * @return bool
     */
    public function updateProducts()
    {
        $delivery_cost = $this->get('delivery_cost');
        $cart_cost = $cost = $weight = 0;

        $products = $this->getMany('Products');
        /** @var msOrderProduct $product */
        foreach ($products as $product) {
            $count = $product->get('count');
            $cart_cost += $product->get('price') * $count;
            $weight += $product->get('weight') * $count;
        }

        $this->fromArray([
            'cost' => $cart_cost + $delivery_cost,
            'cart_cost' => $cart_cost,
            'weight' => $weight,
            'update_products' => true
        ]);

        return $this->save();
    }

    public function save($cacheFlag = null)
    {
        $isNew = $this->isNew();

        if ($this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msOnBeforeSaveOrder', [
                'mode' => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
                'object' => $this,
                'msOrder' => $this,
                'cacheFlag' => $cacheFlag,
            ]);
        }

        $saved = parent:: save($cacheFlag);

        if ($saved && $this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msOnSaveOrder', [
                'mode' => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
                'object' => $this,
                'msOrder' => $this,
                'cacheFlag' => $cacheFlag,
            ]);
        }

        return $saved;
    }

    public function remove(array $ancestors = [])
    {
        if ($this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msOnBeforeRemoveOrder', [
                'id' => parent::get('id'),
                'object' => $this,
                'msOrder' => $this,
                'ancestors' => $ancestors,
            ]);
        }

        $removed = parent::remove($ancestors);

        if ($this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msOnRemoveOrder', [
                'id' => parent::get('id'),
                'object' => $this,
                'msOrder' => $this,
                'ancestors' => $ancestors,
            ]);
        }

        return $removed;
    }
}
