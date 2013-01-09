<?php
$xpdo_meta_map['msCategory']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'msCategory',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'msCategory',
      'index' => 'index',
    ),
  ),
  'aggregates' => 
  array (
    'Products' => 
    array (
      'class' => 'msProduct',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
