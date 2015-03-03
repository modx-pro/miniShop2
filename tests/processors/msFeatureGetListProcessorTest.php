<?php

class msFeatureGetListProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/getlist';

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

    public function testGetAllFeatures() {
        $response = $this->getResponse(array());
        $this->assertTrue( $response['success']);
        $data = $response['results'][0];
        $this->assertTrue( isset($data['id']));
        $this->assertTrue( isset($data['name']));
        $this->assertTrue( isset($data['caption']));
        $this->assertTrue( isset($data['type']));
    }

    public function testEmptyCategoryFeatures() {
        $category = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestEmptyCategory'));
        $response = $this->getResponse(array('category' => $category->get('id')));
        $this->assertTrue( $response['success']);
        $this->assertEquals(0, $response['total']);
    }

    public function testRankedCategoryFeatures() {
        $category = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $response = $this->getResponse(array('category' => $category->get('id'), 'sort' => 'rank'));
        $this->assertTrue( $response['success']);
        $this->assertEquals(3, $response['total']);
        $this->assertEquals('UnitTestFeature3', $response['results'][0]['name']);
        $this->assertEquals('UnitTestFeature1', $response['results'][1]['name']);
        $this->assertEquals('UnitTestFeature2', $response['results'][2]['name']);
        $this->assertEquals(0, $response['results'][0]['rank']);
        $this->assertEquals(1, $response['results'][1]['rank']);
        $this->assertEquals(2, $response['results'][2]['rank']);
    }

    public function testRankByNameCategoryFeatures() {
        $category = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $response = $this->getResponse(array('category' => $category->get('id')));
        $this->assertTrue( $response['success']);
        $this->assertEquals(3, $response['total']);
        $this->assertEquals('UnitTestFeature1', $response['results'][0]['name']);
        $this->assertEquals('UnitTestFeature2', $response['results'][1]['name']);
        $this->assertEquals('UnitTestFeature3', $response['results'][2]['name']);
        $this->assertEquals(1, $response['results'][0]['rank']);
        $this->assertEquals(2, $response['results'][1]['rank']);
        $this->assertEquals(0, $response['results'][2]['rank']);
    }
}
