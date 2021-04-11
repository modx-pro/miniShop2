<?php

$xpdo_meta_map['msOrderAddress'] = array(
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_order_addresses',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        array(
            'user_id' => null,
            'createdon' => null,
            'updatedon' => null,
            'receiver' => null,
            'phone' => null,
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
        ),
    'fieldMeta' =>
        array(
            'user_id' =>
                array(
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                ),
            'createdon' =>
                array(
                    'dbtype' => 'datetime',
                    'phptype' => 'datetime',
                    'null' => true,
                ),
            'updatedon' =>
                array(
                    'dbtype' => 'datetime',
                    'phptype' => 'datetime',
                    'null' => true,
                ),
            'receiver' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'phone' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '20',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'country' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'index' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'region' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'city' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'metro' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'street' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'building' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'entrance' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'floor' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'room' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'text_address' =>
                array(
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'comment' =>
                array(
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'properties' =>
                array(
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => true,
                ),
        ),
    'indexes' =>
        array(
            'user_id' =>
                array(
                    'alias' => 'user_id',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        array(
                            'user_id' =>
                                array(
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                        ),
                ),
        ),
    'aggregates' =>
        array(
            'User' =>
                array(
                    'class' => 'modUser',
                    'local' => 'user_id',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ),
            'UserProfile' =>
                array(
                    'class' => 'modUserProfile',
                    'local' => 'user_id',
                    'foreign' => 'internalKey',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ),
            'CustomerProfile' =>
                array(
                    'class' => 'msCustomerProfile',
                    'local' => 'user_id',
                    'foreign' => 'internalKey',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ),
        ),
);
