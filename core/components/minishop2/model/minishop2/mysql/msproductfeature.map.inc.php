<?php
$xpdo_meta_map['msProductFeature']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms3_product_features',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'feature_id' => 0,
    'product_id' => 0,
    'value' => NULL,
  ),
  'fieldMeta' => 
  array (
    'feature_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'product_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'value' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'index' => 'fulltext',
    ),
  ),
  'indexes' => 
  array (
    'category_feature' => 
    array (
      'alias' => 'product_feature',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'feature_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'product_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'value_ft' => 
    array (
      'alias' => 'value_ft',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'value' => 
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
    'Feature' => 
    array (
      'class' => 'msFeature',
      'local' => 'feature_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Product' => 
    array (
      'class' => 'msProduct',
      'local' => 'product_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
