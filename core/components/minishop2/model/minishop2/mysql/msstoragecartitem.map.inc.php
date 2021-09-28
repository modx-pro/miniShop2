<?php

$xpdo_meta_map['msStorageCartItem'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_storage_cart_items',
    'extends' => 'xPDOSimpleObject',
    'tableMeta' => [
        'engine' => 'InnoDB',
    ],
    'fields' => [
        'cart_id' => 0,
        'user_id' => '',
        'product_key' => '',
        'product_id' => 0,
        'price' => 0.0,
        'old_price' => 0.0,
        'discount_price' => 0.0,
        'discount_cost' => 0.0,
        'weight' => 0.0,
        'count' => 0,
        'options' => null,
        'ctx' => '',
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
        'cart_id' => [
            'dbtype' => 'int',
            'precision' => '10',
            'attributes' => 'unsigned',
            'phptype' => 'integer',
            'null' => false,
        ],
        'product_key' => [
            'dbtype' => 'varchar',
            'precision' => '32',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ],
        'product_id' => [
            'dbtype' => 'int',
            'precision' => '10',
            'attributes' => 'unsigned',
            'phptype' => 'integer',
            'null' => false,
        ],
        'price' => [
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => true,
            'default' => 0.0,
        ],
        'old_price' => [
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => true,
            'default' => 0.0,
        ],
        'discount_price' => [
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => true,
            'default' => 0.0,
        ],
        'discount_cost' => [
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => true,
            'default' => 0.0,
        ],
        'weight' => [
            'dbtype' => 'decimal',
            'precision' => '13,3',
            'phptype' => 'float',
            'null' => true,
            'default' => 0.0,
        ],
        'count' => [
            'dbtype' => 'int',
            'precision' => '10',
            'attributes' => 'unsigned',
            'phptype' => 'integer',
            'null' => true,
            'default' => 1,
        ],
        'options' => [
            'dbtype' => 'text',
            'phptype' => 'json',
            'null' => true,
        ],
        'ctx' => [
            'dbtype' => 'varchar',
            'precision' => '191',
            'phptype' => 'string',
            'null' => true,
            'default' => 'web',
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
            ],
        ],
        'product_id' => [
            'alias' => 'product_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'product_id' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ]
            ],
        ],
        'cart_id' => [
            'alias' => 'cart_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'cart_id' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'product_key' => [
            'alias' => 'product_key',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'product_key' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
    ],
    'composites' => [],
    'aggregates' => [
        'Product' => [
            'class' => 'msProduct',
            'local' => 'product_id',
            'foreign' => 'id',
            'owner' => 'foreign',
            'cardinality' => 'one',
        ],
        'Cart' => [
            'class' => 'msStorageCart',
            'local' => 'cart_id',
            'foreign' => 'id',
            'owner' => 'foreign',
            'cardinality' => 'one',
        ],
    ],
];
