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

        MODx.loadPage(action, 'id=' + id)
    },

});
Ext.reg('minishop2-page-product-create', miniShop2.page.CreateProduct);


miniShop2.panel.CreateProduct = function (config) {
    config = config || {};
    miniShop2.panel.CreateProduct.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.CreateProduct, miniShop2.panel.Product, {

    getFields: function (config) {
        var fields = [];
        var originals = miniShop2.panel.Product.prototype.getFields.call(this, config);

        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var item = originals[i];
            if (item.id == 'modx-resource-tabs') {
                // Additional "Gallery" tab
                if (miniShop2.config['show_gallery'] != 0) {
                    item.items.push(this.getGallery(config));

                    // Get the "Resource Groups" tab and move it to the end
                    var accessPermissionsTab;
                    var index = item.items.findIndex(function (tab) {
                        return tab.id == 'modx-resource-access-permissions';
                    });
                    if (index != -1) {
                        accessPermissionsTab = item.items.splice(index, 1);
                        accessPermissionsTab && item.items.push(accessPermissionsTab);
                    }
                }
            }
            fields.push(item);
        }

        return fields;
    },

    getGallery: function (config) {
        return {
            title: _('ms2_tab_product_gallery'),
            disabled: true,
            listeners: {
                afterrender: function (p) {
                    Ext.get(p.tabEl).on('click', function () {
                        MODx.msg.alert(_('warning'), _('ms2_gallery_unavailablemsg'));
                    });
                }
            }
        };
    },
});
Ext.reg('minishop2-panel-product-create', miniShop2.panel.CreateProduct);
