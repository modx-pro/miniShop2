<?php

require_once dirname(__FILE__, 5) . '/import/importCSV.php';

/**
 * Simple Import
 * import products
 *
 * @package miniShop2
 * @subpackage processors
 */
class msUtilityImportProcessor extends modProcessor
{

    public $classKey = 'msProduct';
    public $languageTopics = ['minishop2:default', 'minishop2:manager'];
    public $permission = 'msproduct_save';
    public $properties = [];

    /** @var miniShop $miniShop */
    public $miniShop;


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        $this->properties = $this->getProperties();

        return parent::initialize();
    }


    /**
     * {@inheritDoc}
     */
    public function getLanguageTopics()
    {
        return $this->languageTopics;
    }


    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $required = ['importfile', 'fields', 'delimiter'];

        foreach ($required as $field) {
            if (!trim($this->getProperty($field))) {
                return $this->addFieldError($field, $this->modx->lexicon('field_required'));
            }
        }

        $importParams = [
            'file' => $this->properties['importfile'],
            'fields' => $this->properties['fields'],
            'update' => $this->properties['update'],
            'key' => $this->properties['key'],
            'debug' => $this->properties['debug'],
            'delimiter' => $this->properties['delimiter'],
            'skip_header' => $this->properties['skip_header'],
        ];

        $scheduler = $this->getProperty('scheduler', 0);
        if (empty($scheduler)) {
            $importCSV = new ImportCSV($this->modx);
            return $importCSV->process($importParams);
        }


        /** @var Scheduler $scheduler */
        $path = $this->modx->getOption(
            'scheduler.core_path',
            null,
            $this->modx->getOption('core_path') . 'components/scheduler/'
        );
        $scheduler = $this->modx->getService('scheduler', 'Scheduler', $path . 'model/scheduler/');
        if (!$scheduler) {
            $this->modx->log(1, 'not found Scheduler extra');
            return $this->failure($this->modx->lexicon('ms2_utilities_scheduler_nf'));
        }
        $task = $scheduler->getTask('minishop2', 'ms2_csv_import');
        if (!$task) {
            $task = $this->createImportTask();
        }
        if (empty($task)) {
            return $this->failure($this->modx->lexicon('ms2_utilities_scheduler_task_ce'));
        }

        $task->schedule('+1 second', $importParams);

        return $this->success($this->modx->lexicon('ms2_utilities_scheduler_success'));
    }

    /**
     * Creating Sheduler's task for start import
     * @return false|object|null
     */
    private function createImportTask()
    {
        $task = $this->modx->newObject('sFileTask');
        $task->fromArray([
            'class_key' => 'sFileTask',
            'content' => '/tasks/csvImport.php',
            'namespace' => 'minishop2',
            'reference' => 'ms2_csv_import',
            'description' => 'MiniShop2 CSV import'
        ]);
        if (!$task->save()) {
            return false;
        }
        return $task;
    }
}

return 'msUtilityImportProcessor';
