<?php

if (!class_exists('msResourceUpdateController')) {
    require_once dirname(dirname(__FILE__)) . '/resource_update.class.php';
}

class msCategoryUpdateManagerController extends msResourceUpdateController
{
    /** @var msCategory $resource */
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
     */
    public function loadCustomCssJs()
    {
        $mgrUrl = $this->modx->getOption('manager_url', null, MODX_MANAGER_URL);
        $assetsUrl = $this->miniShop2->config['assetsUrl'];

        /** @var msProduct $product */
        $product = $this->modx->newObject('msProduct');
        $product_fields = array_merge(
            $product->getAllFieldsNames(),
            array('actions', 'preview_url', 'cls', 'vendor_name', 'category_name')
        );

        if (!$category_grid_fields = $this->modx->getOption('ms2_category_grid_fields')) {
            $category_grid_fields = 'id,pagetitle,article,price,weight,image';
        }

        $category_grid_fields = array_map('trim', explode(',', $category_grid_fields));
        $grid_fields = array_values(array_intersect($category_grid_fields, $product_fields));
        if (!in_array('actions', $grid_fields)) {
            $grid_fields[] = 'actions';
        }

        if ($this->resource instanceof msCategory) {
            $neighborhood = $this->resource->getNeighborhood();
        }

        $this->addCss($assetsUrl . 'css/mgr/main.css');
        $this->addCss($assetsUrl . 'css/mgr/bootstrap.buttons.css');
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
        $this->addJavascript($assetsUrl . 'js/mgr/category/category.common.js');
        $this->addJavascript($assetsUrl . 'js/mgr/category/option.grid.js');
        $this->addJavascript($assetsUrl . 'js/mgr/category/option.windows.js');
        $this->addJavascript($assetsUrl . 'js/mgr/category/product.grid.js');
        $this->addLastJavascript($assetsUrl . 'js/mgr/category/update.js');

        $showComments = (int)(class_exists('TicketsSection') && $this->modx->getOption('ms2_category_show_comments'));
        $config = array(
            'assets_url' => $this->miniShop2->config['assetsUrl'],
            'connector_url' => $this->miniShop2->config['connectorUrl'],
            'show_comments' => $showComments,
            'product_fields' => $product_fields,
            'grid_fields' => $grid_fields,
            'default_thumb' => $this->miniShop2->config['defaultThumb'],
        );
        $ready = array(
            'xtype' => 'minishop2-page-category-update',
            'resource' => $this->resource->get('id'),
            'record' => $this->resourceArray,
            'publish_document' => $this->canPublish,
            'preview_url' => $this->previewUrl,
            'locked' => $this->locked,
            'lockedText' => $this->lockedText,
            'canSave' => $this->modx->hasPermission('mscategory_save'),
            'canEdit' => $this->canEdit,
            'canCreate' => $this->canCreate,
            'canDuplicate' => $this->canDuplicate,
            'canDelete' => $this->canDelete,
            'canPublish' => $this->canPublish,
            'show_tvs' => !empty($this->tvCounts),
            'next_page' => !empty($neighborhood['right'][0]) ? $neighborhood['right'][0] : 0,
            'prev_page' => !empty($neighborhood['left'][0]) ? $neighborhood['left'][0] : 0,
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
        $this->modx->invokeEvent('msOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'category_update'));
        $this->loadPlugins();

        // Load Tickets
        if ($showComments) {
            $this->loadTickets();
        }
    }


    /**
     * Used to set values on the resource record sent to the template for derivative classes
     *
     * @return void
     */
    public function prepareResource()
    {
        $settings = $this->resource->getProperties('ms2');
        if (is_array($settings) && !empty($settings)) {
            foreach ($settings as $k => $v) {
                $this->resourceArray['setting_' . $k] = $v;
            }
        }
    }


    /**
     * Loads component Tickets for displaying comments
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
                connector_url: "' . $connectorUrl . '",
            };
            // ]]>
            </script>');
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

}
