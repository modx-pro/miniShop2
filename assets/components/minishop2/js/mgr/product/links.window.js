miniShop2.window.CreateProductLink = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_link'),
        width: 600,
        success: this.success,
        baseParams: {
            action: 'mgr/product/productlink/create',
        },
        fields: config.fields,
    });
    miniShop2.window.CreateProductLink.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.CreateProductLink, miniShop2.window.Default, {

    getFields: function (config) {
        return [{
            xtype: 'minishop2-combo-link',
            id: config.id + '-link',
            fieldLabel: _('ms2_link'),
            name: 'link',
            allowBlank: false,
            anchor: '99%',
        }, {
            xtype: 'minishop2-combo-product',
            id: config.id + '-product',
            fieldLabel: _('ms2_product'),
            name: 'slave',
            hiddenName: 'slave',
            allowBlank: false,
            anchor: '99%',
        }];
    },

    getButtons: function () {
        return [{
            text: _('close'),
            scope: this,
            handler: function () {
                this.hide();
            }
        }, {
            text: _('save'),
            cls: 'primary-button',
            scope: this,
            handler: function () {
                this.submit(false);
            }
        }];
    },

    success: function () {
        var product = Ext.getCmp(this.id + '-product');
        if (product) {
            product.clearValue();
        }
    },

});
Ext.reg('minishop2-product-link-create', miniShop2.window.CreateProductLink);