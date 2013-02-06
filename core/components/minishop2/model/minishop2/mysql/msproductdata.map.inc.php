<?php
$xpdo_meta_map['msProductData']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_products',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'article' => NULL,
    'price' => '0',
    'new_price' => '0',
    'weight' => '0',
    'color' => NULL,
    'remains' => 0,
    'reserved' => 0,
    'image' => NULL,
    'vendor' => 0,
    'made_in' => NULL,
    'tags' => NULL,
  ),
  'fieldMeta' => 
  array (
    'article' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'int',
      'null' => true,
    ),
    'price' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'int',
      'null' => false,
      'default' => '0',
    ),
    'new_price' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '0',
    ),
    'weight' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '0',
    ),
    'color' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
    'remains' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'reserved' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'image' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'vendor' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'made_in' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'tags' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'vendor' => 
    array (
      'alias' => 'vendor',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'vendor' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'price' => 
    array (
      'alias' => 'price',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'price' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'new_price' => 
    array (
      'alias' => 'new_price',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'new_price' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'color' => 
    array (
      'alias' => 'color',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'color' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'made_in' => 
    array (
      'alias' => 'made_in',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'made_in' => 
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
    'Product' => 
    array (
      'class' => 'msProduct',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Vendor' => 
    array (
      'class' => 'msVendor',
      'local' => 'vendor',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

if (!in_array('ms2Plugins', get_declared_classes())) {
	require_once (dirname(dirname(__FILE__)) . '/plugins.class.php');
	$this->ms2Plugins = new ms2Plugins($this, array());
}

$xpdo_meta_map['msProductData'] = $this->ms2Plugins->loadMap('msProductData', $xpdo_meta_map['msProductData']);