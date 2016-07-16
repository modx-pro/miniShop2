<?php

if (!class_exists('msResourceUpdateController')) {
    require_once dirname(dirname(__FILE__)) . '/resource_update.class.php';
}

class msProductUpdateManagerController extends msResourceUpdateController
{
    /** @var msProduct $resource */
    public $resource;


    /**
     * Returns language topics
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('resource', 'minishop2:default', 'minishop2:product', 'minishop2:manager', 'tickets:default');
    }


    /**
     * Check for any permissions or requirements to load page
     * @return bool
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('edit_document');
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
        $this->addJavascript($mgrUrl . 'assets/modext/sections/resource/update.js');
        $this->addJavascript($assetsUrl . 'js/mgr/minishop2.js');
        $this->addJavascript($assetsUrl . 'js/mgr/misc/ms2.combo.js');
        $this->addJavascript($assetsUrl . 'js/mgr/misc/ms2.utils.js');
        $this->addJavascript($assetsUrl . 'js/mgr/misc/default.grid.js');
        $this->addJavascript($assetsUrl . 'js/mgr/misc/default.window.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/category.tree.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/links.grid.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/links.window.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/product.common.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/product/update.js');

        $show_gallery = $this->modx->getOption('ms2_product_tab_gallery', null, true);
        if ($show_gallery) {
            $this->addLastJavascript($assetsUrl . 'js/mgr/misc/plupload/plupload.full.min.js');
            $this->addLastJavascript($assetsUrl . 'js/mgr/misc/ext.ddview.js');
            $this->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.panel.js');
            $this->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.toolbar.js');
            $this->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.view.js');
            $this->addLastJavascript($assetsUrl . 'js/mgr/product/gallery/gallery.window.js');
        }

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

        $this->prepareFields();
        //---

        $show_comments = class_exists('Ticket') && $this->modx->getOption('ms2_product_show_comments');
        if ($show_comments) {
            $this->loadTickets();
        }
        $neighborhood = array();
        if ($this->resource instanceof msProduct) {
            $neighborhood = $this->resource->getNeighborhood();
        }

        $config = array(
            'assets_url' => $this->miniShop2->config['assetsUrl'],
            'connector_url' => $this->miniShop2->config['connectorUrl'],
            'show_comments' => $show_comments,
            'show_gallery' => $show_gallery,
            'show_extra' => (bool)$this->modx->getOption('ms2_product_tab_extra', null, true),
            'show_options' => (bool)$this->modx->getOption('ms2_product_tab_options', null, true),
            'show_links' => (bool)$this->modx->getOption('ms2_product_tab_links', null, true),
            'show_categories' => (bool)$this->modx->getOption('ms2_product_tab_categories', null, true),
            'default_thumb' => $this->miniShop2->config['defaultThumb'],
            'main_fields' => $product_main_fields,
            'extra_fields' => $product_extra_fields,
            'option_fields' => $product_option_fields,
            'data_fields' => $product_data_fields,
            'additional_fields' => array(),
            'media_source' => $this->getSourceProperties(),
        );

        $ready = array(
            'xtype' => 'minishop2-page-product-update',
            'resource' => $this->resource->get('id'),
            'record' => $this->resourceArray,
            'publish_document' => $this->canPublish,
            'preview_url' => $this->previewUrl,
            'locked' => $this->locked,
            'lockedText' => $this->lockedText,
            'canSave' => $this->canSave,
            'canEdit' => $this->canEdit,
            'canCreate' => $this->canCreate,
            'canDuplicate' => $this->canDuplicate,
            'canDelete' => $this->canDelete,
            'canPublish' => $this->canPublish,
            'show_tvs' => !empty($this->tvCounts),
            'next_page' => !empty($neighborhood['right'][0])
                ? $neighborhood['right'][0]
                : 0,
            'prev_page' => !empty($neighborhood['left'][0])
                ? $neighborhood['left'][0]
                : 0,
            'up_page' => $this->resource->parent,
            'mode' => 'update',
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
        $this->modx->invokeEvent('msOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'product_update'));
        $this->loadPlugins();
    }


    /**
     * Loads Tickets component to display comments
     */
    public function loadTickets()
    {
        /** @var Tickets $Tickets */
        if (!$Tickets = $this->modx->getService('Tickets')) {
            return;
        }
        if (method_exists($Tickets, 'loadManagerFiles')) {
            $Tickets->loadManagerFiles($this, array(
                'config' => true,
                'utils' => true,
                'css' => true,
                'comments' => true,
            ));
        } else {
            $ticketsAssetsUrl = $Tickets->config['assetsUrl'];
            $connectorUrl = $ticketsAssetsUrl . 'connector.php';
            $ticketsJsUrl = $ticketsAssetsUrl . 'js/mgr/';

            $this->addJavascript($ticketsJsUrl . 'tickets.js');
            $this->addLastJavascript($ticketsJsUrl . 'misc/utils.js');
            $this->addLastJavascript($ticketsJsUrl . 'comment/comments.common.js');
            $this->addLastJavascript($ticketsJsUrl . 'comment/comments.grid.js');
            $this->addHtml('
			<script type="text/javascript">
			// <![CDATA[
			Tickets.config = {
				assets_url: "' . $ticketsAssetsUrl . '",
				connector_url: "' . $connectorUrl . '"
			};
			// ]]>
			</script>');
        }
    }


    /**
     * Additional preparation of the resource fields
     */
    function prepareFields()
    {
        $data = array_keys($this->modx->getFieldMeta('msProductData'));
        foreach ($this->resourceArray as $k => $v) {
            if (is_array($v) && in_array($k, $data)) {
                $tmp = $this->resourceArray[$k];
                $this->resourceArray[$k] = array();
                foreach ($tmp as $v2) {
                    if (!empty($v2)) {
                        $this->resourceArray[$k][] = array('value' => $v2);
                    }
                }
            }
        }

        if (empty($this->resourceArray['vendor'])) {
            $this->resourceArray['vendor'] = '';
        }
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


    /**
     * Loads media source properties
     *
     * @return array
     */
    function getSourceProperties()
    {
        $properties = array();
        /** @var $source modMediaSource */
        if ($source = $this->resource->initializeMediaSource()) {
            $tmp = $source->getProperties();
            $properties = array();
            foreach ($tmp as $v) {
                $properties[$v['name']] = $v['value'];
            }
        }

        return $properties;
    }
}
