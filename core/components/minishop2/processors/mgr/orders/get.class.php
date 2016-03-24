<?php

class msOrderGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'msOrder';
    public $languageTopics = array('minishop2:default');
    public $permission = 'msorder_view';
    /** @var  miniShop2 $ms2 */
    protected $ms2;


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $this->ms2 = $this->modx->getService('miniShop2');

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
        $array = $this->object->toArray();
        if ($address = $this->object->getOne('Address')) {
            $array = array_merge($array, $address->toArray('addr_'));
        }
        if ($profile = $this->object->getOne('UserProfile')) {
            $array['fullname'] = $profile->get('fullname');
        } else {
            $array['fullname'] = $this->modx->lexicon('no');
        }

        $array['createdon'] = $this->ms2->formatDate($array['createdon']);
        $array['updatedon'] = $this->ms2->formatDate($array['updatedon']);

        return $this->success('', $array);
    }
}

return 'msOrderGetProcessor';