<?php

class msProductFileGenerateProcessor extends modObjectProcessor
{
    public $classKey = 'msProductFile';
    public $languageTopics = array('minishop2:default');
    public $permission = 'msproductfile_generate';


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
     * @return array|string
     */
    public function process()
    {
        $id = $this->getProperty('id');
        if (empty($id)) {
            return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));
        }

        /** @var msProductFile $file */
        if ($file = $this->modx->getObject('msProductFile', $id)) {
            $children = $file->getMany('Children');
            /** @var msProductFile $child */
            foreach ($children as $child) {
                $child->remove();
            }
            $file->generateThumbnails();

            $thumb = $file->getFirstThumbnail();
            /** @var msProductData $product */
            $product = $this->modx->getObject('msProductData', array('id' => $file->get('product_id')));
            $product->set('thumb', $thumb['url']);
            if ($product->save()) {
                return $this->success();
            }
        }

        return $this->success();
    }

}

return 'msProductFileGenerateProcessor';
