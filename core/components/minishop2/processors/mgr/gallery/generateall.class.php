<?php

class msProductFileGenerateAllProcessor extends modObjectProcessor
{
    public $classKey = 'msProductFile';
    public $languageTopics = ['minishop2:default'];
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
        $product_id = (int)$this->getProperty('product_id');
        if (empty($product_id)) {
            return $this->failure($this->modx->lexicon('ms2_gallery_err_ns'));
        }

        $files = $this->modx->getCollection('msProductFile', ['product_id' => $product_id, 'parent' => 0]);
        /** @var msProductFile $file */
        foreach ($files as $file) {
            $children = $file->getMany('Children');
            /** @var msProductFile $child */
            foreach ($children as $child) {
                $child->remove();
            }
            $file->generateThumbnails();
        }

        /** @var msProductData $product */
        $product = $this->modx->getObject('msProductData', ['id' => $product_id]);
        if ($product) {
            $thumb = $product->updateProductImage();
            /** @var miniShop2 $miniShop2 */
            if (empty($thumb) && $miniShop2 = $this->modx->getService('miniShop2')) {
                $thumb = $miniShop2->config['defaultThumb'];
            }
            return $this->success('', ['thumb' => $thumb]);
        }

        return $this->success();
    }
}

return 'msProductFileGenerateAllProcessor';
