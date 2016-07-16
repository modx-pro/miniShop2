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
    'weight_price' => 0.0,
    'distance_price' => 0.0,
    'logo' => NULL,
    'rank' => 0,
    'active' => 1,
    'class' => NULL,
    'properties' => NULL,
    'requires' => 'email,receiver',
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
      'precision' => '11',
      'phptype' => 'string',
      'null' => true,
      'default' => '0',
    ),
    'weight_price' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => true,
      'default' => 0.0,
    ),
    'distance_price' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => true,
      'default' => 0.0,
    ),
    'logo' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'rank' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => true,
      'default' => 1,
    ),
    'class' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
    'properties' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
    'requires' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => 'email,receiver',
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
    'Payments' => 
    array (
      'class' => 'msDeliveryMember',
      'local' => 'id',
      'foreign' => 'delivery_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
