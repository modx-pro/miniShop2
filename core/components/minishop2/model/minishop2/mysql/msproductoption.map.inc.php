<?php

$xpdo_meta_map['msProductOption'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_product_options',
    'extends' => 'xPDOObject',
    'fields' =>
        [
            'product_id' => null,
            'key' => null,
            'value' => '',
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
                ],
            'key' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '191',
                    'phptype' => 'string',
                    'null' => false,
                ],
            'value' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
        ],
    'indexes' =>
        [
            'product' =>
                [
                    'alias' => 'product',
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
                            'key' =>
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
            'Option' =>
                [
                    'class' => 'msOption',
                    'local' => 'key',
                    'foreign' => 'key',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
        ],
];
