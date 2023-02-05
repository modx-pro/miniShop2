<?php

$xpdo_meta_map['msOrderProduct'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_order_products',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'product_id' => null,
            'order_id' => null,
            'name' => null,
            'count' => 1,
            'price' => 0.0,
            'weight' => 0.0,
            'cost' => 0.0,
            'options' => null,
            'properties' => null,
        ],
    'fieldMeta' =>
        [
            'product_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => false,
                ],
            'order_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => false,
                ],
            'name' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'count' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => true,
                    'default' => 1,
                ],
            'price' =>
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
            'cost' =>
                [
                    'dbtype' => 'decimal',
                    'precision' => '12,2',
                    'phptype' => 'float',
                    'null' => true,
                    'default' => 0.0,
                ],
            'options' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'json',
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
            'product_id' =>
                [
                    'alias' => 'product_id',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'product_id' =>
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
            'Product' =>
                [
                    'class' => 'msProduct',
                    'local' => 'product_id',
                    'foreign' => 'id',
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
