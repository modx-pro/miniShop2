<?php
$xpdo_meta_map['msDeliveryMember']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_delivery_payments',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'delivery_id' => NULL,
    'payment_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'delivery_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'payment_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'delivery' => 
    array (
      'alias' => 'delivery',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'delivery_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'payment_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Delivery' => 
    array (
      'class' => 'msDelivery',
      'local' => 'delivery_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Payment' => 
    array (
      'class' => 'msPayment',
      'local' => 'payment_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
