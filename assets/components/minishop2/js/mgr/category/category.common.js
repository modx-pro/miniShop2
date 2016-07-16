miniShop2.panel.Category = function (config) {
    config = config || {};
    miniShop2.panel.Category.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.Category, MODx.panel.Resource, {

    getFields: function (config) {
        var fields = [];
        var originals = MODx.panel.Resource.prototype.getFields.call(this, config);

        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var item = originals[i];
            if (item.id == 'modx-resource-header') {
                item.html = '<h2>' + _('ms2_category_new') + '</h2>';
            }
            else if (item.id == 'modx-resource-tabs') {
                item.stateful = MODx.config['ms2_category_remember_tabs'] == 1;
                item.stateId = 'minishop2-category-' + config.mode + '-tabpanel';
                item.stateEvents = ['tabchange'];
                item.collapsible = false;
                item.getState = function () {
                    return {activeTab: this.items.indexOf(this.getActiveTab())};
                };
                for (var i2 in item.items) {
                    if (!item.items.hasOwnProperty(i2)) {
                        continue;
                    }
                    var tab = item.items[i2];
                    if (tab.id == 'modx-resource-settings') {
                        tab.title = _('ms2_tab_category');
                        tab.items.push(this.getContent(config));
                    }
                    else if (tab.id == 'modx-page-settings') {
                        tab.items = this.getCategorySettings(config);
                    }
                }
            }
            if (item.id != 'modx-resource-content') {
                fields.push(item);
            }
        }

        return fields;
    },

    getContent: function (config) {
        var fields = [];
        var originals = MODx.panel.Resource.prototype.getContentField.call(this, config);
        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var item = originals[i];

            if (item.id == 'ta') {
                item.hideLabel = false;
                item.fieldLabel = _('content');
                item.description = '<b>[[*content]]</b>';
                if (MODx.config['ms2_category_content_default'] && config['mode'] == 'create') {
                    item.value = MODx.config['ms2_category_content_default'];
                }
            }
            fields.push(item);
        }

        return fields;
    },

    getCategorySettings: function (config) {
        var originals = MODx.panel.Resource.prototype.getSettingFields.call(this, config);

        var moved = {};
        var items = [];
        for (var i in originals[0]['items']) {
            if (!originals[0]['items'].hasOwnProperty(i)) {
                continue;
            }
            var column = originals[0]['items'][i];
            var fields = [];
            for (var i2 in column['items']) {
                if (!column['items'].hasOwnProperty(i2)) {
                    continue;
                }
                var field = column['items'][i2];
                switch (field.id) {
                    case 'modx-resource-content-type':
                        field.xtype = 'hidden';
                        field.value = MODx.config['default_content_type'] || 1;
                        break;
                    case 'modx-resource-content-dispo':
                        field.xtype = 'hidden';
                        field.value = config.record['content_dispo'] || 0;
                        break;
                    case 'modx-resource-menuindex':
                        moved.menuindex = field;
                        continue;
                    case undefined:
                        if (field.xtype == 'fieldset') {
                            this.findField(field, 'modx-resource-isfolder', function (f) {
                                f.disabled = true;
                                f.hidden = true;
                            });
                            field.items[0].items[0].items = [{
                                id: 'modx-resource-hide_children_in_tree',
                                xtype: 'xcheckbox',
                                name: 'hide_children_in_tree',
                                listeners: config.listeners,
                                enableKeyEvents: true,
                                msgTarget: 'under',
                                hideLabel: true,
                                boxLabel: _('ms2_product_hide_children_in_tree'),
                                description: '<b>[[*hide_children_in_tree]]</b><br />' + _('ms2_product_hide_children_in_tree_help'),
                            }].concat(field.items[0].items[0].items);
                            moved.checkboxes = field;
                            continue;
                        }
                        else {
                            break;
                        }
                }
                fields.push(field);
            }
            column.items = fields;
            items.push(column);
        }
        if (moved.checkboxes != undefined) {
            items[0]['items'].push(moved.checkboxes);
        }
        if (moved.menuindex != undefined) {
            items[1]['items'].push(moved.menuindex);
        }
        originals[0]['items'] = items;

        return originals[0];
    },

    findField: function (data, id, callback) {
        for (var i in data) {
            if (!data.hasOwnProperty(i)) {
                continue;
            }
            var item = data[i];
            if (typeof(item) == 'object') {
                if (item.id == id) {
                    return callback(item);
                }
                else {
                    this.findField(item, id, callback);
                }
            }
        }

        return false;
    },

});
Ext.reg('minishop2-panel-category', miniShop2.panel.Category);