<?php

class msProductLinkRemoveProcessor extends modObjectRemoveProcessor
{
    public $checkRemovePermission = true;
    public $classKey = 'msLink';
    public $languageTopics = array('minishop2');
    public $permission = 'msproduct_save';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @return array|string
     */
    public function process()
    {
        $canRemove = $this->beforeRemove();
        if ($canRemove !== true) {
            return $this->failure($canRemove);
        }

        $link = $this->getProperty('link');
        $master = $this->getProperty('master');
        $slave = $this->getProperty('slave');

        if (!$link || !$master || !$slave) {
            return $this->failure('Wrong object key');
        }

        /** @var msLink $msLink */
        if (!$msLink = $this->modx->getObject('msLink', $link)) {
            return $this->failure($this->modx->lexicon('ms2_err_no_link'));
        }
        $type = $msLink->get('type');

        $q = $this->modx->newQuery('msProductLink');
        $q->command('DELETE');
        $q->where(array('link' => $link));
        switch ($type) {
            case 'many_to_many':
            case 'one_to_one':
                $q->where(array('master' => $slave, 'OR:slave:=' => $slave));
                break;

            case 'many_to_one':
            case 'one_to_many':
                $q->where(array('master' => $master, 'slave' => $slave));
                break;
        }
        $q->prepare();
        $q->stmt->execute();

        return $this->success('');
    }

}

return 'msProductLinkRemoveProcessor';
