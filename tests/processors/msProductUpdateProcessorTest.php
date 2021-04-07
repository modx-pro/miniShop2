<?php

class msProductUpdateProcessorTest extends MODxProcessorTestCase
{

    public $processor = 'mgr/product/update';

    public function setUp()
    {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');

        $option1 = $this->createTestOption('UnitTestOption1');
        $option2 = $this->createTestOption('UnitTestOption2');
        $option3 = $this->createTestOption('UnitTestOption3');
        $option4 = $this->createTestOption('4');

        $catOption = $this->createTestCategoryOption($category->get('id'), $option1->get('id'));

        $catOption = $this->createTestCategoryOption($category2->get('id'), $option1->get('id'), array('rank' => 1));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option2->get('id'), array('rank' => 2));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option3->get('id'), array('rank' => 0));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option4->get('id'), array('rank' => 3));

        $product = $this->createTestProduct('UnitTestProduct1', $category->get('id'));
        $this->createTestProductOption($product->get('id'), 'UnitTestOption1', array('value' => 1));
    }

    public function testNumberKeyOption()
    {
        $product = $this->modx->getObject('msProduct', array('pagetitle' => 'UnitTestProduct1'));
        $product->set('4', 'test');
        $this->assertEquals('test', $product->get('4'));
    }
}
