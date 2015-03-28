<?php
$xpdo_meta_map['msOption']= array (
  'package' => 'minishop2',
  'version' => '1.1',
  'table' => 'ms3_options',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'key' => '',
    'caption' => '',
    'type' => '',
    'properties' => NULL,
  ),
  'fieldMeta' => 
  array (
    'key' => 
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
    'properties' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'key' => 
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
    'OptionCategories' => 
    array (
      'class' => 'msCategoryOption',
      'local' => 'id',
      'foreign' => 'option_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'OptionProducts' => 
    array (
      'class' => 'msProductOption',
      'local' => 'key',
      'foreign' => 'key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'validation' => 
  array (
    'rules' => 
    array (
      'key' => 
      array (
        'invalid' => 
        array (
          'type' => 'preg_match',
          'rule' => '/^(?!\\s)[a-zA-Z0-9\\x2d-\\x2f\\x7f-\\xff-_\\s]+(?!\\s)$/',
          'message' => 'ms_option_err_invalid_key',
        ),
        'reserved' => 
        array (
          'type' => 'preg_match',
          'rule' => '/^(?!(id|type|contentType|pagetitle|longtitle|description|alias|link_attributes|published|pub_date|unpub_date|parent|isfolder|introtext|content|richtext|template|menuindex|searchable|cacheable|createdby|createdon|editedby|editedon|deleted|deletedby|deletedon|publishedon|publishedby|menutitle|donthit|privateweb|privatemgr|content_dispo|hidemenu|class_key|context_key|content_type|uri|uri_override|hide_children_in_tree|show_in_tree|article|price|old_price|weight|image|thumb|vendor|made_in|new|popular|favorite|tags|color|size|source)$)/',
          'message' => 'ms_err_reserved_key',
        ),
      ),
    ),
  ),
);
