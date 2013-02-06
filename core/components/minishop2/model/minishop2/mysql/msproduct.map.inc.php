<?php
$xpdo_meta_map['msProduct']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'msProduct',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'msProduct',
      'index' => 'index',
    ),
  ),
  'composites' => 
  array (
    'Data' => 
    array (
      'class' => 'msProductData',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Tags' => 
    array (
      'class' => 'msProductTag',
      'local' => 'id',
      'foreign' => 'product_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Category' => 
    array (
      'class' => 'msCategory',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Categories' => 
    array (
      'class' => 'msCategoryMember',
      'local' => 'id',
      'foreign' => 'product_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
