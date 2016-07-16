miniShop2.window.UpdateOrder = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_update'),
        width: 750,
        baseParams: {
            action: 'mgr/orders/update',
        },
    });
    miniShop2.window.UpdateOrder.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.UpdateOrder, miniShop2.window.Default, {

    getFields: function (config) {
        return {
            xtype: 'modx-tabs',
            activeTab: config.activeTab || 0,
            bodyStyle: {background: 'transparent'},
            deferredRender: false,
            autoHeight: true,
            stateful: true,
            stateId: 'minishop2-window-order-update',
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            items: this.getTabs(config)
        };
    },

    getTabs: function (config) {
        var tabs = [{
            title: _('ms2_order'),
            hideMode: 'offsets',
            //bodyStyle: 'padding:5px 0;',
            defaults: {msgTarget: 'under', border: false},
            items: this.getOrderFields(config)
        }, {
            xtype: 'minishop2-grid-order-products',
            title: _('ms2_order_products'),
            order_id: config.record.id
        }];

        var address = this.getAddressFields(config);
        if (address.length > 0) {
            tabs.push({
                layout: 'form',
                title: _('ms2_address'),
                hideMode: 'offsets',
                bodyStyle: 'padding:5px 0;',
                defaults: {msgTarget: 'under', border: false},
                items: address
            });
        }

        tabs.push({
            xtype: 'minishop2-grid-order-logs',
            title: _('ms2_order_log'),
            order_id: config.record.id
        });

        return tabs;
    },

    getOrderFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            layout: 'column',
            defaults: {msgTarget: 'under', border: false},
            style: 'padding:15px 5px;text-align:center;',
            items: [{
                columnWidth: .5,
                layout: 'form',
                items: [{
                    xtype: 'minishop2-combo-user',
                    name: 'user_id',
                    fieldLabel: _('ms2_user'),
                    anchor: '95%',
                }]
            }, {
                columnWidth: .5,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'cost',
                    fieldLabel: _('ms2_order_cost'),
                    anchor: '100%',
                    style: 'font-size:1.1em;'
                }]
            }]
        }, {
            xtype: 'fieldset',
            layout: 'column',
            style: 'padding:15px 5px;text-align:center;',
            defaults: {msgTarget: 'under', border: false},
            items: [{
                columnWidth: .33,
                layout: 'form',
                items: [
                    {xtype: 'displayfield', name: 'num', fieldLabel: _('ms2_num'), anchor: '100%'},
                    {xtype: 'displayfield', name: 'cart_cost', fieldLabel: _('ms2_cart_cost'), anchor: '100%'}
                ]
            }, {
                columnWidth: .33,
                layout: 'form',
                items: [
                    {xtype: 'displayfield', name: 'createdon', fieldLabel: _('ms2_createdon'), anchor: '100%'},
                    {xtype: 'displayfield', name: 'delivery_cost', fieldLabel: _('ms2_delivery_cost'), anchor: '100%'}
                ]
            }, {
                columnWidth: .33,
                layout: 'form',
                items: [
                    {xtype: 'displayfield', name: 'updatedon', fieldLabel: _('ms2_updatedon'), anchor: '100%'},
                    {xtype: 'displayfield', name: 'weight', fieldLabel: _('ms2_weight'), anchor: '100%'}
                ]
            }]
        }, {
            layout: 'column',
            defaults: {msgTarget: 'under', border: false},
            anchor: '100%',
            items: [{
                columnWidth: .48,
                layout: 'form',
                items: [{
                    xtype: 'minishop2-combo-status',
                    name: 'status',
                    fieldLabel: _('ms2_status'),
                    anchor: '100%',
                    order_id: config.record.id
                }, {
                    xtype: 'minishop2-combo-delivery',
                    name: 'delivery',
                    fieldLabel: _('ms2_delivery'),
                    anchor: '100%'
                }, {
                    xtype: 'minishop2-combo-payment',
                    name: 'payment',
                    fieldLabel: _('ms2_payment'),
                    anchor: '100%',
                    delivery_id: config.record.delivery
                }]
            }, {
                columnWidth: .5,
                layout: 'form',
                items: [
                    {xtype: 'textarea', name: 'comment', fieldLabel: _('ms2_comment'), anchor: '100%', height: 170}
                ]
            }]
        }];
    },

    getAddressFields: function (config) {
        var all = {
            receiver: {},
            phone: {},
            index: {},
            country: {},
            region: {},
            metro: {},
            building: {},
            city: {},
            street: {},
            room: {}
        };
        var fields = [], tmp = [];
        for (var i = 0; i < miniShop2.config['order_address_fields'].length; i++) {
            var field = miniShop2.config['order_address_fields'][i];
            if (all[field]) {
                Ext.applyIf(all[field], {
                    xtype: 'textfield',
                    name: 'addr_' + field,
                    fieldLabel: _('ms2_' + field)
                });
                all[field].anchor = '100%';
                tmp.push(all[field]);
            }
        }

        var addx = function (w1, w2) {
            if (!w1) {
                w1 = .5;
            }
            if (!w2) {
                w2 = .5;
            }
            return {
                layout: 'column',
                defaults: {msgTarget: 'under', border: false},
                items: [
                    {columnWidth: w1, layout: 'form', items: []},
                    {columnWidth: w2, layout: 'form', items: []}
                ]
            };
        };

        var n;
        if (tmp.length > 0) {
            for (i = 0; i < tmp.length; i++) {
                if (i == 0) fields.push(addx(.7, .3));
                else if (i == 2) fields.push(addx(.3, .7));
                else if (i % 2 == 0) fields.push(addx());

                if (i <= 1) {
                    n = 0;
                }
                else {
                    n = Math.floor(i / 2);
                }
                fields[n].items[i % 2].items.push(tmp[i]);
            }
            if (miniShop2.config['order_address_fields'].in_array('comment')) {
                fields.push(
                    {
                        xtype: 'displayfield',
                        name: 'addr_comment',
                        fieldLabel: _('ms2_comment'),
                        anchor: '98%',
                        style: 'min-height: 50px;border:1px solid #efefef;width:95%;'
                    }
                );
            }
        }

        return fields;
    },

    getKeys: function () {
        return {
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }
    },

});
Ext.reg('minishop2-window-order-update', miniShop2.window.UpdateOrder);
