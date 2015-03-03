<?php

class msFeatureUpdateProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/update';

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

    public function testUpdateNotSpecifiedFeature() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_ns'), $response['message']);
    }

    public function testUpdateNotExistedFeature() {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_nfs'), $response['message']);
    }

    public function testUpdateNonUniqueFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $response = $this->getResponse(array(
            'id' =>  $feature->get('id'),
            'name' => 'UnitTestFeature2',
        ));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_ae'), $response['errors'][0]['msg']);
    }

    public function testUpdateEmptyNameFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $response = $this->getResponse(array(
            'id' =>  $feature->get('id'),
            'name' => '',
        ));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_name_ns'), $response['errors'][0]['msg']);
    }

    public function testUpdateFeature() {
        $feature = $this->modx->getObject('msFeature', array('name' => 'UnitTestFeature1'));
        $id = $feature->get('id');
        $response = $this->getResponse(array(
            'id' =>  $id,
            'name' => 'UnitTestFeature5',
            'caption' => 'UnitTestFeature5'
        ));
        $this->assertTrue($response['success']);
        $this->assertEquals(array(
            'id' => $id,
            'name' => 'UnitTestFeature5',
            'caption' => 'UnitTestFeature5',
            'type' => '',
            'properties' => null
        ), $response['object']);

    }
}
