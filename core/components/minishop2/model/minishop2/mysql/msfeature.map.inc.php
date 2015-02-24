<?php
$xpdo_meta_map['msFeature']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms3_features',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'caption' => '',
    'type' => '',
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'caption' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'fulltext',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'name' => 
    array (
      'alias' => 'type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'caption_ft' => 
    array (
      'alias' => 'caption_ft',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'caption' => 
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
    'FeatureCategories' => 
    array (
      'class' => 'msCategoryFeature',
      'local' => 'id',
      'foreign' => 'feature_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'FeatureProducts' => 
    array (
      'class' => 'msProductFeature',
      'local' => 'id',
      'foreign' => 'feature_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
