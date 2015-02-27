<?php
class msFeature extends xPDOSimpleObject {


    public function getInputProperties() {
        if ($this->get('type') == 'number') {
            return '<input type="text" value="" name="feature' . $this->get('id') . '">';
        }

        return '';
    }
}