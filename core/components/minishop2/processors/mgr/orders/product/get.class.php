<?php

class msOrderProductGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'msOrderProduct';
    public $languageTopics = ['minishop2:default'];
    public $permission = 'msorder_view';
    private $product_options = [];

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        $this->product_options = array_map('trim', explode(',', $this->modx->getOption('ms_order_product_options')));

        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function cleanup()
    {
        $array = $this->object->toArray('', true);
        if ($tmp = json_decode($array['options'], true)) {
            if (is_array($tmp)) {
                foreach ($tmp as $key => $value) {
                    if ($this->checkOptionField($value)) {
                        $array['option-' . $key] = $value;
                    }
                }
                if (PHP_VERSION_ID >= 50400) {
                    $array['options'] = json_encode($tmp, JSON_UNESCAPED_UNICODE);
                } else {
                    $array['options'] = $this->myJsonEncode($tmp);
                }
            }
        }

        if ($product = $this->object->getOne('Product')) {
            $array = array_merge($product->toArray(), $array);
            if (empty($array['name'])) {
                $array['name'] = $array['pagetitle'];
            }
        }

        return $this->success('', $array);
    }

    /**
     * @param $arr
     *
     * @return string
     */
    private function myJsonEncode($arr)
    {
        //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
        array_walk_recursive($arr, function (&$item, $key) {
            if (is_string($item)) {
                $item = mb_encode_numericentity($item, [0x80, 0xffff, 0, 0xffff], 'UTF-8');
            }
        });

        return mb_decode_numericentity(json_encode($arr), [0x80, 0xffff, 0, 0xffff], 'UTF-8');
    }

    private function checkOptionField($option)
    {
        if (is_array($option)) {
            return false;
        }

        if (is_array(json_decode($option, true))) {
            return false;
        }

        if (in_array($option, $this->product_options)) {
            return false;
        }

        return true;
    }
}

return 'msOrderProductGetProcessor';
