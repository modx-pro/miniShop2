miniShop2.panel.Orders = function (config) {
    config = config || {};

    Ext.apply(config, {
        cls: 'container',
        items: [{
            xtype: 'modx-tabs',
            id: 'minishop2-orders-tabs',
            stateful: true,
            stateId: 'minishop2-orders-tabs',
            stateEvents: ['tabchange'],
            getState: function () {
                return {
                    activeTab: this.items.indexOf(this.getActiveTab())
                };
            },
            deferredRender: false,
            items: [{
                title: _('ms2_orders'),
                layout: 'anchor',
                items: [{
                    xtype: 'minishop2-form-orders',
                    id: 'minishop2-form-orders',
                }, {
                    xtype: 'minishop2-grid-orders',
                    id: 'minishop2-grid-orders',
                }],
            }]
        }]
    });
    miniShop2.panel.Orders.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.Orders, MODx.Panel);
Ext.reg('minishop2-panel-orders', miniShop2.panel.Orders);