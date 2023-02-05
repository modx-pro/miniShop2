<?php

$xpdo_meta_map['msCustomerProfile'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_customer_profiles',
    'extends' => 'xPDOObject',
    'fields' =>
        [
            'id' => null,
            'account' => 0.0,
            'spent' => 0.0,
            'createdon' => 'CURRENT_TIMESTAMP',
            'referrer_id' => 0,
            'referrer_code' => '',
        ],
    'fieldMeta' =>
        [
            'id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => false,
                    'index' => 'pk',
                ],
            'account' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'spent' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'createdon' =>
                [
                    'dbtype' => 'timestamp',
                    'phptype' => 'datetime',
                    'null' => true,
                    'default' => 'CURRENT_TIMESTAMP',
                ],
            'referrer_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => true,
                    'default' => 0,
                    'index' => 'index',
                ],
            'referrer_code' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                    'index' => 'index',
                ],
        ],
    'indexes' =>
        [
            'id' =>
                [
                    'alias' => 'id',
                    'primary' => true,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'id' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'referrer_id' =>
                [
                    'alias' => 'referrer_id',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'referrer_id' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'referrer_code' =>
                [
                    'alias' => 'referrer_code',
                    'primary' => false,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'referrer_code' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'spent' =>
                [
                    'alias' => 'spent',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'spent' =>
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
                    'local' => 'id',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
        ],
];
