<?php

class msOptionCategoryDuplicateProcessorTest extends MODxProcessorTestCase
{

    public $processor = 'mgr/settings/option/category_duplicate';

    public function setUp()
    {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');
        $category3 = $this->createTestCategory('UnitTestCategory3');

        $option1 = $this->createTestOption('UnitTestOption1', array('type' => 'number', 'caption' => 'UnitTestOption1 Caption'));
        $option2 = $this->createTestOption('UnitTestOption2');
        $option3 = $this->createTestOption('UnitTestOption3');
        $option4 = $this->createTestOption('UnitTestOption4');

        $catOption = $this->createTestCategoryOption($category->get('id'), $option1->get('id'));

        $catOption = $this->createTestCategoryOption($category2->get('id'), $option1->get('id'), array('rank' => 1));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option2->get('id'), array('rank' => 2));
        $catOption = $this->createTestCategoryOption($category2->get('id'), $option3->get('id'), array('rank' => 0));

        $product = $this->createTestProduct('UnitTestProduct1', $category->get('id'));
        $prodOption = $this->createTestProductOption($product->get('id'), $option1->get('id'), array('value' => 100500));
    }

    public function testOptionCategoryDuplicate()
    {
        /** @var msCategory $cat1 */
        $cat1 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $cat2 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory3'));
        $response = $this->getResponse(array(
            'category_from' => $cat1->get('id'),
            'category_to' => $cat2->get('id'),
            ));
        $this->assertTrue($response['success']);
        $this->assertCount(3, $response['object']['options']);
    }

    public function testOptionCategoryDuplicateWithRepeats()
    {
        /** @var msCategory $cat1 */
        $cat1 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $cat2 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory'));

        $response = $this->getResponse(array(
            'category_from' => $cat1->get('id'),
            'category_to' => $cat2->get('id'),
        ));

        $this->assertTrue($response['success']);
        $this->assertCount(3, $response['object']['options']);
    }

    public function testOptionCategoryDuplicateSelf()
    {
        /** @var msCategory $cat1 */
        $c_start = $this->modx->getCount('msCategoryOption');
        $cat1 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $response = $this->getResponse(array(
            'category_from' => $cat1->get('id'),
            'category_to' => $cat1->get('id'),
        ));
        $c = $this->modx->getCount('msCategoryOption');
        $this->assertEquals($c_start, $c);
        $this->assertTrue($response['success']);
        $this->assertCount(3, $response['object']['options']);
    }
}
