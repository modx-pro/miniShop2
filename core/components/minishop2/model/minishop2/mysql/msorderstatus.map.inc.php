<?php

$xpdo_meta_map['msOrderStatus'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_order_statuses',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'name' => null,
            'description' => null,
            'color' => '000000',
            'email_user' => 0,
            'email_manager' => 0,
            'subject_user' => '',
            'subject_manager' => '',
            'body_user' => 0,
            'body_manager' => 0,
            'active' => 1,
            'final' => 0,
            'fixed' => 0,
            'rank' => 0,
            'editable' => 1,
        ],
    'fieldMeta' =>
        [
            'name' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => false,
                ],
            'description' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'color' =>
                [
                    'dbtype' => 'char',
                    'precision' => '6',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '000000',
                ],
            'email_user' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'email_manager' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'subject_user' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'subject_manager' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'body_user' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'body_manager' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
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
            'final' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'fixed' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'rank' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => true,
                    'default' => 0,
                ],
            'editable' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 1,
                ],
        ],
    'indexes' =>
        [
            'active' =>
                [
                    'alias' => 'active',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'active' =>
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
            'Orders' =>
                [
                    'class' => 'msOrder',
                    'local' => 'id',
                    'foreign' => 'status',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
        ],
];
