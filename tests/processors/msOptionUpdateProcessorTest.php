<?php

class msOptionUpdateProcessorTest extends MODxProcessorTestCase
{

    public $processor = 'mgr/settings/option/update';

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

        $product = $this->createTestProduct('UnitTestProduct1', $category->get('id'));
        $this->createTestProductOption($product->get('id'), 'UnitTestOption1', array('value' => 1));
    }

    public function testUpdateNotSpecifiedOption()
    {
        $response = $this->getResponse(array());
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_ns'), $response['message']);
    }

    public function testUpdateNotExistedOption()
    {
        $response = $this->getResponse(array('id' => 100500));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_nfs'), $response['message']);
    }

    public function testUpdateNonUniqueOption()
    {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $response = $this->getResponse(array(
            'id' =>  $option->get('id'),
            'key' => 'UnitTestOption2',
        ));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_ae'), $response['errors'][0]['msg']);
    }

    public function testUpdateEmptyNameOption()
    {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $response = $this->getResponse(array(
            'id' =>  $option->get('id'),
            'key' => '',
        ));
        $this->assertFalse($response['success']);
        $this->assertEquals($this->modx->lexicon('ms2_option_err_name_ns'), $response['errors'][0]['msg']);
    }

    public function testUpdateOption()
    {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('id');
        $response = $this->getResponse(array(
            'id' =>  $id,
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption5'
        ));
        $this->assertTrue($response['success']);
        $this->assertArraySubset(array(
            'id' => $id,
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption5',
            'type' => '',
            'properties' => null
        ), $response['object']);
    }

    public function testUpdateWithCategories()
    {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('id');
        $categories = $this->modx->getCollection('msCategory', array('pagetitle:LIKE' => '%UnitTest%'));

        $response = $this->getResponse(array(
            'id' =>  $id,
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption5',
            'categories' => json_encode(array_keys($categories))
        ));

        $this->assertTrue($response['success']);
        $this->assertEquals($response['object']['categories'], array_keys($categories));

        $product = $this->modx->getObject('msProduct', array('pagetitle' => 'UnitTestProduct1'));
        $productOpt = $this->modx->getObject('msProductOption', array(
            'product_id' => $product->get('id'),
            'key' => 'UnitTestOption5',
        ));
        $this->assertInstanceOf('msProductOption', $productOpt);
        $this->assertEquals('1', $productOpt->get('value'));
    }

    public function testUpdateWithNotExistedCategories()
    {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption1'));
        $id = $option->get('id');
        $categories = $this->modx->getCollection('msCategory', array('pagetitle:LIKE' => '%UnitTest%'));
        $categories[100500] = array();
        $response = $this->getResponse(array(
            'id' =>  $id,
            'key' => 'UnitTestOption5',
            'caption' => 'UnitTestOption5',
            'categories' => json_encode(array_keys($categories))
        ));

        unset($categories[100500]);
        $this->assertTrue($response['success']);
        $this->assertEquals($response['object']['categories'], array_keys($categories));
    }
}
