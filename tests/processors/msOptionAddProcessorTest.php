<?php

class msOptionAddProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/option/add';

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

    public function testAddOption() {
        $option = $this->modx->getObject('msOption', array('key' => 'UnitTestOption2'));
        $cat = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory'));

        $t=microtime(true);
        $response = $this->getResponse(array(
            'option_id' => $option->id,
            'category_id' => $cat->id,
            'value' => 'Test',
            'active' => 1,
            'required' => 1,
        ));
        $t=microtime(true) - $t;
        $this->assertLessThan(30, $t);

        $this->assertTrue($response['success']);
        $this->assertArraySubset(array(
            'option_id' => $option->id,
            'category_id' => $cat->id,
            'value' => 'Test',
            'active' => true,
            'required' => true,
        ), $response['object']);

        $product = $this->modx->getObject('msProduct', array('pagetitle' => 'UnitTestProduct1'));
        $productOpt = $this->modx->getObject('msProductOption', array(
            'product_id' => $product->get('id'),
            'key' => $option->get('key'),
        ));
        $this->assertInstanceOf('msProductOption', $productOpt);
        $this->assertEquals('Test', $productOpt->get('value'));
    }
}
