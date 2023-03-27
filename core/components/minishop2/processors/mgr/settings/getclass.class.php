<?php

class msClassGetListProcessor extends modProcessor
{
    /**
     * @return string
     */
    public function process()
    {
        $type = $this->getProperty('type');
        $interface = 'ms' . ucfirst($type) . 'Interface';
        $handler = 'ms' . ucfirst($type) . 'Handler';

        $declared = get_declared_classes();
        /** @var miniShop2 $miniShop2 */
        $miniShop2 = $this->modx->getService('miniShop2');
        $miniShop2->loadCustomClasses($type);

        $declared = array_diff(get_declared_classes(), $declared);
        $available = [];
        foreach ($declared as $class) {
            if ($class == $handler || strpos($class, 'Exception') !== false) {
                continue;
            }
            try {
                $object = in_array($type, ['payment', 'delivery'])
                    ? new $class($this->modx->newObject('msProduct'))
                    : new $class($miniShop2);

                if (!empty($object) && is_a($object, $interface)) {
                    $available[] = [
                        'type' => $type,
                        'class' => $class,
                    ];
                }
            } catch (Error $e) {
                // nothing
            }
        }

        return $this->outputArray($available);
    }
}

return 'msClassGetListProcessor';
