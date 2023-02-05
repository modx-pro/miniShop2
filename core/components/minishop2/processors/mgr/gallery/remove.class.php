<?php

require_once 'removecatalogs.class.php';

class msProductFileRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'msProductFile';
    public $languageTopics = ['minishop2:product'];
    public $permission = 'msproductfile_save';
    /** @var msProduct $product */
    public $product;

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
        parent::process();

        /** @var msProduct $product */
        $thumb = '';
        if ($product = $this->object->getOne('Product')) {
            $thumb = $product->updateProductImage();
        }
        /** @var miniShop2 $miniShop2 */
        if (empty($thumb) && $miniShop2 = $this->modx->getService('miniShop2')) {
            $thumb = $miniShop2->config['defaultThumb'];
        }

        if (empty($product->getMany('Files'))) {
            RemoveCatalogs::process($this->modx, $product->get('id'));
        }

        return $this->success('', ['thumb' => $thumb]);
    }
}

return 'msProductFileRemoveProcessor';
