<?php

class msFeatureDuplicateProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/duplicate';

    public function setUp() {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');

        $feature1 = $this->createTestFeature('UnitTestFeature1', array('type' => 'number', 'caption' => 'UnitTestFeature1 Caption'));
        $feature2 = $this->createTestFeature('UnitTestFeature2');
        $feature3 = $this->createTestFeature('UnitTestFeature3');
        $feature4 = $this->createTestFeature('UnitTestFeature4');

        $catFeature = $this->createTestCategoryFeature($category->get('id'), $feature1->get('id'));

        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature1->get('id'), array('rank' => 1));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature2->get('id'), array('rank' => 2));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature3->get('id'), array('rank' => 0));

        $product = $this->createTestProduct('UnitTestProduct1', $category->get('id'));
        $prodFeature = $this->createTestProductFeature($product->get('id'), $feature1->get('id'), array('value' => 100500));

    }

    public function testDuplicateNotSpecifiedFeature() {
        $response = $this->getResponse(array());
        $this->assertEquals(false, $response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_ns'), $response['message']);
    }

    public function testDuplicateNotExistedFeature() {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertEquals(false, $response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_nfs'), $response['message']);
    }

    public function testDuplicateFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $id = $feature->get('id');
        $cats = $this->modx->getCollection('msCategoryFeature', array('feature_id' => $id));
        $this->assertCount(2, $cats);

        $response = $this->getResponse(array(
            'id' =>  $feature->get('id'),
        ));
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('id', $response['object']);
        $newId = $response['object']['id'];
        unset($response['object']['id']);
        $this->assertEquals(array(
            'name' => 'Копия UnitTestFeature1',
            'caption' => 'UnitTestFeature1 Caption',
            'type' => 'number',
        ), $response['object']);

        $cats = $this->modx->getCollection('msCategoryFeature', array('feature_id' => $newId));
        $this->assertCount(0, $cats);

        $products = $this->modx->getCollection('msProductFeature', array('feature_id' => $newId));
        $this->assertCount(0, $products);
    }

    public function testDuplicateWithCatFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $id = $feature->get('id');
        $cats = $this->modx->getCollection('msCategoryFeature', array('feature_id' => $id));
        $this->assertCount(2, $cats);

        $response = $this->getResponse(array(
            'id' =>  $feature->get('id'),
            'name' => 'NewUnitTestFeature',
            'copy_categories' => true,
        ));
        $this->assertTrue($response['success']);

        $newId = $response['object']['id'];
        $cats = $this->modx->getCollection('msCategoryFeature', array('feature_id' => $newId));
        $this->assertCount(2, $cats);
    }

    public function testDuplicateWithValuesFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $id = $feature->get('id');
        $products = $this->modx->getCollection('msProductFeature', array('feature_id' => $id));
        $this->assertCount(1, $products);

        $response = $this->getResponse(array(
            'id' =>  $feature->get('id'),
            'name' => 'NewUnitTestFeature',
            'copy_values' => true,
        ));
        $this->assertTrue($response['success']);

        $newId = $response['object']['id'];
        $products = $this->modx->getCollection('msProductFeature', array('feature_id' => $newId));
        $this->assertCount(1, $products);
    }
}
