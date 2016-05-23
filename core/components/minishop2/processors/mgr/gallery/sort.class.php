<?php

class msProductFileSortProcessor extends modObjectProcessor
{
    public $classKey = 'msProductFile';
    public $permission = 'msproductfile_save';


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
     * It is adapted code from https://github.com/splittingred/Gallery/blob/a51442648fde1066cf04d46550a04265b1ad67da/core/components/gallery/processors/mgr/item/sort.php
     *
     * @return array|string
     */
    public function process()
    {
        /** @var msProductFile $source */
        $source = $this->modx->getObject('msProductFile', $this->getProperty('source'));
        /** @var msProductFile $target */
        $target = $this->modx->getObject('msProductFile', $this->getProperty('target'));
        /** @var msProductData $product */
        $product = $this->modx->getObject('msProductData', $this->getProperty('product_id'));
        $product_id = $product->get('id');

        if (empty($source) || empty($target) || empty($product_id)) {
            return $this->modx->error->failure();
        }

        if ($source->get('rank') < $target->get('rank')) {
            $this->modx->exec("UPDATE {$this->modx->getTableName('msProductFile')}
                SET rank = rank - 1 WHERE
                    product_id = " . $product_id . "
                    AND rank <= {$target->get('rank')}
                    AND rank > {$source->get('rank')}
                    AND rank > 0
            ");
            $newRank = $target->get('rank');
        } else {
            $this->modx->exec("UPDATE {$this->modx->getTableName('msProductFile')}
                SET rank = rank + 1 WHERE
                    product_id = " . $product_id . "
                    AND rank >= {$target->get('rank')}
                    AND rank < {$source->get('rank')}
            ");
            $newRank = $target->get('rank');
        }
        $source->set('rank', $newRank);
        $source->save();

        $thumb = $product->updateProductImage();

        return $this->modx->error->success('', array('thumb' => $thumb));
    }

}

return 'msProductFileSortProcessor';