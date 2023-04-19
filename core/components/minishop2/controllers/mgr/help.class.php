<?php

if (!class_exists('msManagerController')) {
    require_once dirname(__FILE__, 2) . '/manager.class.php';
}

class Minishop2MgrHelpManagerController extends msManagerController
{
    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('ms2_help') . ' | miniShop2';
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['minishop2:help'];
    }

    /**
     *
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->miniShop2->config['cssUrl'] . 'mgr/help.css');
    }

    /**
     * @param array $scriptProperties
     * @return mixed
     */
    public function process(array $scriptProperties = []) {
        $placeholders = [];
        $placeholders['logo'] = $this->miniShop2->config['defaultThumb'];
        $placeholders['changelog'] = file_get_contents(dirname(__FILE__, 3) . '/docs/changelog.txt');

        return $placeholders;
    }

    /**
     * @return string
     */
    public function getTemplateFile() {
        return dirname(__FILE__, 3) . '/templates/default/help.tpl';
    }
}
