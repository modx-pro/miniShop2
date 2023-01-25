<?php

$xpdo_meta_map['msVendor'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'table' => 'ms2_vendors',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'name' => null,
            'resource' => 0,
            'country' => null,
            'logo' => null,
            'address' => null,
            'phone' => null,
            'fax' => null,
            'email' => null,
            'description' => null,
            'properties' => null,
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
            'resource' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'country' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'logo' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'address' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'phone' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '20',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'fax' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '20',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'email' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'description' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'properties' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => true,
                ],
        ],
    'aggregates' =>
        [
            'Products' =>
                [
                    'class' => 'msProduct',
                    'local' => 'id',
                    'foreign' => 'vendor',
                    'cardinality' => 'many',
                    'owner' => 'foreign',
                ],
            'Resource' =>
                [
                    'class' => 'modResource',
                    'local' => 'resource',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'local',
                ],
        ],
];
