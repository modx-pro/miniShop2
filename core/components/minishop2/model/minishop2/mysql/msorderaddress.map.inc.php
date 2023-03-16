<?php

$xpdo_meta_map['msOrderAddress'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_order_addresses',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'order_id' => null,
            'user_id' => null,
            'createdon' => null,
            'updatedon' => null,
            'receiver' => null,
            'phone' => null,
            'email' => null,
            'country' => null,
            'index' => null,
            'region' => null,
            'city' => null,
            'metro' => null,
            'street' => null,
            'building' => null,
            'entrance' => null,
            'floor' => null,
            'room' => null,
            'comment' => null,
            'text_address' => null,
            'properties' => null,
        ],
    'fieldMeta' =>
        [
            'order_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'user_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
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
            'receiver' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'phone' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '20',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'email' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '191',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'country' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'index' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'region' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'city' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'metro' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'street' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'building' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'entrance' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'floor' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'room' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'text_address' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'comment' =>
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
        ],
    'indexes' =>
        [
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
        ],
];
