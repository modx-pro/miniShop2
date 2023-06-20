<?php

/**
 * Save ms import config to system settings 
 *
 * @package miniShop2
 * @subpackage processors
 */
class msUtilityImportSaveConfigProcessor extends modProcessor
{

    public $languageTopics = ['minishop2:default', 'minishop2:manager'];
    public $permission = 'mssetting_save';
    public $fields = '';
    public $delimiter = '';

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

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
        $this->fields = $this->getProperty('fields');
        $this->delimiter = $this->getProperty('delimiter', ';');

        // save fields to system settings
        if ($settingFields = $this->modx->getObject('modSystemSetting', 'ms2_utility_import_fields')) {
            $settingFields->set('value', $this->fields);
            $settingFields->save();
        }

        // save delimiter to system settings
        if ($settingDelimiter = $this->modx->getObject('modSystemSetting', 'ms2_utility_import_fields_delimiter')) {
            $settingDelimiter->set('value', $this->delimiter);
            $settingDelimiter->save();
        }

        return $this->success();
    }
}

return 'msUtilityImportSaveConfigProcessor';
