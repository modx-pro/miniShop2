<?php
$xpdo_meta_map['msVendor']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms2_vendors',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'country' => NULL,
    'logo' => NULL,
    'address' => NULL,
    'phone' => NULL,
    'fax' => NULL,
    'description' => NULL,
    'properties' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'country' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
    'logo' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'address' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'phone' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => true,
    ),
    'fax' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => true,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
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
    'Products' => 
    array (
      'class' => 'msProduct',
      'local' => 'id',
      'foreign' => 'vendor',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);

if (!in_array('ms2Plugins', get_declared_classes())) {
	require_once (dirname(dirname(__FILE__)) . '/plugins.class.php');
	$this->ms2Plugins = new ms2Plugins($this, array());
}

$xpdo_meta_map['msVendor'] = $this->ms2Plugins->loadMap('msVendor', $xpdo_meta_map['msVendor']);