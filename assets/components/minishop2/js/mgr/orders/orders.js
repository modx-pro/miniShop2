miniShop2.page.Orders = function (config) {
    config = config || {};
    Ext.apply(config, {
        formpanel: 'minishop2-panel-orders',
        cls: 'container',
        buttons: this.getButtons(config),
        components: [{
            xtype: 'minishop2-panel-orders'
        }]
    });
    miniShop2.page.Orders.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.page.Orders, MODx.Component, {
    getButtons: function (config) {
        var b = [];

        if (MODx.perm.mssetting_list) {
            b.push({
                text: _('ms2_settings')
                ,id: 'ms2-abtn-settings'
                ,handler: function () {
                    MODx.loadPage('?', 'a=mgr/settings&namespace=minishop2');
                }
            });
        }

        return b;
    }
});
Ext.reg('minishop2-page-orders', miniShop2.page.Orders);
