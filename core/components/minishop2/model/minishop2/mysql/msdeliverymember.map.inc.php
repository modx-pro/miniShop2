<?php

$xpdo_meta_map['msDeliveryMember'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_delivery_payments',
    'extends' => 'xPDOObject',
    'fields' =>
        [
            'delivery_id' => null,
            'payment_id' => null,
        ],
    'fieldMeta' =>
        [
            'delivery_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                    'index' => 'pk',
                ],
            'payment_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                    'index' => 'pk',
                ],
        ],
    'indexes' =>
        [
            'delivery' =>
                [
                    'alias' => 'delivery',
                    'primary' => true,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'delivery_id' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                            'payment_id' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
        ],
    'aggregates' =>
        [
            'Delivery' =>
                [
                    'class' => 'msDelivery',
                    'local' => 'delivery_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'Payment' =>
                [
                    'class' => 'msPayment',
                    'local' => 'payment_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
        ],
];
