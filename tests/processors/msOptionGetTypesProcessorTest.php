<?php

class msOptionGetTypesProcessorTest extends MODxProcessorTestCase
{

    public $processor = 'mgr/settings/option/gettypes';

    public function testGetAllTypes()
    {
        $response = $this->getResponse(array());
        $this->assertTrue($response['success']);
        $data = $response['results'];
        $this->assertGreaterThan(0, count($data));
        $this->assertTrue(isset($data[0]['name']));
        $this->assertTrue(isset($data[0]['caption']));
        $types = array(
            "combo-boolean" => null,
            "combo-multiple" => 'minishop2-grid-combobox-options',
            "combobox" => 'minishop2-grid-combobox-options',
            "numberfield" => null,
            "textarea" => null,
            "textfield" => null,
        );
        foreach ($types as $type => $xtype) {
            $this->assertContains(array(
                'name' => $type,
                'caption' => $this->modx->lexicon('ms2_ft_' . $type),
                'xtype' => $xtype
            ), $data);
        }
    }
}
