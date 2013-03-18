<?php
require dirname(__FILE__) . '/config.inc.php';

// Getting templates of miniShop items
$cats_tpl = array_map('trim', explode(',',$modx->getOption('minishop.categories_tpl')));
$goods_tpl = array_map('trim', explode(',',$modx->getOption('minishop.goods_tpl')));
$kits_tpl = array_map('trim', explode(',',$modx->getOption('minishop.kits_tpl')));
$pdoFetch->addTime('Tpls fetched');

// Converting categories
$exclude = array(0); // resources to exclude
$c = array('template:IN' => $cats_tpl, 'class_key:!=' => 'msCategory', 'id:NOT IN' => $exclude);
$count = $modx->getCount('modResource', $c);
$pdoFetch->addTime('Total categories to convert <b>'.$count.'</b>');
if ($count) {
	$cats = $modx->getCollection('modResource', $c);
	$i = 0;
	/* @var modResource $row */
	foreach ($cats as $row) {
		$row->fromArray(array(
			'class_key' => 'msCategory'
			,'isfolder' => 1
		));
		if ($row->save()) {$i++;}
	}
	$pdoFetch->addTime('Converted <b>'.$i.'</b> categories from '.$count);
	unset($cats, $i);
}

// Retrieving vendors
$vendors = array();
$q = $modx->newQuery('ModGoods');
$q->select('add2'); // Field to retrieve vendors from
$q->groupby('add2');
$i = 0;
if ($q->prepare() && $q->stmt->execute()) {
	$results = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
	foreach ($results as $name) {
		if (empty($name)) {continue;}
		if (!$vendor = $modx->getObject('msVendor', array('name' => $name))) {
			$vendor = $modx->newObject('msVendor', array(
				'name' => $name
			));
			$vendor->save();
		}
		$i++;
		$vendors[$name] = $vendor->get('id');
	}
}
$pdoFetch->addTime('Retrieved <b>'.$i.'</b> vendors');



// Converting products
$sql = "UPDATE {$modx->getTableName('modResource')} SET `class_key` = 'msProduct' WHERE `template` IN (".implode(',',$goods_tpl).") AND `class_key` = 'modResource'";
$modx->exec($sql);

$c = array('class_key' => 'msProduct');
$count = $modx->getCount('msProduct', $c);
$pdoFetch->addTime('Total products to convert <b>'.$count.'</b>');

if ($count) {
	$q = $modx->newQuery('msProduct', $c);
	$q->leftJoin('ModGoods','Data','msProduct.id = Data.gid');
	$q->select($modx->getSelectColumns('msProduct','msProduct'));
	$q->select($modx->getSelectColumns('ModGoods','Data','',array('id'),true));
	//$q->limit(200,100); // you can set limit and offset

	$goods = $modx->getCollection('msProduct', $q);
	$i = 0;
	/* @var modResource $row */
	foreach ($goods as $row) {
		// Getting tags
		$c = $modx->newQuery('ModTags', array('rid' => $row->id));
		$c->select('tag');
		if ($c->prepare() && $c->stmt->execute()) {
			$tags = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
		}

		$row->fromArray(array(
			'class_key' => 'msProduct'
			,'article' => $row->article
			,'price' => $row->price
			,'weight' => $row->weight
			,'tags' => !empty($tags) ? $tags : null
			,'source' => 3																				// miniShop2 default media source id
			,'show_in_tree' => 0
			// You can comment these three items, if not needed
			,'old_price' => $row->add1																	// Old price from add1
			,'vendor' => array_key_exists(trim($row->add2), $vendors) ? $vendors[trim($row->add2)] : 0	// Vendor from add2
			,'size' => array_map('trim', explode('||', $row->add3))										// Sizes from add3
			// There howe you can import TV values
			,'new' => $row->getTVValue('new')
			,'popular' => $row->getTVValue('popular')
			,'favorite' => $row->getTVValue('favorite')
		));
		if ($row->save()) {$i++;}

	}
	$pdoFetch->addTime('Converted <b>'.$i.'</b> products from '.$count);
	unset($goods, $i);
}



// Processing multi-categories
$vendors = array();
$q = $modx->newQuery('ModCategories');
$q->leftJoin('msProduct','Product', 'Product.id = ModCategories.gid' );
$q->select('gid as product_id, cid as category_id');
$i = 0;
if ($q->prepare() && $q->stmt->execute()) {
	$results = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
	$sql = "INSERT INTO {$modx->getTableName('msCategoryMember')} (`product_id`,`category_id`) VALUES ";
	$tmp = array();
	foreach ($results as $v) {
		$tmp[] = "('{$v['product_id']}', '{$v['category_id']}')";
		$i++;
	}
	if (!empty($tmp)) {
		$sql .= implode(', ', $tmp);
		$modx->exec($sql);
		$pdoFetch->addTime('Processed <b>'.$i.'</b> multi-category records');
	}
}


echo '<pre>';
print_r($pdoFetch->getTime());
echo '</pre>';
