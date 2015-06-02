<?php

class msOptionDuplicateProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/option/duplicate';

    public function setUp() {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');

        $option1 = $this->createTestOption('UnitTestOption1', array('type' => 'number', 'caption' => 'UnitTestOption1 Caption'));
        $option2 = $this->createTestOption('UnitTestOption2');
        $option3 = $this->createTestOption('UnitTestOption3');
        $option4 = $this->createTestOption('UnitTestOption4');

        $catOption = $this->createTestCategoryOption($category->get('id'), $option1->get('id'));

        $catOption = $this->createTestCategoryOption($category2->get('id'), $option1->get('id'), array('rank' => 1));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option2->get('id'), array('rank' => 2));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option3->get('id'), array('rank' => 0));

        $product = $this->createTestProduct('UnitTestProduct1', $category->get('id'));
        $prodOption = $this->createTestProductOption($product->get('id'), $option1->get('key'), array('value' => 100500));

    }

    public function testDuplicateNotSpecifiedOption() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_ns'), $response['message']);
    }

    public function testDuplicateNotExistedOption() {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_nfs'), $response['message']);
    }

    public function testDuplicateOption() {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('id');
        $cats = $this->modx->getCollection('msCategoryOption', array('option_id' => $id));
        $this->assertCount(2, $cats);

        $response = $this->getResponse(array(
            'id' =>  $option->get('id'),
        ));
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('id', $response['object']);
        $newId = $response['object']['id'];
        unset($response['object']['id']);
        $this->assertEquals('Копия UnitTestOption1', $response['object']['key']);
        $this->assertEquals('UnitTestOption1 Caption', $response['object']['caption']);
        $this->assertEquals('number', $response['object']['type']);

        $cats = $this->modx->getCollection('msCategoryOption', array('option_id' => $newId));
        $this->assertCount(0, $cats);

        $products = $this->modx->getCollection('msProductOption', array('key' => $response['object']['key']));
        $this->assertCount(0, $products);
    }

    public function testDuplicateWithCatOption() {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('id');
        $cats = $this->modx->getCollection('msCategoryOption', array('option_id' => $id));
        $this->assertCount(2, $cats);
        $response = $this->getResponse(array(
            'id' =>  $option->get('id'),
            'key' => 'NewUnitTestOption',
            'copy_categories' => true,
        ));
        $this->assertTrue($response['success']);

        $newId = $response['object']['id'];
        $cats = $this->modx->getCollection('msCategoryOption', array('option_id' => $newId));
        $this->assertCount(2, $cats);
    }

    public function testDuplicateWithValuesOption() {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('key');
        $products = $this->modx->getCollection('msProductOption', array('key' => $id));
        $this->assertCount(1, $products);

        $response = $this->getResponse(array(
            'id' =>  $option->get('id'),
            'key' => 'NewUnitTestOption',
            'copy_values' => true,
        ));
        $this->assertTrue($response['success']);

        $newId = $response['object']['key'];
        $products = $this->modx->getCollection('msProductOption', array('key' => $newId));
        $this->assertCount(1, $products);
    }
}
