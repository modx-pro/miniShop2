<?php
$xpdo_meta_map['msDiscountCard']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_discount_card',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'uid' => 0,
    'user_id' => 0,
    'discount' => 0,
    'discount_type' => 1,
    'public' => 0,
  ),
  'fieldMeta' => 
  array (
    'uid' => 
    array (
      'dbtype' => 'bigint',
      'precision' => '20',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'unique',
    ),
    'user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
      'index' => 'index',
    ),
    'discount' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'discount_type' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
    'public' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'uid' => 
    array (
      'alias' => 'uid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'uid' => 
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
          'null' => true,
        ),
      ),
    ),
    'discount' => 
    array (
      'alias' => 'discount',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'discount' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'discount_type' => 
    array (
      'alias' => 'discount_type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'discount_type' => 
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
    'dcCoOwner' => 
    array (
      'class' => 'msDiscountCardCoOwners',
      'local' => 'id',
      'foreign' => 'dc_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'dcOwner' => 
    array (
      'class' => 'modUser',
      'local' => 'user_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
