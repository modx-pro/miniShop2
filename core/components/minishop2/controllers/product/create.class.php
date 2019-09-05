<?php

if (!class_exists('msResourceCreateController')) {
    require_once dirname(dirname(__FILE__)) . '/resource_create.class.php';
}

class msProductCreateManagerController extends msResourceCreateController
{
    /** @var msProduct $resource */
    public $resource;


    /**
     * Returns language topics
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('resource', 'minishop2:default', 'minishop2:product', 'minishop2:manager');
    }


    /**
     * @return int|mixed
     */
    public function getDefaultTemplate()
    {
        if (!$template = $this->modx->getOption('ms2_template_product_default')) {
            $template = parent::getDefaultTemplate();
        }

        return $template;
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
     * @param array $scriptProperties
     *
     * @return mixed
     */
    public function process(array $scriptProperties = array())
    {
        $placeholders = parent::process($scriptProperties);

        $this->resourceArray['show_in_tree'] = (int)$this->modx->getOption('ms2_product_show_in_tree_default');
        $this->resourceArray['source'] = (int)$this->modx->getOption('ms2_product_source_default');

        return $placeholders;

    }


    /**
     * Check if content field is hidden
     * @return bool
     */
    public function hideContent()
    {
        $userGroups = $this->modx->user->getUserGroups();
        $c = $this->modx->newQuery('modActionDom');
        $c->innerJoin('modFormCustomizationSet','FCSet');
        $c->innerJoin('modFormCustomizationProfile','Profile','FCSet.profile = Profile.id');
        $c->leftJoin('modFormCustomizationProfileUserGroup','ProfileUserGroup','Profile.id = ProfileUserGroup.profile');
        $c->leftJoin('modFormCustomizationProfile','UGProfile','UGProfile.id = ProfileUserGroup.profile');
        $c->where(array(
            'modActionDom.action:IN' => ['resource/*', 'resource/create'],
            'modActionDom.name' => 'modx-resource-content',
            'modActionDom.container' => 'modx-panel-resource',
            'modActionDom.rule' => 'fieldVisible',
            'modActionDom.active' => true,
            'FCSet.template:IN' => [0, $this->resource->template],
            'FCSet.active' => true,
            'Profile.active' => true,
        ));
        $c->where(array(
            array(
                'ProfileUserGroup.usergroup:IN' => $userGroups,
                array(
                    'OR:ProfileUserGroup.usergroup:IS' => null,
                    'AND:UGProfile.active:=' => true,
                ),
            ),
            'OR:ProfileUserGroup.usergroup:=' => null,
        ), xPDOQuery::SQL_AND, null, 2);
        
        return (bool) $this->modx->getCount('modActionDom', $c);
    }


    /**
     * Register custom CSS/JS for the page
     * @return void
     */
    public function loadCustomCssJs()
    {
        $mgrUrl = $this->modx->getOption('manager_url', null, MODX_MANAGER_URL);
        $assetsUrl = $this->miniShop2->config['assetsUrl'];

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
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/category.tree.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/product.common.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/create.js');

        // Customizable product fields feature
        $product_fields = array_merge($this->resource->getAllFieldsNames(), array('syncsite'));
        $product_data_fields = $this->resource->getDataFieldsNames();

        if (!$product_main_fields = $this->modx->getOption('ms2_product_main_fields')) {
            $product_main_fields = 'pagetitle,longtitle,introtext,content,publishedon,pub_date,unpub_date,template,
                parent,alias,menutitle,searchable,cacheable,richtext,uri_override,uri,hidemenu,show_in_tree';
        }
        $product_main_fields = array_map('trim', explode(',', $product_main_fields));
        $product_main_fields = array_values(array_intersect($product_main_fields, $product_fields));

        if (!$product_extra_fields = $this->modx->getOption('ms2_product_extra_fields')) {
            $product_extra_fields = 'article,price,old_price,weight,color,remains,reserved,vendor,made_in,tags';
        }
        $product_extra_fields = array_map('trim', explode(',', $product_extra_fields));
        $product_extra_fields = array_values(array_intersect($product_extra_fields, $product_fields));
        $product_option_fields = $this->resource->loadData()->getOptionFields();
        //---

        $config = array(
            'assets_url' => $this->miniShop2->config['assetsUrl'],
            'connector_url' => $this->miniShop2->config['connectorUrl'],
            'show_comments' => false,
            'show_gallery' => false,
            'show_extra' => (bool)$this->modx->getOption('ms2_product_tab_extra', null, true),
            'show_options' => (bool)$this->modx->getOption('ms2_product_tab_options', null, true),
            'show_links' => (bool)$this->modx->getOption('ms2_product_tab_links', null, true),
            'show_categories' => (bool)$this->modx->getOption('ms2_product_tab_categories', null, true),
            'default_thumb' => $this->miniShop2->config['defaultThumb'],
            'main_fields' => $product_main_fields,
            'extra_fields' => $product_extra_fields,
            'option_fields' => $product_option_fields,
            'product_tab_extra' => (bool)$this->modx->getOption('ms2_product_tab_extra', null, true),
            'product_tab_gallery' => (bool)$this->modx->getOption('ms2_product_tab_gallery', null, true),
            'product_tab_links' => (bool)$this->modx->getOption('ms2_product_tab_links', null, true),
            'data_fields' => $product_data_fields,
            'additional_fields' => array(),
            'hideContent' => $this->hideContent(),
        );

        $ready = array(
            'xtype' => 'minishop2-page-product-create',
            'resource' => $this->resource->get('id'),
            'record' => $this->resourceArray,
            'publish_document' => $this->canPublish,
            'canSave' => $this->canSave,
            'canEdit' => $this->canEdit,
            'canCreate' => $this->canCreate,
            'canDuplicate' => $this->canDuplicate,
            'canDelete' => $this->canDelete,
            'canPublish' => $this->canPublish,
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
        $this->modx->invokeEvent('msOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'product_create'));
        $this->loadPlugins();
    }


    /**
     * Loads additional scripts for product form from miniShop2 plugins
     */
    function loadPlugins()
    {
        $plugins = $this->miniShop2->loadPlugins();
        foreach ($plugins as $plugin) {
            if (!empty($plugin['manager']['msProductData'])) {
                $this->addJavascript($plugin['manager']['msProductData']);
            }
        }
    }
}
