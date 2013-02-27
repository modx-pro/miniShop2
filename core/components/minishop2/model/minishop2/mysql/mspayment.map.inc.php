<?php
$xpdo_meta_map['msPayment']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_payments',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'description' => NULL,
    'active' => 1,
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
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
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
      'foreign' => 'payment',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
