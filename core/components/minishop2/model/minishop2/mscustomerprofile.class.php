<?php

class msCustomerProfile extends xPDOObject
{
    /** @var miniShop2 $miniShop2 */
    public $miniShop2;


    /**
     * @param xPDO $xpdo
     * @param string $className
     * @param mixed $criteria
     * @param bool $cacheFlag
     *
     * @return msCustomerProfile
     */
    public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag = true)
    {
        /** @var $instance msCustomerProfile */
        $instance = parent::load($xpdo, 'msCustomerProfile', $criteria, $cacheFlag);

        if (!is_object($instance) || !($instance instanceof $className)) {
            if (is_numeric($criteria) || (is_array($criteria) && !empty($criteria['id']))) {
                $id = is_numeric($criteria) ? $criteria : $criteria['id'];
                if ($xpdo->getCount('modUser', array('id' => $id))) {
                    $instance = $xpdo->newObject('msCustomerProfile');
                    $time = time();
                    $instance->set('id', $id);
                    $instance->fromArray(array(
                        'createdon' => $time,
                        'referrer_code' => md5($id . $time),
                    ));
                    $instance->save();
                }
            }
        }

        return $instance;
    }

}