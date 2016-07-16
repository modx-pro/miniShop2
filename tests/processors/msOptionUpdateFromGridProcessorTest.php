<?php

class msOptionUpdateFromGridProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/option/updatefromgrid';

    public function setUp() {
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

    public function testUpdateInvalidDataOption() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('invalid_data'), $response['message']);
    }

    public function testUpdateNotSpecifiedOption() {
        $response = $this->getResponse(array('data' => '{}'));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_ns'), $response['message']);
    }

    public function testUpdateNotExistedOption() {
        $response = $this->getResponse(array('data' => json_encode(array('id' => 100500))));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_nfs'), $response['message']);
    }

    public function testUpdateOption() {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $response = $this->getResponse(array('data' => json_encode(array(
            'id' =>  $option->get('id'),
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption5'
        ))));
        $this->assertTrue($response['success']);
        $this->assertArraySubset(array(
            'id' => $option->get('id'),
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption5',
        ), $response['object']);

        $response = $this->getResponse(array('data' => json_encode(array(
            'id' =>  $option->get('id'),
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption6'
        ))));
        $this->assertTrue($response['success']);
        $this->assertArraySubset(array(
            'id' => $option->get('id'),
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption6',
        ), $response['object']);
    }


}
