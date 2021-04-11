miniShop2.page.Settings = function (config) {
    config = config || {};
    Ext.apply(config, {
        formpanel: 'minishop2-panel-settings',
        cls: 'container',
        buttons: this.getButtons(),
        components: [{
            xtype: 'minishop2-panel-settings'
        }]
    });
    miniShop2.page.Settings.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.page.Settings, MODx.Component, {
    getButtons: function (config) {
        var b = [];

        if (MODx.perm.msorder_list) {
            b.push({
                text: _('ms2_orders'),
                id: 'ms2-abtn-orders',
                cls: 'primary-button',
                handler: function () {
                    MODx.loadPage('?', 'a=mgr/orders&namespace=minishop2');
                }
            });
        }

        return b;
    }
});
Ext.reg('minishop2-page-settings', miniShop2.page.Settings);
