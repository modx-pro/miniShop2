<?php

/**
 * @property int id
 */
class msVendor extends xPDOSimpleObject
{
    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = array())
    {
        $c = $this->xpdo->newQuery('msProductData');
        $c->command('UPDATE');
        $c->set(array(
            'vendor' => 0,
        ));
        $c->where(array(
            'vendor' => $this->id,
        ));
        $c->prepare();
        $c->stmt->execute();

        return parent::remove($ancestors);
    }

}