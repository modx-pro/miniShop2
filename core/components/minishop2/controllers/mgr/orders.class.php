<?php

if (!class_exists('msManagerController')) {
    require_once dirname(__FILE__, 2) . '/manager.class.php';
}

class Minishop2MgrOrdersManagerController extends msManagerController
{
    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('ms2_orders') . ' | miniShop2';
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['minishop2:default', 'minishop2:product', 'minishop2:manager'];
    }

    /**
     *
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->miniShop2->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addCss($this->miniShop2->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/minishop2.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.utils.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.combo.js');

        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.form.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.grid.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.grid.logs.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.grid.products.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.panel.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.window.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.window.product.js');

        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');

        $grid_fields = array_map(
            'trim',
            explode(
                ',',
                $this->getOption(
                    'ms2_order_grid_fields',
                    null,
                    'id,customer,num,status,cost,weight,delivery,payment,createdon,updatedon,comment',
                    true
                )
            )
        );
        $grid_fields = array_values(
            array_unique(
                array_merge($grid_fields, [
                    'id',
                    'user_id',
                    'num',
                    'type',
                    'actions',
                    'color'
                ])
            )
        );

        $address_fields = array_map('trim', explode(',', $this->getOption('ms2_order_address_fields')));
        $product_fields = array_map('trim', explode(',', $this->getOption('ms2_order_product_fields', null, '')));
        $product_fields = array_values(
            array_unique(
                array_merge($product_fields, [
                    'id',
                    'product_id',
                    'name',
                    'actions'
                ])
            )
        );
        $product_options = array_map('trim', explode(',', $this->getOption('ms2_order_product_options')));

        $config = $this->miniShop2->config;
        $config['order_grid_fields'] = $grid_fields;
        $config['order_address_fields'] = $address_fields;
        $config['order_product_fields'] = $product_fields;
        $config['order_product_options_fields'] = $product_options;
        $this->addHtml(
            '
            <script>
                miniShop2.config = ' . json_encode($config) . ';

                MODx.perm.mssetting_list = ' . ($this->modx->hasPermission('mssetting_list') ? 1 : 0) . ';

                Ext.onReady(function() {
                    MODx.add({xtype: "minishop2-page-orders"});
                });
            </script>'
        );

        $this->modx->invokeEvent('msOnManagerCustomCssJs', [
            'controller' => $this,
            'page' => 'orders',
        ]);
    }
}
