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
    public function remove(array $ancestors = [])
    {
        $c = $this->xpdo->newQuery('msProductData');
        $c->command('UPDATE');
        $c->set([
            'vendor' => 0,
        ]);
        $c->where([
            'vendor' => $this->id,
        ]);
        $c->prepare();
        $c->stmt->execute();

        return parent::remove($ancestors);
    }
}
