<?php

abstract class MODxProcessorTestCase extends MODxTestCase {

    public $processor;

    protected function getResponse($data) {
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor($this->processor, $data, $this->path);
        $response = $response->getResponse();
        if (!is_array($response)) {
            $response = $this->modx->fromJSON($response);
        }

        echo "\r\n".$this->processor ." response:\r\n";
        var_dump($response);
        return $response;
    }

    protected function createTestCategory($pagetitle, $properties = array()) {
        /** @var msCategory $category */
        $category = $this->modx->newObject('msCategory');
        $category->set('pagetitle', $pagetitle);
        if (count($properties) > 0) {
            $category->fromArray($properties);
        }
        $category->save();

        return $category;
    }

    protected function createTestProduct($pagetitle, $category, $properties = array()) {
        /** @var msProduct $product */
        $product = $this->modx->newObject('msProduct');
        $product->set('pagetitle', $pagetitle);
        $product->set('parent', $category);
        if (count($properties) > 0) {
            $product->fromArray($properties);
        }
        $product->save();

        return $product;
    }

    protected function createTestFeature($name, $properties = array()) {
        /** @var msFeature $feature */
        $feature = $this->modx->newObject('msFeature');
        $feature->set('name', $name);
        $feature->fromArray($properties);
        $feature->save();

        return $feature;
    }

    protected function createTestCategoryFeature($cat_id, $feature_id, $properties = array()) {
        /** @var msCategoryFeature $catFeature */
        $catFeature = $this->modx->newObject('msCategoryFeature');
        $catFeature->set('category_id', $cat_id);
        $catFeature->set('feature_id', $feature_id);
        $catFeature->fromArray($properties);
        $catFeature->save();

        return $catFeature;
    }

    protected function createTestProductFeature($product_id, $feature_id, $properties = array()) {
        /** @var msProductFeature $prodFeature */
        $prodFeature = $this->modx->newObject('msProductFeature');
        $prodFeature->set('product_id', $product_id);
        $prodFeature->set('feature_id', $feature_id);
        $prodFeature->fromArray($properties);
        $prodFeature->save();
        return $prodFeature;
    }

    public function tearDown() {
        parent::tearDown();

        /* Remove test categories */
        $category = $this->modx->getCollection('modResource',array('pagetitle:LIKE' => '%UnitTest%'));
        /** @var msCategory $cat */
        foreach ($category as $cat) {
            $cat->remove();
        }
        $this->modx->query("ALTER TABLE ".$this->modx->getTableName('msCategory')." AUTO_INCREMENT = 0;");

        /* Remove test features */
        $objs = $this->modx->getCollection('msFeature',array('name:LIKE' => '%UnitTest%'));
        /** @var xPDOObject $obj */
        foreach ($objs as $obj) {
            $obj->remove();
        }
        $this->modx->query("ALTER TABLE ".$this->modx->getTableName('msFeature')." AUTO_INCREMENT = 0;");

        $this->modx->query("ALTER TABLE ".$this->modx->getTableName('msProductData')." AUTO_INCREMENT = 0;");
        $this->modx->query("ALTER TABLE ".$this->modx->getTableName('msCategoryFeature')." AUTO_INCREMENT = 0;");
        $this->modx->query("ALTER TABLE ".$this->modx->getTableName('msProductFeature')." AUTO_INCREMENT = 0;");

    }
}
