<?php

class msOptionGetTypesProcessorTest extends MODxProcessorTestCase {

    public $processor = 'mgr/settings/option/gettypes';

    public function setUp() {
        parent::setUp();
    }

    public function testGetAllTypes() {
        $response = $this->getResponse(array());
        $this->assertTrue($response['success']);
        $data = $response['results'];
        $this->assertGreaterThan(0, count($data));
        $this->assertTrue(isset($data[0]['name']));
        $this->assertTrue(isset($data[0]['caption']));
        $types = array(
            "combo-boolean",
            "combo-multiple",
            "combobox",
            "numberfield",
            "textarea",
            "textfield",
        );
        foreach ($types as $type) {
            $this->assertContains(array(
                'name' => $type,
                'caption' => $this->modx->lexicon('ms2_ft_'.$type),
            ), $data);
        }
    }

}
