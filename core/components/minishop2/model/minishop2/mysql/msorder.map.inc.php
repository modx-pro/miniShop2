<?php

$xpdo_meta_map['msOrder'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_orders',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'user_id' => null,
            'session_id' => null,
            'createdon' => null,
            'updatedon' => null,
            'num' => '',
            'cost' => 0.0,
            'cart_cost' => 0.0,
            'delivery_cost' => 0.0,
            'weight' => 0.0,
            'status' => 0,
            'delivery' => 0,
            'payment' => 0,
            'context' => 'web',
            'order_comment' => null,
            'properties' => null,
            'type' => 0,
        ],
    'fieldMeta' =>
        [
            'user_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                ],
            'session_id' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '32',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'createdon' =>
                [
                    'dbtype' => 'datetime',
                    'phptype' => 'datetime',
                    'null' => true,
                ],
            'updatedon' =>
                [
                    'dbtype' => 'datetime',
                    'phptype' => 'datetime',
                    'null' => true,
                ],
            'num' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '20',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'cost' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'cart_cost' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'delivery_cost' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'weight' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '13,3',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'status' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'delivery' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'payment' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'context' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => 'web',
                ],
            'order_comment' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'properties' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => true,
                ],
            'type' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '3',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => true,
                    'default' => 0,
                ],
        ],
    'indexes' =>
        [
            'user_id' =>
                [
                    'alias' => 'user_id',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'user_id' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'session_id' =>
                [
                    'alias' => 'session_id',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'session_id' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'status' =>
                [
                    'alias' => 'status',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'status' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'type' =>
                [
                    'alias' => 'type',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'type' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
        ],
    'composites' =>
        [
            'Products' =>
                [
                    'class' => 'msOrderProduct',
                    'local' => 'id',
                    'foreign' => 'order_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'Log' =>
                [
                    'class' => 'msOrderLog',
                    'local' => 'id',
                    'foreign' => 'order_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'Address' =>
                [
                    'class' => 'msOrderAddress',
                    'local' => 'id',
                    'foreign' => 'order_id',
                    'cardinality' => 'one',
                    'owner' => 'local',
                ],
        ],
    'aggregates' =>
        [
            'User' =>
                [
                    'class' => 'modUser',
                    'local' => 'user_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'UserProfile' =>
                [
                    'class' => 'modUserProfile',
                    'local' => 'user_id',
                    'foreign' => 'internalKey',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
            'CustomerProfile' =>
                [
                    'class' => 'msCustomerProfile',
                    'local' => 'user_id',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
            'Status' =>
                [
                    'class' => 'msOrderStatus',
                    'local' => 'status',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'Delivery' =>
                [
                    'class' => 'msDelivery',
                    'local' => 'delivery',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'Payment' =>
                [
                    'class' => 'msPayment',
                    'local' => 'payment',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
        ],
];
