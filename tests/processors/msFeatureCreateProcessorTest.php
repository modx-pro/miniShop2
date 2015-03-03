<?php

class msFeatureCreateProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/create';

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

    public function testCreateUniqueFeature() {
        $response = $this->getResponse(array(
            'name' => 'UnitTestUniqueFeature'
        ));
        $this->assertTrue($response['success']);
        $data = $response['object'];
        $this->assertTrue( isset($data['id']));
        $this->assertEquals('UnitTestUniqueFeature', $data['name']);
        $this->assertTrue( isset($data['caption']));
        $this->assertTrue( isset($data['type']));
    }

    public function testCreateNonUniqueFeature() {
        $response = $this->getResponse(array(
            'name' => 'UnitTestFeature1'
        ));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_ae'), $response['errors'][0]['msg']);
    }

    public function testCreateEmptyNameFeature() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_feature_err_name_ns'), $response['errors'][0]['msg']);
    }


}
