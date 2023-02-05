<?php

class GetVendor
{
    public static function getVendorId($modx, $name)
    {
        $criteria = [
            'id' => $name,
            'OR:name:=' => $name
        ];

        $vendor = $modx->getObject('msVendor', $criteria);

        if ($vendor) {
            return $vendor->get('id');
        }

        $vendor = $modx->newObject('msVendor');
        $vendor->set('name', $name);
        $vendor->save();

        return $vendor->get('id');
    }
}
