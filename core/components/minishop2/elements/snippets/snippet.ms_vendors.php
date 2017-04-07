<?
$q = $modx->newQuery('msVendor');
$q->innerJoin('msProductData', 'msProductData', '`msProductData`.`vendor` = `msVendor`.`id`');
$q->innerJoin('msProduct', 'msProduct', array(
	'`msProductData`.`id` = `msProduct`.`id`',
	'msProduct.deleted' => 0,
	'msProduct.published' => 1
));
$q->groupby('msVendor.id');
$q->sortby('name','ASC');
$q->select(array('msVendor.id', 'name'));
$rows = '';
if ($q->prepare() && $q->stmt->execute()) {
	while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
		$rows .= $modx->getChunk($tplRow, $row);
	}
}
return $modx->getChunk($tplOuter, array('rows' => $rows));
?>
