miniShop2.window.OrderProduct = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_update'),
        width: 600,
        baseParams: {
            action: config.action || 'mgr/orders/product/update',
        },
        modal: true,
    });
    miniShop2.window.OrderProduct.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.OrderProduct, miniShop2.window.Default, {

    getFields: function () {
        return [
            {xtype: 'hidden', name: 'id'},
            {xtype: 'hidden', name: 'order_id'},
            {
                layout: 'column',
                border: false,
                anchor: '100%',
                items: [{
                    columnWidth: .3,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [{
                        xtype: 'numberfield',
                        fieldLabel: _('ms2_product_count'),
                        name: 'count',
                        anchor: '100%',
                        allowNegative: false,
                        allowBlank: false
                    }]
                }, {
                    columnWidth: .7,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_name'),
                        name: 'name',
                        anchor: '100%'
                    }]
                }]
            }, {
                layout: 'column',
                border: false,
                anchor: '100%',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [{
                        xtype: 'numberfield',
                        decimalPrecision: 2,
                        fieldLabel: _('ms2_product_price'),
                        name: 'price',
                        anchor: '100%'
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [{
                        xtype: 'numberfield',
                        decimalPrecision: 3,
                        fieldLabel: _('ms2_product_weight'),
                        name: 'weight',
                        anchor: '100%'
                    }]
                }]
            },
            {xtype: 'textarea', fieldLabel: _('ms2_product_options'), name: 'options', height: 100, anchor: '100%'}
        ];
    },

    getKeys: function () {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            }, scope: this
        }];
    },

});
Ext.reg('minishop2-window-orderproduct-update', miniShop2.window.OrderProduct);