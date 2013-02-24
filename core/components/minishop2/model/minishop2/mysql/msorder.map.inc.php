<?php
$xpdo_meta_map['msOrder']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_orders',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'user_id' => NULL,
    'num' => NULL,
    'status' => 1,
    'sum' => NULL,
    'weight' => 0,
    'created' => '0000-00-00 00:00:00',
    'updated' => '0000-00-00 00:00:00',
    'comment' => NULL,
    'delivery' => 0,
    'payment' => 0,
    'address' => 0,
  ),
  'fieldMeta' => 
  array (
    'user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'num' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
    ),
    'status' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '2',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
    'sum' => 
    array (
      'dbtype' => 'float',
      'precision' => '10,2',
      'phptype' => 'float',
      'null' => false,
    ),
    'weight' => 
    array (
      'dbtype' => 'float',
      'precision' => '10,3',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'created' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => '0000-00-00 00:00:00',
    ),
    'updated' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => '0000-00-00 00:00:00',
    ),
    'comment' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'delivery' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'payment' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'address' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'status' => 
    array (
      'alias' => 'status',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'status' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'user_id' => 
    array (
      'alias' => 'user_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'user_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
