<?php

class msResourceUpdateController extends ResourceUpdateManagerController
{
    /** @var miniShop2 $miniShop2 */
    public $miniShop2;


    /**
     *
     */
    public function initialize()
    {
        parent::initialize();
        $this->miniShop2 = $this->modx->getService('miniShop2');
    }


    /**
     * @param string $script
     */
    public function addCss($script)
    {
        $script = $script . '?v=' . $this->miniShop2->version;
        parent::addCss($script);
    }


    /**
     * @param string $script
     */
    public function addJavascript($script)
    {
        $script = $script . '?v=' . $this->miniShop2->version;
        parent::addJavascript($script);
    }


    /**
     * @param string $script
     */
    public function addLastJavascript($script)
    {
        $script = $script . '?v=' . $this->miniShop2->version;
        parent::addLastJavascript($script);
    }

}