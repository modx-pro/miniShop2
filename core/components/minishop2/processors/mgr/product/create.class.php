<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/create.class.php';
require_once dirname(__FILE__) . '/getvendor.php';

class msProductCreateProcessor extends modResourceCreateProcessor
{
    public $classKey = 'msProduct';
    public $languageTopics = ['resource', 'minishop2:default'];
    public $permission = 'msproduct_save';
    public $beforeSaveEvent = 'OnBeforeDocFormSave';
    public $afterSaveEvent = 'OnDocFormSave';
    /** @var msProduct $object */
    public $object;

    /**
     * @return string
     */
    public function prepareAlias()
    {
        if ($this->workingContext->getOption('ms2_product_id_as_alias')) {
            $alias = 'empty-resource-alias';
            $this->setProperty('alias', $alias);
        } else {
            $alias = parent::prepareAlias();
        }

        return $alias;
    }

    /**
     * @return array|string
     */
    public function beforeSet()
    {
        $this->setDefaultProperties([
            'show_in_tree' => $this->modx->getOption('ms2_product_show_in_tree_default', null, false),
            'hidemenu' => $this->modx->getOption('hidemenu_default', null, true),
            'source' => $this->modx->getOption('ms2_product_source_default', null, 1),
            'template' => $this->modx->getOption(
                'ms2_template_product_default',
                null,
                $this->modx->getOption('default_template')
            ),
        ]);

        $properties = $this->getProperties();
        $options = [];
        foreach ($properties as $key => $value) {
            if (strpos($key, 'options-') === 0) {
                $options[substr($key, 8)] = $value;
                $this->unsetProperty($key);
            }
        }
        $this->setProperty('options', $options);

        if (!empty($properties['vendor'])) {
            $getVendor = new GetVendor();
            $vendor_id = $getVendor::getVendorId($this->modx, $properties['vendor']);
            if ($vendor_id) {
                $this->setProperty('vendor', $vendor_id);
            }
        }

        return parent::beforeSet();
    }

    /**
     * @return mixed
     */
    public function beforeSave()
    {
        $this->object->set('isfolder', false);

        return parent::beforeSave();
    }

    /**
     * @return mixed
     */
    public function afterSave()
    {
        if ($this->object->get('alias') == 'empty-resource-alias') {
            $this->object->set('alias', $this->object->get('id'));
            $this->object->save();
        }
        // Update resourceMap before OnDocSaveForm event
        $results = $this->modx->cacheManager->generateContext($this->object->context_key);
        if (isset($results['resourceMap'])) {
            $this->modx->context->resourceMap = $results['resourceMap'];
        }
        if (isset($results['aliasMap'])) {
            $this->modx->context->aliasMap = $results['aliasMap'];
        }

        return parent::afterSave();
    }

    /**
     * @return int
     */
    private function getVendorId($name)
    {
        $criteria = [
            'id' => $name,
            'OR:name:=' => $name
        ];

        $vendor = $this->modx->getObject('msVendor', $criteria);

        if ($vendor) {
            return $vendor->get('id');
        }

        $vendor = $this->modx->newObject('msVendor');
        $vendor->set('name', $name);
        $vendor->save();

        return $vendor->get('id');
    }
}

return 'msProductCreateProcessor';
