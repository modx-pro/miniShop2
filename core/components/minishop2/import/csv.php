<?php

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/config.inc.php';
require_once MODX_BASE_PATH . 'index.php';

if (XPDO_CLI_MODE) {
	$file = @$argv[1];
	$fields = @$argv[2];
	$update = (bool) !empty($argv[3]);
	$key = @$argv[4];
	$is_debug = (bool) !empty($argv[5]);
	$delimeter = @$argv[6];
}
else {
	$file = @$_REQUEST['file'];
	$fields = @$_REQUEST['fields'];
	$update = (bool) !empty($_REQUEST['update']);
	$key = @$_REQUEST['key'];
	$is_debug = (bool) !empty($_REQUEST['debug']);
	$delimeter = @$_REQUEST['delimeter'];
}

// Load main services
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->setLogLevel($is_debug ? modX::LOG_LEVEL_INFO : modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');
$modx->lexicon->load('minishop2:default');
$modx->lexicon->load('minishop2:manager');

// Time limit
set_time_limit(600);
$tmp = 'Trying to set time limit = 600 sec: ';
$tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';
$modx->log(modX::LOG_LEVEL_INFO,  $tmp);

// Check required options
if (empty($fields)) {
	$modx->log(modX::LOG_LEVEL_ERROR, 'You must specify the parameter "fields". It needed for parse of your file.');
	exit;
}
if (empty($key)) {
	$modx->log(modX::LOG_LEVEL_ERROR, 'You must specify the parameter "key". It needed for check for duplicates.');
	exit;
}
$keys = array_map('trim', explode(',', strtolower($fields)));
$tv_enabled = false;
foreach ($keys as $v) {
	if (preg_match('/^tv(\d)$/', $v)) {
		$tv_enabled = true;
		break;
	}
}
if (empty($delimeter)) {$delimeter = ';';}

// Check file
if (empty($file)) {
	$error = 'You must specify an file in the ';
	$error .= XPDO_CLI_MODE ? 'first parameter of console call' : '$_GET["file"] parameter';
	$error .= '!';
	$modx->log(modX::LOG_LEVEL_ERROR, $error);
	exit;
}
elseif (!preg_match('/\.csv$/i', $file)) {
	$modx->log(modX::LOG_LEVEL_ERROR, 'Wrong file extension. File must be an *.csv.');
	exit;
}

$file = str_replace('//', '/', MODX_BASE_PATH . $file);
if (!file_exists($file)) {
	$modx->log(modX::LOG_LEVEL_ERROR, 'File not found at '.$file.'.');
	exit;
}

// Import!
$handle = fopen($file, "r");
$rows = $created = $updated = 0;
while (($csv = fgetcsv($handle, 0, $delimeter)) !== false) {
	$rows ++;
	$data = $gallery = array();
	$modx->error->reset();
	$modx->log(modX::LOG_LEVEL_INFO, "Raw data for import: \n".print_r($csv,1));
	foreach ($keys as $k => $v) {
		if (!isset($csv[$k])) {
			exit('Field "' . $v . '" not exists in file. Please fix import file or parameter "fields".');
		}
		if ($v == 'gallery') {
			$gallery[] = $csv[$k];
		}
		elseif (isset($data[$v]) && !is_array($data[$v])) {
			$data[$v] = array($data[$v], $csv[$k]);
		}
		elseif (isset($data[$v]) && is_array($data[$v])) {
			$data[$v][] = $csv[$k];
		}
		else {
			$data[$v] = $csv[$k];
		}
	}
	$is_product = false;

	// Set default values
	if (empty($data['class_key'])) {$data['class_key'] = 'msProduct';}
	if (empty($data['context_key'])) {
		if (isset($data['parent']) && $parent = $modx->getObject('modResource', $data['parent'])) {
			$data['context_key'] = $parent->get('context_key');
		}
		elseif (isset($modx->resource) && isset($modx->context)) {
			$data['context_key'] = $modx->context->key;
		}
		else {
			$data['context_key'] = 'web';
		}
	}
	$data['tvs'] = $tv_enabled;
	$modx->log(modX::LOG_LEVEL_INFO, "Array with importing data: \n" . print_r($data, 1));

	// Duplicate check
	$q = $modx->newQuery($data['class_key']);
	$q->select($data['class_key'].'.id');
	if (strtolower($data['class_key']) == 'msproduct') {
		$q->innerJoin('msProductData', 'Data', $data['class_key'].'.id = Data.id');
		$is_product = true;
	}
	$tmp = $modx->getFields($data['class_key']);
	if (isset($tmp[$key])) {
		$q->where(array($key => $data[$key]));
	}
	elseif ($is_product) {
		$q->where(array('Data.'.$key => $data[$key]));
	}
	$q->prepare();
	$modx->log(modX::LOG_LEVEL_INFO, "SQL query for check for duplicate: \n" . $q->toSql());

	/** @var modResource $exists */
	if ($exists = $modx->getObject($data['class_key'], $q)) {
		$modx->log(modX::LOG_LEVEL_INFO, "Key $key = $data[$key] has duplicate.");
		if (!$update) {
			$modx->log(modX::LOG_LEVEL_ERROR, "Skipping line with $key = \"$data[$key]\" because update is disabled.");
			if ($is_debug) {
				$modx->log(modX::LOG_LEVEL_INFO, 'You in debug mode, so we process only 1 row. Time: '.number_format(microtime(true) - $modx->startTime, 7) . " s");
				exit;
			}
			else {continue;}
		}
		else {
			$action = 'update';
			$data['id'] = $exists->id;
		}
	}
	else {
		$action = 'create';
	}

	// Create or update resource
	/** @var modProcessorResponse $response */
	$response = $modx->runProcessor('resource/'.$action, $data);
	if ($response->isError()) {
		$modx->log(modX::LOG_LEVEL_ERROR, "Error on $action: \n". print_r($response->getAllErrors(), 1));
	}
	else {
		if ($action == 'update') {$updated ++;}
		else {$created ++;}

		$resource = $response->getObject();
		$modx->log(modX::LOG_LEVEL_INFO, "Successful $action: \n". print_r($resource, 1));

		// Process gallery images, if exists
		if (!empty($gallery)) {
			$modx->log(modX::LOG_LEVEL_INFO, "Importing images: \n". print_r($gallery, 1));
			foreach ($gallery as $v) {
				if (empty($v)) {continue;}
				$image = str_replace('//', '/', MODX_BASE_PATH . $v);
				if (!file_exists($image)) {
					$modx->log(modX::LOG_LEVEL_ERROR, "Could not import image \"$v\" to gallery. File \"$image\" not found on server.");
				}
				else {
					$response = $modx->runProcessor('gallery/upload',
						array('id' => $resource['id'], 'name' => $v, 'file' => $image),
						array('processors_path' => MODX_CORE_PATH.'components/minishop2/processors/mgr/')
					);
					if ($response->isError()) {
						$modx->log(modX::LOG_LEVEL_ERROR, "Error on upload \"$v\": \n". print_r($response->getAllErrors(), 1));
					}
					else {
						$modx->log(modX::LOG_LEVEL_INFO, "Successful upload  \"$v\": \n". print_r($response->getObject(), 1));
					}
				}
			}
		}
	}

	if ($is_debug) {
		$modx->log(modX::LOG_LEVEL_INFO, 'You in debug mode, so we process only 1 row. Time: '.number_format(microtime(true) - $modx->startTime, 7) . " s");
		exit;
	}
}
fclose($handle);

if (!XPDO_CLI_MODE) {echo '<pre>';}
echo "\nImport complete in ".number_format(microtime(true) - $modx->startTime, 7) . " s\n";
echo "\nTotal rows:	$rows\n";
echo "Created:	$created\n";
echo "Updated:	$updated\n";
if (!XPDO_CLI_MODE) {echo '</pre>';}