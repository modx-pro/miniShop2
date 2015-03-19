<?php

class msOptionRemoveProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/option/remove';

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

    public function testRemoveNotSpecifiedOption() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_ns'), $response['message']);
    }

    public function testRemoveNotExistedOption() {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_nfs'), $response['message']);
    }

    public function testRemoveOption() {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('id');
        $cats = $option->getMany('OptionCategories');//$this->modx->getCollection('msCategoryFeature', array('feature_id' => $id));
        $this->assertCount(2, $cats);

        $response = $this->getResponse(array(
            'id' =>  $id,
        ));
        $this->assertTrue($response['success']);
        $this->assertEquals(array(
            'id' => $id,
        ), $response['object']);

        $cats = $this->modx->getCollection('msCategoryOption', array('option_id' => $id));
        $this->assertCount(0, $cats);
    }
}
