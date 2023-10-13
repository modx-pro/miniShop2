<?php

class ImportCSV
{
    /**
     * @var modX
     */
    private $modx;
    /**
     * @var miniShop2
     */
    private $miniShop2;
    private $rows = 0;
    private $created = 0;
    private $updated = 0;

    private $params;

    public function __construct(modX $modx)
    {
        $this->modx = $modx;
        $this->miniShop2 = $this->modx->getService('miniShop2');
        // Time limit
        set_time_limit(600);
        $tmp = 'Trying to set time limit = 600 sec: ';
        $tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';
        $this->modx->log(modX::LOG_LEVEL_INFO, $tmp);
    }

    public function process($params)
    {
        $this->params['file'] = @$params['file'];
        $this->params['fields'] = @$params['fields'];
        $this->params['update'] = !empty($params['update']);
        $this->params['key'] = @$params['key'];
        $this->params['skip_header'] = @$params['skip_header'];
        $this->params['is_debug'] = !empty($params['debug']);
        $this->params['delimiter'] = $params['delimiter'] ?? ';';
        $this->params['keys'] = [];
        $this->params['tv_enabled'] = false;


        // Check required options
        if (empty($this->params['fields'])) {
            $error = $this->modx->lexicon('ms2_utilities_import_fields_ns');
            $this->modx->log(modX::LOG_LEVEL_ERROR, $error);
            return $this->miniShop2->error($error);
        }
        if (empty($this->params['key'])) {
            $error = $this->modx->lexicon('ms2_utilities_import_key_ns');
            $this->modx->log(modX::LOG_LEVEL_ERROR, $error);
            return $this->miniShop2->error($error);
        }

        $this->params['keys'] = array_map('trim', explode(',', strtolower($this->params['fields'])));
        foreach ($this->params['keys'] as $v) {
            if (preg_match('/^tv(\d+)$/', $v)) {
                $this->params['tv_enabled'] = true;
                break;
            }
        }

        // Check file
        if (empty($this->params['file'])) {
            $error = $this->modx->lexicon('ms2_utilities_import_file_ns');
            $this->modx->log(modX::LOG_LEVEL_ERROR, $error);
            return $this->miniShop2->error($error);
        } elseif (!preg_match('/\.csv$/i', $this->params['file'])) {
            $error = $this->modx->lexicon('ms2_utilities_import_file_ext_err');
            $this->modx->log(modX::LOG_LEVEL_ERROR, $error);
            return $this->miniShop2->error($error);
        }

        $this->params['file'] = str_replace('//', '/', MODX_BASE_PATH . $this->params['file']);
        if (!file_exists($this->params['file'])) {
            $error = $this->modx->lexicon('ms2_utilities_import_file_nf', ['path' => $this->params['file']]);
            $this->modx->log(modX::LOG_LEVEL_ERROR, $error);
            return $this->miniShop2->error($error);
        }

        $requiredFields = [
            'parent',
            'pagetitle',
        ];

        foreach ($requiredFields as $rf) {
            if (!in_array($rf, $this->params['keys'])) {
                $error = $this->modx->lexicon('ms2_utilities_import_required_field', ['field' => $rf]);
                return $this->miniShop2->error($error);
            }
        }


        $this->import();

        $message = $this->modx->lexicon('ms2_utilities_import_success', [
            'total' => $this->rows,
            'created' => $this->created,
            'updated' => $this->updated
        ]);
        return $this->miniShop2->success($message);
    }

    private function import()
    {
        $handle = fopen($this->params['file'], 'r');

        while (($csv = fgetcsv($handle, 0, $this->params['delimiter'])) !== false) {
            $this->rows++;
            if (!empty($this->params['skip_header']) && $this->rows === 1) {
                continue;
            }
            $this->processRow($csv);

            if ($this->params['is_debug'] && $this->rows === 1) {
                $this->modx->log(
                    modX::LOG_LEVEL_INFO,
                    'You in debug mode, so we process only 1 row. Time: ' . number_format(
                        microtime(true) - $this->modx->startTime,
                        7
                    ) . ' s'
                );
                return true;
            }
        }
        fclose($handle);
        return true;
    }

