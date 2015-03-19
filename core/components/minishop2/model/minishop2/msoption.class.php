<?php
class msOption extends xPDOSimpleObject {
    public function getInputProperties() {
        if ($this->get('type') == 'number') {
            return '<input type="text" value="" name="option' . $this->get('id') . '">';
        }

        return '';
    }

    public function setCategories($categories) {
        $result = array();
        foreach ($categories as $category) {
            $catObj = $this->xpdo->getObject('msCategory', $category);
            if ($catObj) {
                /** @var msCategoryFeature $catFtObj */
                $catFtObj = $this->xpdo->newObject('msCategoryOption');
                $catFtObj->set('category_id', $category);
                $this->addMany($catFtObj);
                $result[] = $catObj->get('id');
            }
        }
        $this->save();

        return $result;
    }
}