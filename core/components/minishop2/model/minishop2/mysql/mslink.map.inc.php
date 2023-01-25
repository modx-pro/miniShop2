<?php

$xpdo_meta_map['msLink'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_links',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'type' => null,
            'name' => null,
            'description' => null,
        ],
    'fieldMeta' =>
        [
            'type' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => false,
                ],
            'name' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                ],
            'description' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
        ],
    'indexes' =>
        [
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
        ],
    'composites' =>
        [
            'Links' =>
                [
                    'class' => 'msProductLink',
                    'local' => 'id',
                    'foreign' => 'link',
                    'owner' => 'local',
                    'cardinality' => 'many',
                ],
        ],
];
