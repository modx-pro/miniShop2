<?php

$xpdo_meta_map['msDelivery'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_deliveries',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'name' => null,
            'description' => null,
            'price' => '0',
            'weight_price' => 0.0,
            'distance_price' => 0.0,
            'logo' => null,
            'rank' => 0,
            'active' => 1,
            'class' => null,
            'properties' => null,
            'requires' => 'email,receiver',
            'free_delivery_amount' => 0.0,
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
            'weight_price' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'distance_price' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
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
            'requires' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => 'email,receiver',
                ],
            'free_delivery_amount' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
        ],
    'aggregates' =>
        [
            'Orders' =>
                [
                    'class' => 'msOrder',
                    'local' => 'id',
                    'foreign' => 'delivery',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'Payments' =>
                [
                    'class' => 'msDeliveryMember',
                    'local' => 'id',
                    'foreign' => 'delivery_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
        ],
];
