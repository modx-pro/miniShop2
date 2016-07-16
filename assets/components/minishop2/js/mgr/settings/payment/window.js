miniShop2.window.CreatePayment = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_payment'),
        width: 600,
        baseParams: {
            action: 'mgr/settings/payment/create',
        },
    });
    miniShop2.window.CreatePayment.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.CreatePayment, miniShop2.window.Default, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id'
        }, {
            layout: 'column',
            items: [{
                columnWidth: .7,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _('ms2_name'),
                    name: 'name',
                    anchor: '99%',
                    id: config.id + '-name'
                }]
            }, {
                columnWidth: .3,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _('ms2_add_cost'),
                    name: 'price',
                    description: _('ms2_add_cost_help'),
                    anchor: '99%',
                    id: config.id + '-price'
                }],
            }]

        }, {
            columnWidth: .5,
            layout: 'form',
            defaults: {msgTarget: 'under'},
            items: [{
                xtype: 'minishop2-combo-classes',
                type: 'payment',
                fieldLabel: _('ms2_class'),
                name: 'class',
                anchor: '99%',
                id: config.id + '-class',
            }],
        }, {
            xtype: 'minishop2-combo-browser',
            fieldLabel: _('ms2_logo'),
            name: 'logo',
            anchor: '99%',
            id: config.id + '-logo'
        }, {
            xtype: 'textarea',
            fieldLabel: _('ms2_description'),
            name: 'description',
            anchor: '99%',
            id: config.id + '-description'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('ms2_active'),
            hideLabel: true,
            name: 'active',
            id: config.id + '-active'
        }];
    },
});
Ext.reg('minishop2-window-payment-create', miniShop2.window.CreatePayment);


miniShop2.window.UpdatePayment = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/payment/update',
        },
        bodyCssClass: 'tabs',
    });
    miniShop2.window.UpdatePayment.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.UpdatePayment, miniShop2.window.CreatePayment, {

    getFields: function (config) {
        return [{
            xtype: 'modx-tabs',
            items: [{
                title: _('ms2_payment'),
                layout: 'form',
                items: miniShop2.window.CreatePayment.prototype.getFields.call(this, config),
            }, {
                title: _('ms2_deliveries'),
                items: [{
                    xtype: 'minishop2-grid-payment-deliveries',
                    record: config.record,
                }]
            }]
        }];
    }

});
Ext.reg('minishop2-window-payment-update', miniShop2.window.UpdatePayment);