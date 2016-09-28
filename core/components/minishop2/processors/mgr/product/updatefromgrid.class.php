<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msProductUpdateFromGridProcessor extends msProductUpdateProcessor
{

    /**
     * @param modX $modx
     * @param string $className
     * @param array $properties
     *
     * @return modProcessor
     */
    public static function getInstance(modX &$modx, $className, $properties = array())
    {

        /** @var modProcessor $processor */
        $processor = new msProductUpdateFromGridProcessor($modx, $properties);

        return $processor;
    }


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $data = $this->getProperty('data');
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }

        $data = json_decode($data, true);
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }

        $data = $this->prepareValues($data);
        $this->setProperties($data);
        $this->unsetProperty('data');

        return parent::initialize();
    }


    /**
     * @param array $data
     *
     * @return array
     */
    public function prepareValues(array $data)
    {
        $fields = $this->modx->getFieldMeta('modResource');
        foreach ($fields as $key => $field) {
            if ($field['phptype'] == 'timestamp') {
                if (!empty($data[$key]) && is_numeric($data[$key])) {
                    $data[$key] = date('Y-m-d H:i:s', $data[$key]);
                }
            }
        }

        return $data;
    }


    /**
     * @return array|string
     */
    public function beforeSet()
    {
        return true;
    }


    /**
     *
     */
    public function handleCheckBoxes()
    {
        $this->setCheckbox('hidemenu');
        $this->setCheckbox('isfolder');
        $this->setCheckbox('richtext');
        $this->setCheckbox('published');
        $this->setCheckbox('cacheable');
        $this->setCheckbox('searchable');
        $this->setCheckbox('syncsite');
        $this->setCheckbox('deleted');
        $this->setCheckbox('uri_override');
        $this->setCheckbox('show_in_tree');
    }


    /**
     * @return array|string
     */
    public function cleanup()
    {
        $this->object->removeLock();
        $this->clearCache();

        /** @var miniShop2 $miniShop2 */
        $miniShop2 = $this->modx->getService('miniShop2');
        /** @var modProcessorResponse $res */
        $res = $miniShop2->runProcessor('mgr/product/getlist', array(
            'id' => $this->object->id,
            'parent' => $this->object->parent,
        ));

        return $res->getResponse();
    }

}

return 'msProductUpdateFromGridProcessor';