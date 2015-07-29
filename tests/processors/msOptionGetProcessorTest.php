<?php

class msOptionGetProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/option/get';

    public function setUp() {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');

        $option1 = $this->createTestOption('UnitTestOption1', array('type' => 'number'));
        $option2 = $this->createTestOption('UnitTestOption2');
        $option3 = $this->createTestOption('UnitTestOption3');
        $option4 = $this->createTestOption('UnitTestOption4');

        $catOption = $this->createTestCategoryOption($category->get('id'), $option1->get('id'));

        $catOption = $this->createTestCategoryOption($category2->get('id'), $option1->get('id'), array('rank' => 1));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option2->get('id'), array('rank' => 2));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option3->get('id'), array('rank' => 0));

    }

    public function testGetNotSpecifiedOption() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_ns'), $response['message']);
    }

    public function testgetNotExistedOption() {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_nfs'), $response['message']);
    }

    public function testGetOption() {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('id');

        $response = $this->getResponse(array('id' => $id));

        $this->assertTrue($response['success']);
        $this->assertArraySubset(array(
            'id' => $id,
            'key' => 'UnitTestOption1',
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
