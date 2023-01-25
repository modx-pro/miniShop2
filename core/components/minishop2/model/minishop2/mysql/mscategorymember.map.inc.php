<?php

$xpdo_meta_map['msCategoryMember'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_product_categories',
    'extends' => 'xPDOObject',
    'fields' =>
        [
            'product_id' => null,
            'category_id' => null,
        ],
    'fieldMeta' =>
        [
            'product_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                    'index' => 'pk',
                ],
            'category_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                    'index' => 'pk',
                ],
        ],
    'indexes' =>
        [
            'product' =>
                [
                    'alias' => 'product',
                    'primary' => true,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'product_id' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                            'category_id' =>
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
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'Category' =>
                [
                    'class' => 'msCategory',
                    'local' => 'category_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
        ],
];
