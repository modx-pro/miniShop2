<?php

$xpdo_meta_map['msProduct'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'extends' => 'modResource',
    'fields' =>
        [
            'class_key' => 'msProduct',
        ],
    'fieldMeta' =>
        [
            'class_key' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => 'msProduct',
                ],
        ],
    'composites' =>
        [
            'Data' =>
                [
                    'class' => 'msProductData',
                    'local' => 'id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'local',
                ],
            'Categories' =>
                [
                    'class' => 'msCategoryMember',
                    'local' => 'id',
                    'foreign' => 'product_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'Options' =>
                [
                    'class' => 'msProductOption',
                    'local' => 'id',
                    'foreign' => 'product_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
        ],
    'aggregates' =>
        [
            'Category' =>
                [
                    'class' => 'msCategory',
                    'local' => 'parent',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
        ],
];
