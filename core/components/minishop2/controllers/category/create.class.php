<?php

if (!class_exists('msResourceCreateController')) {
    require_once dirname(dirname(__FILE__)) . '/resource_create.class.php';
}

class msCategoryCreateManagerController extends msResourceCreateController
{
    /**
     * Returns language topics
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('resource', 'minishop2:default', 'minishop2:product', 'minishop2:manager');
    }


    /**
     * Check for any permissions or requirements to load page
     * @return bool
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('new_document');
    }


    /**
     * Return the default template for this resource
     * @return int|mixed
     */
    public function getDefaultTemplate()
    {
        if (!$template = $this->modx->getOption('ms2_template_category_default')) {
            $template = parent::getDefaultTemplate();
        }

        return $template;
    }


    /**
     * Register custom CSS/JS for the page
     * @return void
     */
    public function loadCustomCssJs()
    {
        $miniShop2 = $this->modx->getService('miniShop2');
        $mgrUrl = $this->modx->getOption('manager_url', null, MODX_MANAGER_URL);
        $assetsUrl = $miniShop2->config['assetsUrl'];

        $this->addCss($assetsUrl . 'css/mgr/main.css');
        $this->addJavascript($mgrUrl . 'assets/modext/util/datetime.js');
        $this->addJavascript($mgrUrl . 'assets/modext/widgets/element/modx.panel.tv.renders.js');
        $this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
        $this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.tv.js');
        $this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->addJavascript($mgrUrl . 'assets/modext/sections/resource/create.js');
        $this->addJavascript($assetsUrl . 'js/mgr/minishop2.js');
        $this->addJavascript($assetsUrl . 'js/mgr/misc/ms2.combo.js');
        $this->addJavascript($assetsUrl . 'js/mgr/misc/ms2.utils.js');
        $this->addJavascript($assetsUrl . 'js/mgr/category/category.common.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/category/create.js');

        $config = array(
            'assets_url' => $miniShop2->config['assetsUrl'],
            'connector_url' => $miniShop2->config['connectorUrl'],
        );
        $ready = array(
            'xtype' => 'minishop2-page-category-create',
            'record' => array_merge($this->resourceArray, array(
                'isfolder' => true,
            )),
            'publish_document' => $this->canPublish,
            'canSave' => $this->modx->hasPermission('mscategory_save'),
            'show_tvs' => !empty($this->tvCounts),
            'mode' => 'create',
        );

        $this->addHtml('
        <script type="text/javascript">
        // <![CDATA[
            MODx.config.publish_document = "' . $this->canPublish . '";
            MODx.onDocFormRender = "' . $this->onDocFormRender . '";
            MODx.ctx = "' . $this->ctx . '";
            miniShop2.config = ' . json_encode($config) . ';
            Ext.onReady(function() {
                MODx.load(' . json_encode($ready) . ');
            });
        // ]]>
        </script>');

        // load RTE
        $this->loadRichTextEditor();
        $this->modx->invokeEvent('msOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'category_create'));
    }
}
