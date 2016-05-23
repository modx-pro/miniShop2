miniShop2.page.UpdateCategory = function (config) {
    config = config || {record: {}};
    config.record = config.record || {};
    Ext.applyIf(config, {
        panelXType: 'minishop2-panel-category-update',
        mode: 'update',
        actions: {
            new: 'resource/create',
            edit: 'resource/update',
            preview: 'resource/preview',
        }
    });
    miniShop2.page.UpdateCategory.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.page.UpdateCategory, MODx.page.UpdateResource, {

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
Ext.reg('minishop2-page-category-update', miniShop2.page.UpdateCategory);


miniShop2.panel.UpdateCategory = function (config) {
    config = config || {};
    miniShop2.panel.UpdateCategory.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.UpdateCategory, miniShop2.panel.Category, {

    getFields: function (config) {
        var fields = [];
        var originals = miniShop2.panel.Category.prototype.getFields.call(this, config);

        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var item = originals[i];
            if (item.id == 'modx-resource-tabs') {
                var tabs = [
                    this.getProducts(config)
                ];
                for (var i2 in item.items) {
                    if (!item.items.hasOwnProperty(i2)) {
                        continue;
                    }
                    var tab = item.items[i2];
                    tabs.push(tab);
                    // Additional tabs
                    if (tab.id == 'modx-page-settings') {
                        tab.items = this.addOptions(config, tab.items);
                    }
                }
                if (miniShop2.config['show_comments']) {
                    tabs.push(this.getComments(config));
                }
                item.items = tabs;
            }
            fields.push(item);
        }

        return fields;
    },

    getProducts: function (config) {
        return {
            title: _('ms2_tab_products'),
            id: 'modx-minishop2-products',
            layout: 'anchor',
            items: [{
                xtype: 'minishop2-grid-products',
                resource: config.resource,
                border: false,
                listeners: {

                },
            }]
        };
    },

    addOptions: function (config, items) {
        return [{
            layout: 'form',
            items: [items, {
                html: String.format('<h3>{0}</h3>', _('ms2_product_options')),
                style: 'margin-top: 20px',
                border: false,
            }, {
                xtype: 'minishop2-grid-category-option',
                border: false,
                record: config['record'],
            }]
        }];
    },

    getComments: function (config) {
        return {
            title: _('ms2_tab_comments'),
            layout: 'anchor',
            items: [{
                xtype: 'tickets-panel-comments',
                record: config.record,
                section: config.record.id,
                border: false,
            }]
        };
    },

    handlePreview: function (action) {
        var previewBtn = Ext.getCmp('modx-abtn-preview');
        var deleteButton = Ext.getCmp('modx-abtn-delete');
        if (previewBtn == undefined || deleteButton == undefined) {
            Ext.defer(function () {
                this.handlePreview(action);
            }, 200, this);
        } else {
            previewBtn[action]();
            deleteButton[action]();
        }
    },

});
Ext.reg('minishop2-panel-category-update', miniShop2.panel.UpdateCategory);