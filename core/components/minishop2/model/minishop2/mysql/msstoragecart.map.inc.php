<?php

$xpdo_meta_map['msStorageCart'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_storage_carts',
    'extends' => 'xPDOSimpleObject',
    'tableMeta' => [
        'engine' => 'InnoDB',
    ],
    'fields' => [
        'user_id' => '',
        'createdon' => null,
        'updatedon' => null,
    ],
    'fieldMeta' => [
        'user_id' => [
            'dbtype' => 'varchar',
            'precision' => '50',
            'phptype' => 'string',
            'null' => false,
        ],
        'createdon' => [
            'dbtype' => 'datetime',
            'phptype' => 'datetime',
            'null' => true,
        ],
        'updatedon' => [
            'dbtype' => 'datetime',
            'phptype' => 'datetime',
            'null' => true,
        ],
    ],
    'indexes' => [
        'user_id' => [
            'alias' => 'user_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'user_id' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ]
            ]
        ],
    ],
    'composites' => [
        'Items' => [
            'class' => 'msStorageCartItem',
            'local' => 'id',
            'foreign' => 'cart_id',
            'cardinality' => 'many',
            'owner' => 'local',
        ],
    ],
    'aggregates' => []
];
