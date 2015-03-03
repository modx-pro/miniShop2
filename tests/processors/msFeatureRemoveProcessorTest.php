<?php

class msFeatureRemoveProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/remove';

    public function setUp() {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');

        $feature1 = $this->createTestFeature('UnitTestFeature1');
        $feature2 = $this->createTestFeature('UnitTestFeature2');
        $feature3 = $this->createTestFeature('UnitTestFeature3');
        $feature4 = $this->createTestFeature('UnitTestFeature4');

        $catFeature = $this->createTestCategoryFeature($category->get('id'), $feature1->get('id'));

        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature1->get('id'), array('rank' => 1));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature2->get('id'), array('rank' => 2));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature3->get('id'), array('rank' => 0));

    }

    public function testRemoveNotSpecifiedFeature() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_ns'), $response['message']);
    }

    public function testRemoveNotExistedFeature() {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_nfs'), $response['message']);
    }

    public function testRemoveFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $id = $feature->get('id');
        $cats = $feature->getMany('FeatureCategories');//$this->modx->getCollection('msCategoryFeature', array('feature_id' => $id));
        $this->assertCount(2, $cats);

        $response = $this->getResponse(array(
            'id' =>  $id,
        ));
        $this->assertTrue($response['success']);
        $this->assertEquals(array(
            'id' => $id,
        ), $response['object']);

        $cats = $this->modx->getCollection('msCategoryFeature', array('feature_id' => $id));
        $this->assertCount(0, $cats);
    }
}
