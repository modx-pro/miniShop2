miniShop2.page.CreateProduct = function (config) {
    config = config || {record: {}};
    config.record = config.record || {};

    Ext.applyIf(config, {
        panelXType: 'minishop2-panel-product-create',
        mode: 'create'
    });
    miniShop2.page.CreateProduct.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.page.CreateProduct, MODx.page.CreateResource, {

    getButtons: function (config) {
        var buttons = [];
        var originals = MODx.page.CreateResource.prototype.getButtons.call(this, config);
        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var button = originals[i];
            switch (button.id) {
                case 'modx-abtn-save':
                    button.text = '<i class="icon icon-check"></i> ' + button.text;
                    break;
                case 'modx-abtn-cancel':
                    button.text = '<i class="icon icon-ban"></i> ' + button.text;
                    button.handler = this.cancel;
                    break;
                case 'modx-abtn-help':
                    button.text = '<i class="icon icon-question-circle"></i>';
                    break;
            }
            buttons.push(button)
        }

        return buttons;
    },

    cancel: function () {
        var id = MODx.request.parent;
        var action = id != 0
            ? 'resource/update'
            : 'welcome';

        var fp = Ext.getCmp(this.config.formpanel);
        if (fp && fp.isDirty() && MODx.config['confirm_navigation'] == 1) {
            Ext.Msg.confirm(_('warning'), _('resource_cancel_dirty_confirm'), function (e) {
                if (e == 'yes') {
                    fp.warnUnsavedChanges = false;
                    MODx.loadPage(action, 'id=' + id)
                }
            }, this);
        } else {
            MODx.loadPage(action, 'id=' + id)
        }
    },

});
Ext.reg('minishop2-page-product-create', miniShop2.page.CreateProduct);


miniShop2.panel.CreateProduct = function (config) {
    config = config || {};
    miniShop2.panel.CreateProduct.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.CreateProduct, miniShop2.panel.Product);
Ext.reg('minishop2-panel-product-create', miniShop2.panel.CreateProduct);