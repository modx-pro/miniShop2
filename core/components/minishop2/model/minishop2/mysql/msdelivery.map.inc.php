<?php
$xpdo_meta_map['msDelivery']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_deliveries',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'description' => NULL,
    'price' => '0',
    'add_price' => '0',
    'active' => 1,
    'payments' => NULL,
    'class' => NULL,
  ),
  'fieldMeta' => 
  array (
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
    'price' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'float',
      'null' => false,
      'default' => '0',
    ),
    'add_price' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'float',
      'null' => false,
      'default' => '0',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
    'payments' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
    'class' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'aggregates' => 
  array (
    'Orders' => 
    array (
      'class' => 'msOrder',
      'local' => 'id',
      'foreign' => 'delivery',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
