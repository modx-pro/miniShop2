<?php
$xpdo_meta_map['msProduct']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'msProduct',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'msProduct',
      'index' => 'index',
    ),
  ),
  'composites' => 
  array (
    'Data' => 
    array (
      'class' => 'msProductData',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Category' => 
    array (
      'class' => 'msCategory',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

if (!in_array('ms2Plugins', get_declared_classes())) {
	require_once (dirname(dirname(__FILE__)) . '/plugins.class.php');
	$this->ms2Plugins = new ms2Plugins($this, array());
}

$xpdo_meta_map['msProduct'] = $this->ms2Plugins->loadMap('msProduct', $xpdo_meta_map['msProduct']);