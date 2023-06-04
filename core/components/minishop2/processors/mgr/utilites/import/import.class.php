<?php

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

        $url = $this->modx->getOption('site_url') . 'core/components/minishop2/import/csv.php';

        $importParams = [
            'file' => $this->properties['importfile'],
            'fields' => $this->properties['fields'],
            'update' => $this->properties['update'],
            'key' => $this->properties['key'],
            'debug' => $this->properties['debug'],
            'delimiter' => $this->properties['delimiter']
        ];

        $url .= '?' . http_build_query($importParams);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        return $this->success($result, [
            'message' => $result
        ]);
    }
}

return 'msUtilityImportProcessor';
