<?php
$xpdo_meta_map['msProductLink']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_product_links',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'link' => NULL,
    'master' => NULL,
    'slave' => NULL,
  ),
  'fieldMeta' => 
  array (
    'link' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
    'master' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
    'slave' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'type' => 
    array (
      'alias' => 'link',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'link' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'master' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'slave' => 
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
    'Link' => 
    array (
      'class' => 'msLink',
      'local' => 'link',
      'foreign' => 'id',
      'owner' => 'foreign',
      'cardinality' => 'one',
    ),
    'Master' => 
    array (
      'class' => 'msProduct',
      'local' => 'master',
      'foreign' => 'id',
      'owner' => 'foreign',
      'cardinality' => 'one',
    ),
    'Slave' => 
    array (
      'class' => 'msProduct',
      'local' => 'slave',
      'foreign' => 'id',
      'owner' => 'foreign',
      'cardinality' => 'one',
    ),
  ),
);
