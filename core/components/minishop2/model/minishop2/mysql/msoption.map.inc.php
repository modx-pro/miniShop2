<?php
$xpdo_meta_map['msOption']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms3_options',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'key' => '',
    'caption' => '',
    'type' => '',
    'properties' => NULL,
  ),
  'fieldMeta' => 
  array (
    'key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'caption' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'fulltext',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'properties' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'key' => 
    array (
      'alias' => 'type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'caption_ft' => 
    array (
      'alias' => 'caption_ft',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'caption' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'OptionCategories' => 
    array (
      'class' => 'msCategoryOption',
      'local' => 'id',
      'foreign' => 'option_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'OptionProducts' => 
    array (
      'class' => 'msProductOption',
      'local' => 'key',
      'foreign' => 'key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
