<?php

class msFeatureCategoryDuplicateProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/category_duplicate';

    public function setUp() {
        parent::setUp();

        $category = $this->createTestCategory('UnitTestEmptyCategory');
        $category = $this->createTestCategory('UnitTestCategory');
        $category2 = $this->createTestCategory('UnitTestCategory2');
        $category3 = $this->createTestCategory('UnitTestCategory3');

        $feature1 = $this->createTestFeature('UnitTestFeature1', array('type' => 'number', 'caption' => 'UnitTestFeature1 Caption'));
        $feature2 = $this->createTestFeature('UnitTestFeature2');
        $feature3 = $this->createTestFeature('UnitTestFeature3');
        $feature4 = $this->createTestFeature('UnitTestFeature4');

        $catFeature = $this->createTestCategoryFeature($category->get('id'), $feature1->get('id'));

        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature1->get('id'), array('rank' => 1));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature2->get('id'), array('rank' => 2));
        $catFeature = $this->createTestCategoryFeature($category2->get('id'), $feature3->get('id'), array('rank' => 0));

        $product = $this->createTestProduct('UnitTestProduct1', $category->get('id'));
        $prodFeature = $this->createTestProductFeature($product->get('id'), $feature1->get('id'), array('value' => 100500));

    }

    public function testFeatureCategoryDuplicate() {
        /** @var msCategory $cat1 */
        $cat1 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $cat2 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory3'));
        $response = $this->getResponse(array(
            'category_from' => $cat1->get('id'),
            'category_to' => $cat2->get('id'),
            ));
        $this->assertTrue($response['success']);
        $this->assertCount(3, $response['object']['features']);
    }

    public function testFeatureCategoryDuplicateWithRepeats() {
        /** @var msCategory $cat1 */
        $cat1 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $cat2 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory'));
        $response = $this->getResponse(array(
            'category_from' => $cat1->get('id'),
            'category_to' => $cat2->get('id'),
        ));
        $this->assertTrue($response['success']);
        $this->assertCount(3, $response['object']['features']);
    }

    public function testFeatureCategoryDuplicateSelf() {
        /** @var msCategory $cat1 */
        $c_start = $this->modx->getCount('msCategoryFeature');
        $cat1 = $this->modx->getObject('msCategory', array('pagetitle' => 'UnitTestCategory2'));
        $response = $this->getResponse(array(
            'category_from' => $cat1->get('id'),
            'category_to' => $cat1->get('id'),
        ));
        $c = $this->modx->getCount('msCategoryFeature');
        $this->assertEquals($c_start, $c);
        $this->assertTrue($response['success']);
        $this->assertCount(3, $response['object']['features']);
    }


}