    private function processRow($csv)
    {
        $data = $gallery = [];
        $this->modx->log(modX::LOG_LEVEL_INFO, "Raw data for import: \n" . print_r($csv, 1));

        foreach ($this->params['keys'] as $k => $v) {
            if (!isset($csv[$k])) {
                $error = $this->modx->lexicon('ms2_utilities_file_field_nf', ['field' => $v, 'row' => $this->rows]);
                $this->modx->log(modX::LOG_LEVEL_ERROR, $error);
                return false;
            }
            if ($v == 'gallery') {
                $gallery[] = $csv[$k];
            } elseif (isset($data[$v]) && !is_array($data[$v])) {
                $data[$v] = [$data[$v], $csv[$k]];
            } elseif (isset($data[$v])) {
                $data[$v][] = $csv[$k];
            } else {
                $data[$v] = $csv[$k];
            }
        }
        $is_product = false;

        // Set default values
        if (empty($data['class_key'])) {
            $data['class_key'] = 'msProduct';
        }
        if (empty($data['context_key'])) {
            $parent = $this->modx->getObject('modResource', ['id' => $data['parent']]);
            if (isset($data['parent']) && $parent) {
                $data['context_key'] = $parent->get('context_key');
            } elseif (isset($this->modx->resource) && isset($this->modx->context)) {
                $data['context_key'] = $this->modx->context->key;
            } else {
                $data['context_key'] = 'web';
            }
        }
        $data['tvs'] = $this->params['tv_enabled'];
        $this->modx->log(modX::LOG_LEVEL_INFO, "Array with importing data: \n" . print_r($data, 1));

        // Duplicate check
        $q = $this->modx->newQuery($data['class_key']);
        $q->where([
            'deleted' => 0,
            'class_key' => $data['class_key']
        ]);
        $q->select($data['class_key'] . '.id');
        if (strtolower($data['class_key']) === 'msproduct') {
            $q->innerJoin('msProductData', 'Data', $data['class_key'] . '.id = Data.id');
            $is_product = true;
        }
        $tmp = $this->modx->getFields($data['class_key']);
        $key = $this->params['key'];
        if (isset($tmp[$key])) {
            $q->where([$key => $data[$key]]);
        } elseif ($is_product) {
            $q->where(['Data.' . $key => $data[$key]]);
        }
        $q->prepare();
        $this->modx->log(modX::LOG_LEVEL_INFO, "SQL query for check for duplicate: \n" . $q->toSql());

        $action = 'create';
        /** @var modResource $exists */
        $exists = $this->modx->getObject($data['class_key'], $q);
        if ($exists) {
            $this->modx->log(modX::LOG_LEVEL_INFO, "Key $key = $data[$key] has duplicate.");
            if (!$this->params['update']) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Skipping line with $key = \"$data[$key]\" because update is disabled.");
                if ($this->params['is_debug'] && $this->rows === 1) {
                    $this->modx->log(
                        modX::LOG_LEVEL_INFO,
                        'You in debug mode, so we process only 1 row. Time: ' . number_format(
                            microtime(true) - $this->modx->startTime,
                            7
                        ) . ' s'
                    );
                    return true;
                }
            } else {
                $action = 'update';
                $data['id'] = $exists->id;
            }
        }

        $this->runAction($action, $data, $gallery);
    }

    private function runAction($action, $data, $gallery = [])
    {
        $this->modx->error->reset();
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('resource/' . $action, $data);
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error on $action: \n" . print_r($response->getAllErrors(), 1));
        } else {
            if ($action == 'update') {
                $this->updated++;
            } else {
                $this->created++;
            }

            $resource = $response->getObject();
            $this->modx->log(modX::LOG_LEVEL_INFO, "Successful $action: \n" . print_r($resource, 1));

            if (!empty($gallery)) {
                // Process gallery images, if exists
                $this->processGallery($resource, $gallery);
            }
        }
    }

    private function processGallery($resource, $gallery)
    {
        if (!empty($gallery)) {
            $this->modx->log(modX::LOG_LEVEL_INFO, "Importing images: \n" . print_r($gallery, 1));
            foreach ($gallery as $v) {
                if (empty($v)) {
                    continue;
                }
                $image = str_replace('//', '/', MODX_BASE_PATH . $v);
                if (!file_exists($image)) {
                    $this->modx->log(
                        modX::LOG_LEVEL_ERROR,
                        "Could not import image \"$v\" to gallery. File \"$image\" not found on server."
                    );
                } else {
                    $response = $this->modx->runProcessor(
                        'gallery/upload',
                        ['id' => $resource['id'], 'name' => $v, 'file' => $image],
                        ['processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/mgr/']
                    );
                    if ($response->isError()) {
                        $this->modx->log(
                            modX::LOG_LEVEL_ERROR,
                            "Error on upload \"$v\": \n" . print_r($response->getAllErrors(), 1)
                        );
                    } else {
                        $this->modx->log(
                            modX::LOG_LEVEL_INFO,
                            "Successful upload  \"$v\": \n" . print_r($response->getObject(), 1)
                        );
                    }
                }
            }
        }
    }
}
