<?php

class msFeatureGetProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/get';

    public function setUp() {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');

        $feature1 = $this->createTestFeature('UnitTestFeature1', array('type' => 'number'));
        $feature2 = $this->createTestFeature('UnitTestFeature2');
        $feature3 = $this->createTestFeature('UnitTestFeature3');
        $feature4 = $this->createTestFeature('UnitTestFeature4');

        $catFeature = $this->createTestCategoryFeature($category->get('id'), $feature1->get('id'));

        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature1->get('id'), array('rank' => 1));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature2->get('id'), array('rank' => 2));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature3->get('id'), array('rank' => 0));

    }

    public function testGetNotSpecifiedFeature() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_ns'), $response['message']);
    }

    public function testgetNotExistedFeature() {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_nfs'), $response['message']);
    }

    public function testGetFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $id = $feature->get('id');

        $response = $this->getResponse(array('id' => $id));

        $this->assertTrue($response['success']);
        $this->assertArraySubset(array(
            'id' => $id,
            'name' => 'UnitTestFeature1',
            'caption' => '',
            'type' => 'number',
            'categories' => array(),
        ), $response['object']);

        $this->assertCount(2, $response['object']['categories']);

        $this->assertArraySubset(array(
            'pagetitle' => 'UnitTestCategory',
            'rank' => 0,
            'active' => 0,
            'required' => 0,
        ), $response['object']['categories'][0]);

        $this->assertArraySubset(array(
            'pagetitle' => 'UnitTestCategory2',
            'rank' => 1,
            'active' => 0,
            'required' => 0,
        ), $response['object']['categories'][1]);

        $this->assertArrayHasKey('properties', $response['object']);

    }
}
