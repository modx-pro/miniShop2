<?php

$xpdo_meta_map['msCategory'] = [
    'package' => 'minishop2',
    'version' => '1.1',
    'extends' => 'modResource',
    'fields' =>
        [
            'class_key' => 'msCategory',
        ],
    'fieldMeta' =>
        [
            'class_key' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => 'msCategory',
                ],
        ],
    'composites' =>
        [
            'OwnProducts' =>
                [
                    'class' => 'msProduct',
                    'local' => 'id',
                    'foreign' => 'parent',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'AlienProducts' =>
                [
                    'class' => 'msCategoryMember',
                    'local' => 'id',
                    'foreign' => 'category_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
            'CategoryOptions' =>
                [
                    'class' => 'msCategoryOption',
                    'local' => 'id',
                    'foreign' => 'category_id',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ],
        ],
];
