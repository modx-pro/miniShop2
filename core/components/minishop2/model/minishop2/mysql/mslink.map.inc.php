<?php
$xpdo_meta_map['msLink']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_links',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'type' => NULL,
    'name' => NULL,
    'description' => NULL,
  ),
  'fieldMeta' => 
  array (
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'type' => 
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
  ),
  'composites' => 
  array (
    'Links' => 
    array (
      'class' => 'msProductLink',
      'local' => 'id',
      'foreign' => 'link',
      'owner' => 'local',
      'cardinality' => 'many',
    ),
  ),
);
