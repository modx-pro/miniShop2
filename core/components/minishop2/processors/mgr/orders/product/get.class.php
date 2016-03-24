<?php

class msOrderProductGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'msOrderProduct';
    public $languageTopics = array('minishop2:default');
    public $permission = 'msorder_view';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

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
                if (PHP_VERSION_ID >= 50400) {
                    $array['options'] = json_encode($tmp, JSON_UNESCAPED_UNICODE);
                } else {
                    $array['options'] = $this->my_json_encode($tmp);
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
    function my_json_encode($arr)
    {
        //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
        array_walk_recursive($arr, function (&$item, $key) {
            if (is_string($item)) {
                $item = mb_encode_numericentity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
            }
        });

        return mb_decode_numericentity(json_encode($arr), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
    }

}

return 'msOrderProductGetProcessor';