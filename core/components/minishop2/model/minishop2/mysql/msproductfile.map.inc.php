<?php

$xpdo_meta_map['msProductFile'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_product_files',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'product_id' => null,
            'source' => 1,
            'parent' => 0,
            'name' => '',
            'description' => null,
            'path' => '',
            'file' => null,
            'type' => null,
            'createdon' => null,
            'createdby' => 0,
            'rank' => 0,
            'url' => '',
            'properties' => null,
            'hash' => '',
            'active' => 1,
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
            'source' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 1,
                ],
            'parent' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'name' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'description' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'path' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'file' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                ],
            'type' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'createdon' =>
                [
                    'dbtype' => 'datetime',
                    'phptype' => 'datetime',
                    'null' => true,
                ],
            'createdby' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'rank' =>
                [
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'url' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'properties' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => true,
                ],
            'hash' =>
                [
                    'dbtype' => 'char',
                    'precision' => '40',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                    'index' => 'index',
                ],
            'active' =>
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
            'type' =>
                [
                    'alias' => 'type',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'type' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'parent' =>
                [
                    'alias' => 'parent',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'parent' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
            'hash' =>
                [
                    'alias' => 'hash',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        [
                            'hash' =>
                                [
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ],
                        ],
                ],
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
    'composites' =>
        [
            'Children' =>
                [
                    'class' => 'msProductFile',
                    'local' => 'id',
                    'foreign' => 'parent',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
        ],
    'aggregates' =>
        [
            'Parent' =>
                [
                    'class' => 'msProductFile',
                    'local' => 'parent',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'Product' =>
                [
                    'class' => 'msProduct',
                    'local' => 'product_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'Source' =>
                [
                    'class' => 'modMediaSource',
                    'local' => 'source',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
        ],
];
