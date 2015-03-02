<?php

class msFeatureGetTypesProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/feature/gettypes';

    public function setUp() {
        parent::setUp();
    }

    public function testGetAllTypes() {
        $response = $this->getResponse(array());
        $this->assertEquals(true, $response['success']);
        $data = $response['results'];
        $this->assertGreaterThan(0, count($data));
        $this->assertTrue(isset($data[0]['name']));
        $this->assertTrue(isset($data[0]['caption']));

        $this->assertTrue(class_exists('msNumberType'));
    }

}
