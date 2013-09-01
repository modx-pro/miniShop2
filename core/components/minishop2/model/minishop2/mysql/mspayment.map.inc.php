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
    'price' => '0',
    'logo' => NULL,
    'rank' => 0,
    'active' => 1,
    'class' => NULL,
    'properties' => NULL,
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
      'attributes' => 'unsigned',
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
    'Deliveries' => 
    array (
      'class' => 'msDeliveryMember',
      'local' => 'id',
      'foreign' => 'payment_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
