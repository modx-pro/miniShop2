<?php

class msProductFileUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'msProductFile';
    public $languageTopics = array('core:default', 'minishop2:product');
    public $permission = 'msproductfile_save';
    /** @var msProductFile $object */
    public $object;
    protected $old_name = null;


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
     * @return array|bool|string
     */
    public function beforeSet()
    {
        if (!$this->getProperty('id')) {
            return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));
        }

        foreach (array('file', 'name') as $v) {
            $value = trim($this->getProperty($v));
            if (empty($value)) {
                $this->addFieldError($v, $this->modx->lexicon('field_required'));
            } else {
                $this->setProperty($v, $value);
            }
        }
        $this->old_name = $this->object->get('file');

        return parent::beforeSet();
    }


    /**
     * @return bool
     */
    public function afterSave()
    {
        $extension = pathinfo($this->old_name, PATHINFO_EXTENSION);
        $file = preg_replace('#\.' . $extension . '$#i', '', $this->object->get('file'));
        $file .= '.' . $extension;
        if ($this->old_name != $file) {
            $this->object->rename($this->object->get('file'), $this->old_name);
        } else {
            $this->object->set('file', $this->old_name);
            $this->object->save();
        }

        $children = $this->object->getMany('Children');
        if (!empty($children)) {
            /** @var msProductFile $child */
            foreach ($children as $child) {
                $child->fromArray(array(
                    'name' => $this->object->get('name'),
                    'description' => $this->object->get('description'),
                ));
                $child->save();
            }
        }

        /** @var msProduct $product */
        if ($product = $this->object->getOne('Product')) {
            $product->updateProductImage();
        }

        return parent::afterSave();
    }

}

return 'msProductFileUpdateProcessor';