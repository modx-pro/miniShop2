<?php

abstract class msOptionType
{
    /** @var msOption $option */
    public $option;
    /** @var xPDO $xpdo */
    public $xpdo;
    /** @var array $config */
    public $config = [];
    public static $script = null;
    public static $xtype = null;

    /**
     * msOptionType constructor.
     *
     * @param msOption $option
     * @param array $config
     */
    public function __construct(msOption $option, array $config = [])
    {
        $this->option = $option;
        $this->xpdo = $option->xpdo;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param $criteria
     *
     * @return mixed|null
     *
     * @TODO Maybe vulnerable
     */
    public function getValue($criteria)
    {
        /** @var msProductOption $value */
        $value = $this->xpdo->getObject('msProductOption', $criteria);

        return ($value) ? $value->get('value') : null;
    }

    /**
     * @param $criteria
     *
     * @return mixed|null
     */
    public function getRowValue($criteria)
    {
        return $this->getValue($criteria);
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    abstract public function getField($field);
}

class msOption extends xPDOSimpleObject
{
    /**
     * @return string
     */
    public function getInputProperties()
    {
        if ($this->get('type') == 'number') {
            return '<input type="text" value="" name="option' . $this->get('id') . '">';
        }

        return '';
    }

    /**
     * @param $categories
     *
     * @return array
     */
    public function setCategories($categories)
    {
        $result = [];

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $catObj = $this->xpdo->getObject('msCategory', ['id' => $category]);
                if ($catObj) {
                    /** @var msCategoryOption $catFtObj */
                    $catFtObj = $this->xpdo->getObject(
                        'msCategoryOption',
                        ['category_id' => $category, 'option_id' => $this->get('id')]
                    );
                    if (!$catFtObj) {
                        $catFtObj = $this->xpdo->newObject('msCategoryOption');
                        $catFtObj->set('category_id', $category);
                        $catFtObj->set('value', '');
                        $catFtObj->set('active', true);
                        $this->addMany($catFtObj);
                    }
                    $result[] = $catObj->get('id');
                }
            }
            $this->save();
        }

        return $result;
    }

    /**
     * @param $product_id
     *
     * @return mixed
     */
    public function getValue($product_id)
    {
        /** @var miniShop2 $minishop */
        $minishop = $this->xpdo->getService('miniShop2');

        /** @var msOptionType $type */
        $type = $minishop->getOptionType($this);

        if ($type) {
            $criteria = [
                'product_id' => $product_id,
                'key' => $this->get('key'),
            ];
            return $type->getValue($criteria);
        } else {
            return null;
        }
    }

    /**
     * @param $product_id
     *
     * @return mixed
     */
    public function getRowValue($product_id)
    {
        /** @var miniShop2 $minishop */
        $minishop = $this->xpdo->getService('miniShop2');

        /** @var msOptionType $type */
        $type = $minishop->getOptionType($this);

        if ($type) {
            $criteria = [
                'product_id' => $product_id,
                'key' => $this->get('key'),
            ];
            return $type->getRowValue($criteria);
        } else {
            return null;
        }
    }

    /**
     * @param $field
     *
     * @return mixed|null
     */
    public function getManagerField($field)
    {
        /** @var miniShop2 $minishop */
        $minishop = $this->xpdo->getService('miniShop2');

        /** @var msOptionType $type */
        $type = $minishop->getOptionType($this);

        if ($type) {
            return $type->getField($field);
        } else {
            return null;
        }
    }
}
