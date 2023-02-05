<?php

$xpdo_meta_map['msProductLink'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_product_links',
    'extends' => 'xPDOObject',
    'fields' =>
        [
            'link' => null,
            'master' => null,
            'slave' => null,
        ],
    'fieldMeta' =>
        [
            'link' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => false,
                    'index' => 'pk',
                ],
            'master' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => false,
                    'index' => 'pk',
                ],
            'slave' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'attributes' => 'unsigned',
                    'null' => false,
                    'index' => 'pk',
                ],
        ],
    'indexes' =>
        [
            'type' =>
                [
                    'alias' => 'link',
                    'primary' => true,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'link' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                            'master' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                            'slave' =>
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
            'Link' =>
                [
                    'class' => 'msLink',
                    'local' => 'link',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
            'Master' =>
                [
                    'class' => 'msProduct',
                    'local' => 'master',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
            'Slave' =>
                [
                    'class' => 'msProduct',
                    'local' => 'slave',
                    'foreign' => 'id',
                    'owner' => 'foreign',
                    'cardinality' => 'one',
                ],
        ],
];
