<?php

class msFeatureDuplicateProcessor extends modObjectDuplicateProcessor {
    public $classKey = 'msFeature';
    public $objectType = 'ms2_feature';
    public $languageTopics = array('default', 'minishop2:default');

    public function afterSave() {
        $this->duplicateCategories();
        $this->duplicateProducts();
    }

    public function duplicateCategories() {
        if ($this->getProperty('copy_categories', false)) {
            $cats = $this->object->getMany('FeatureCategories');
            if (is_array($cats) && !empty($cats)) {
                /** @var msCategoryFeature $cat */
                foreach ($cats as $cat) {
                    /** @var msCategoryFeature $newCat */
                    $newCat = $this->modx->newObject('msCategoryFeature');
                    $newCat->fromArray($cat->toArray());
                    $newCat->set('feature_id', $this->newObject->get('id'));
                    $newCat->set('product_id', $cat->get('product_id'));
                    $newCat->save();
                }
            }
        }
    }

    public function duplicateProducts() {
        if ($this->getProperty('copy_values', false)) {
            $products = $this->object->getMany('FeatureProducts');
            $p = $this->modx->getCollection('msProductFeature');
            if (is_array($products) && !empty($products)) {
                /** @var msProductFeature $product */
                foreach ($products as $product) {
                    /** @var msProductFeature $newProduct */
                    $newProduct = $this->modx->newObject('msProductFeature');
                    $newProduct->set('feature_id', $this->newObject->get('id'));
                    $newProduct->set('product_id', $product->get('product_id'));
                    $newProduct->set('value', $product->get('value'));
                    $newProduct->save();
                }
            }
        }
    }
}

return 'msFeatureDuplicateProcessor';
