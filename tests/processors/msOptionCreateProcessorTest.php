<?php

class msOptionCreateProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/option/create';

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

        $product = $this->createTestProduct('UnitTestProduct1', $category->get('id'));

    }

    public function testCreateUniqueOption() {
        $response = $this->getResponse(array(
            'key' => 'UnitTestUniqueOption'
        ));
        $this->assertTrue($response['success']);
        $data = $response['object'];
        $this->assertTrue( isset($data['id']));
        $this->assertEquals('UnitTestUniqueOption', $data['key']);
        $this->assertTrue( isset($data['caption']));
        $this->assertTrue( isset($data['type']));
    }

    public function testCreateNonUniqueOption() {
        $response = $this->getResponse(array(
            'key' => 'UnitTestOption1'
        ));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_ae'), $response['errors'][0]['msg']);
    }

    public function testCreateEmptyNameOption() {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_name_ns'), $response['errors'][0]['msg']);
    }

    public function testCreateWithCategories() {
        $categories = $this->modx->getCollection('msCategory', array('pagetitle:LIKE' => '%UnitTest%'));

        $response = $this->getResponse(array(
            'key' => 'UnitTestUniqueOption',
            'categories' => $this->modx->toJSON(array_keys($categories))
        ));

        $this->assertTrue($response['success']);
        $this->assertEquals($response['object']['categories'], array_keys($categories));

        $product = $this->modx->getObject('msProduct', array('pagetitle' => 'UnitTestProduct1'));
        $productOpt = $this->modx->getObject('msProductOption', array(
            'product_id' => $product->get('id'),
            'key' => 'UnitTestUniqueOption',
        ));
        $this->assertInstanceOf('msProductOption', $productOpt);
        $this->assertEquals('', $productOpt->get('value'));
    }

    public function testCreateWithNotExistedCategories() {
        $categories = $this->modx->getCollection('msCategory', array('pagetitle:LIKE' => '%UnitTest%'));
        $categories[100500] = array();
        $response = $this->getResponse(array(
            'key' => 'UnitTestUniqueOption',
            'categories' => $this->modx->toJSON(array_keys($categories))
        ));

        unset($categories[100500]);
        $this->assertTrue($response['success']);
        $this->assertEquals($response['object']['categories'], array_keys($categories));
    }


}
