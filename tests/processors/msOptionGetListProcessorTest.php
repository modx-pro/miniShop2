<?php

class msOptionGetListProcessorTest extends MODxProcessorTestCase
{

    public $processor = 'mgr/settings/option/getlist';

    public function setUp()
    {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');

        $option1 = $this->createTestOption('UnitTestOption1');
        $option2 = $this->createTestOption('UnitTestOption2');
        $option3 = $this->createTestOption('UnitTestOption3');
        $option4 = $this->createTestOption('UnitTestOption4');

        $catOption = $this->createTestCategoryOption($category->get('id'), $option1->get('id'));

        $catOption = $this->createTestCategoryOption($category2->get('id'), $option1->get('id'), array('rank' => 1));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option2->get('id'), array('rank' => 2));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option3->get('id'), array('rank' => 0));
    }

    public function testGetAllOptions()
    {
        $response = $this->getResponse(array());
        $this->assertTrue($response['success']);
        $data = $response['results'][0];
        $this->assertTrue(isset($data['id']));
        $this->assertTrue(isset($data['key']));
        $this->assertTrue(isset($data['caption']));
        $this->assertTrue(isset($data['type']));
    }

    public function testEmptyCategoryOptions()
    {
        $category = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestEmptyCategory'));
        $response = $this->getResponse(array('category' => $category->get('id')));
        $this->assertTrue($response['success']);
        $this->assertEquals(0, $response['total']);
    }

    public function testRankedCategoryOptions()
    {
        $category = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $response = $this->getResponse(array('category' => $category->get('id'), 'sort' => 'rank'));
        $this->assertTrue($response['success']);
        $this->assertEquals(3, $response['total']);
        $this->assertEquals('UnitTestOption3', $response['results'][0]['key']);
        $this->assertEquals('UnitTestOption1', $response['results'][1]['key']);
        $this->assertEquals('UnitTestOption2', $response['results'][2]['key']);
        $this->assertEquals(0, $response['results'][0]['rank']);
        $this->assertEquals(1, $response['results'][1]['rank']);
        $this->assertEquals(2, $response['results'][2]['rank']);
    }

    public function testRankByNameCategoryOptions()
    {
        $category = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $response = $this->getResponse(array('category' => $category->get('id')));
        $this->assertTrue($response['success']);
        $this->assertEquals(3, $response['total']);
        $this->assertEquals('UnitTestOption1', $response['results'][0]['key']);
        $this->assertEquals('UnitTestOption2', $response['results'][1]['key']);
        $this->assertEquals('UnitTestOption3', $response['results'][2]['key']);
        $this->assertEquals(1, $response['results'][0]['rank']);
        $this->assertEquals(2, $response['results'][1]['rank']);
        $this->assertEquals(0, $response['results'][2]['rank']);
    }
}
