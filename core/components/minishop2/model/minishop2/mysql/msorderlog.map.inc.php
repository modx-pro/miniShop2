<?php

$xpdo_meta_map['msOrderLog'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_order_logs',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'user_id' => 0,
            'order_id' => 0,
            'timestamp' => null,
            'action' => '',
            'entry' => '0',
            'ip' => null,
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
                    'default' => 0,
                ],
            'order_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                    'default' => 0,
                ],
            'timestamp' =>
                [
                    'dbtype' => 'datetime',
                    'phptype' => 'datetime',
                    'null' => true,
                ],
            'action' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'entry' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '0',
                ],
            'ip' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => false,
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
            'order_id' =>
                [
                    'alias' => 'order_id',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'order_id' =>
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
            'User' =>
                [
                    'class' => 'modUser',
                    'local' => 'user_id',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
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
                    'foreign' => 'internalKey',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
            'Order' =>
                [
                    'class' => 'msOrder',
                    'local' => 'order_id',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
        ],
];
