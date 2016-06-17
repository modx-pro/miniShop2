<?php

if (!class_exists('msManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
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
        return array('minishop2:default', 'minishop2:product', 'minishop2:manager');
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
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.utils.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/misc/ms2.combo.js');

        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.form.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.grid.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.grid.logs.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.grid.products.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.panel.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.window.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/orders/orders.window.product.js');

        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');

        $grid_fields = array_map('trim', explode(',', $this->modx->getOption('ms2_order_grid_fields', null,
            'id,customer,num,status,cost,weight,delivery,payment,createdon,updatedon,comment', true
        )));
        $grid_fields = array_values(array_unique(array_merge($grid_fields, array(
            'id', 'user_id', 'num', 'type', 'actions'
        ))));

        $address_fields = array_map('trim', explode(',', $this->modx->getOption('ms2_order_address_fields')));
        $product_fields = array_map('trim', explode(',', $this->modx->getOption('ms2_order_product_fields', null, '')));
        $product_fields = array_values(array_unique(array_merge($product_fields, array(
            'id', 'product_id', 'name', 'actions'
        ))));

        $config = $this->miniShop2->config;
        $config['order_grid_fields'] = $grid_fields;
        $config['order_address_fields'] = $address_fields;
        $config['order_product_fields'] = $product_fields;
        $this->addHtml('
            <script type="text/javascript">
                miniShop2.config = ' . json_encode($config) . ';
                Ext.onReady(function() {
                    MODx.add({xtype: "minishop2-panel-orders"});
                });
            </script>'
        );

        $this->modx->invokeEvent('msOnManagerCustomCssJs', array(
            'controller' => &$this,
            'page' => 'orders',
        ));
    }

}