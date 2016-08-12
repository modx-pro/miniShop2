miniShop2.page.UpdateProduct = function (config) {
    config = config || {record: {}};
    config.record = config.record || {};

    Ext.applyIf(config, {
        panelXType: 'minishop2-panel-product-update',
        mode: 'update'
    });
    miniShop2.page.UpdateProduct.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.page.UpdateProduct, MODx.page.UpdateResource, {

    getButtons: function (config) {
        var buttons = [];
        var originals = MODx.page.UpdateResource.prototype.getButtons.call(this, config);
        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var button = originals[i];
            switch (button.id) {
                case 'modx-abtn-save':
                    button.text = '<i class="icon icon-save"></i> ' + button.text;
                    break;
                case 'modx-abtn-delete':
                    button.text = '<i class="icon icon-times"></i> ' + button.text;
                    break;
                case 'modx-abtn-preview':
                    button.text = '<i class="icon icon-eye"></i>';
                    break;
                case 'modx-abtn-duplicate':
                    button.text = '<i class="icon icon-files-o"></i> ' + button.text;
                    break;
                case 'modx-abtn-cancel':
                    continue;
                case 'modx-abtn-help':
                    buttons.push(this.getAdditionalButtons(config));
                    button.text = '<i class="icon icon-question-circle"></i>';
                    break;
            }
            buttons.push(button)
        }

        return buttons;
    },

    getAdditionalButtons: function (config) {
        return [{
            text: '<i class="icon icon-arrow-left"></i>',
            handler: this.prevPage,
            disabled: !config['prev_page'],
            scope: this,
            tooltip: _('ms2_btn_prev'),
            keys: [{key: 37, alt: true, scope: this, fn: this.prevPage}]
        }, {
            text: '<i class="icon icon-arrow-up"></i>',
            handler: this.cancel,
            scope: this,
            tooltip: _('ms2_btn_back'),
            keys: [{key: 38, alt: true, scope: this, fn: this.upPage}]
        }, {
            text: '<i class="icon icon-arrow-right"></i>',
            handler: this.nextPage,
            disabled: !config['next_page'],
            scope: this,
            tooltip: _('ms2_btn_next'),
            keys: [{key: 39, alt: true, scope: this, fn: this.nextPage}]
        }];
    },

    prevPage: function () {
        if (this.config['prev_page'] > 0) {
            MODx.loadPage('resource/update', 'id=' + this.config['prev_page'])
        }
    },

    nextPage: function () {
        if (this.config['next_page'] > 0) {
            MODx.loadPage('resource/update', 'id=' + this.config['next_page'])
        }
    },

    cancel: function () {
        var id = this.config['up_page'];
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
Ext.reg('minishop2-page-product-update', miniShop2.page.UpdateProduct);


miniShop2.panel.UpdateProduct = function (config) {
    config = config || {};
    miniShop2.panel.UpdateProduct.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.UpdateProduct, miniShop2.panel.Product, {

    getFields: function (config) {
        var fields = [];
        var originals = miniShop2.panel.Product.prototype.getFields.call(this, config);

        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var item = originals[i];
            if (item.id == 'modx-resource-tabs') {
                // Additional tabs
                if (miniShop2.config['show_comments'] != 0) {
                    item.items.push(this.getComments(config));
                }
                if (miniShop2.config['show_gallery'] != 0) {
                    item.items.push(this.getGallery(config));
                }
            }
            fields.push(item);
        }

        return fields;
    },

    getComments: function (config) {
        return {
            title: _('ms2_tab_comments'),
            layout: 'anchor',
            items: [{
                xtype: 'tickets-panel-comments',
                record: config.record,
                parents: config.record.id,
                border: false,
            }]
        };
    },

    getGallery: function (config) {
        return {
            title: _('ms2_tab_product_gallery'),
            layout: 'anchor',
            items: [{
                xtype: 'minishop2-gallery-page',
                record: config.record,
                pageSize: 50,
                border: false,
            }]
        };
    },

});
Ext.reg('minishop2-panel-product-update', miniShop2.panel.UpdateProduct);