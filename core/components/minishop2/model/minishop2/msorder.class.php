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

        $this->fromArray(array(
            'cost' => $cart_cost + $delivery_cost,
            'cart_cost' => $cart_cost,
            'weight' => $weight,
        ));

        return $this->save();
    }

}