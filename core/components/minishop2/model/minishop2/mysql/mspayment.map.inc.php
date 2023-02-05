<?php

$xpdo_meta_map['msPayment'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_payments',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'name' => null,
            'description' => null,
            'price' => '0',
            'logo' => null,
            'rank' => 0,
            'active' => 1,
            'class' => null,
            'properties' => null,
        ],
    'fieldMeta' =>
        [
            'name' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                ],
            'description' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'price' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '11',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '0',
                ],
            'logo' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'rank' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'active' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 1,
                ],
            'class' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'properties' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => true,
                ],
        ],
    'aggregates' =>
        [
            'Orders' =>
                [
                    'class' => 'msOrder',
                    'local' => 'id',
                    'foreign' => 'payment',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'Deliveries' =>
                [
                    'class' => 'msDeliveryMember',
                    'local' => 'id',
                    'foreign' => 'payment_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
        ],
];
