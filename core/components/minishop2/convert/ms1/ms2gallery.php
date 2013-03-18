<?php
require dirname(__FILE__) . '/config.inc.php';

// Warning, this will truncate the whole table, so commented
$modx->exec("TRUNCATE {$modx->getTableName('msProductFile')};");

$q = $modx->newQuery('ModGallery');
$q->leftJoin('msProduct', 'Product', 'Product.id = ModGallery.gid AND Product.class_key="msProduct"');
$q->sortby('gid ASC, fileorder', 'ASC');
$q->select('gid as id, file, fileorder as rank');

$errors = '';
if ($q->prepare() && $q->stmt->execute()) {
	$files = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($files as $file) {
		$tmp = explode('/', $file['file']);
		$file['name'] = end($tmp);
		$file['file'] = MODX_BASE_PATH . $file['file'];

		$response = $modx->runProcessor('gallery/upload', $file, array('processors_path' => dirname(__FILE__).'/processors/'));
		if ($response->isError()) {
			$errors .= 'error on id='.$file['id'] .': '.$response->getMessage().'<br/>';
		}
		// Reset processor errors
		$modx->error->reset();
	}
}


echo '<pre>';
print_r($pdoFetch->getTime());
echo '</pre>';
