miniShop2.panel.Product = function (config) {
    config = config || {};
    miniShop2.panel.Product.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.Product, MODx.panel.Resource, {

    active_fields: [],

    getFields: function (config) {
        var fields = [];
        var originals = MODx.panel.Resource.prototype.getFields.call(this, config);

        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var item = originals[i];
            if (item.id == 'modx-resource-header') {
                item.html = '<h2>' + _('ms2_product_new') + '</h2>';
            }
            else if (item.id == 'modx-resource-tabs') {
                item.stateful = MODx.config['ms2_product_remember_tabs'] == 1;
                item.stateId = 'minishop2-product-' + config.mode + '-tabpanel';
                item.stateEvents = ['tabchange'];
                item.collapsible = false;
                item.getState = function () {
                    return {activeTab: this.items.indexOf(this.getActiveTab())};
                };

                var product = [];
                var other = [];

                for (var i2 in item.items) {
                    if (!item.items.hasOwnProperty(i2)) {
                        continue;
                    }
                    var tab = item.items[i2];
                    switch (tab.id) {
                        case 'modx-resource-settings':
                            tab.items.push(this.getContent(config));
                            product.push(tab);
                            break;
                        case 'modx-page-settings':
                            tab.items = this.getProductSettings(config);
                            product.push(tab);
                            if (miniShop2.config['show_extra']) {
                                product.push(this.getProductFields(config));
                            }
                            if (miniShop2.config['show_options']) {
                                var options = this.getProductOptions(config);
                                if (options) {
                                    product.push(options);
                                }
                            }
                            if (config.mode == 'update' && miniShop2.config['show_links']) {
                                product.push(this.getProductLinks(config));
                            }
                            if (miniShop2.config['show_categories']) {
                                product.push(this.getProductCategories(config));
                            }
                            break;
                        default:
                            other.push(tab);
                    }
                }

                var tabs = [{
                    title: _('ms2_tab_product'),
                    cls: 'panel-wrapper',
                    id: 'minishop2-product-tab',
                    items: [{
                        xtype: 'modx-tabs',
                        id: 'minishop2-product-tabs',
                        stateful: MODx.config['ms2_product_remember_tabs'] == 1,
                        stateId: 'minishop2-product-' + config.mode + '-tabpanel-product',
                        stateEvents: ['tabchange'],
                        getState: function () {
                            return {activeTab: this.items.indexOf(this.getActiveTab())};
                        },
                        deferredRender: false,
                        items: product,
                        resource: config.resource,
                        border: false,
                        listeners: {},
                    }]
                }];

                item.items = tabs.concat(other);
            }
            if (item.id != 'modx-resource-content') {
                fields.push(item);
            }
        }

        return fields;
    },

    getMainFields: function (config) {
        var fields = MODx.panel.Resource.prototype.getMainFields.call(this, config);
        var left = [];
        var other = [];

        if (fields[0].id == 'modx-resource-main-columns') {
            if (fields[0].items[0].id == 'modx-resource-main-left') {
                for (var i in fields[0].items[0].items) {
                    if (!fields[0].items[0].items.hasOwnProperty(i)) {
                        continue;
                    }
                    var field = fields[0].items[0].items[i];
                    if (field.id == 'modx-resource-pagetitle' || field.id == 'modx-resource-longtitle') {
                        left.push(field);
                    }
                    else {
                        other.push(field);
                    }
                }
                fields[0].items[0].items = [{
                    layout: 'column',
                    items: [{
                        columnWidth: .7,
                        layout: 'form',
                        items: left
                    }, {
                        columnWidth: .3,
                        layout: 'form',
                        items: [{
                            xtype: 'displayfield',
                            id: 'minishop2-product-image-wrap',
                            html: String.format(
                                '<img src="{0}" id="minishop2-product-image"/>',
                                config.record['thumb'] || miniShop2.config.default_thumb
                            ),
                            listeners: {
                                afterrender: function () {
                                    var img = Ext.get('minishop2-product-image');
                                    if (img) {
                                        var size = MODx.config['ms2_product_thumbnail_size'] || '120x90';
                                        var tmp = size.split('x');
                                        img.set({
                                            width: tmp[0],
                                            height: tmp[1],
                                        });
                                    }
                                }
                            }
                        }]
                    }]
                }, other];
            }
        }

        return fields;
    },

    getProductFields: function (config) {
        var enabled = miniShop2.config.data_fields;
        var available = miniShop2.config.extra_fields;

        var product_fields = this.getAllProductFields(config);
        var col1 = [];
        var col2 = [];
        var tmp;
        for (var i = 0; i < available.length; i++) {
            var field = available[i];
            if ((enabled.length > 0 && enabled.indexOf(field) === -1) || this.active_fields.indexOf(field) !== -1) {
                continue;
            }
            if (tmp = product_fields[field]) {
                this.active_fields.push(field);
                tmp = this.getExtField(config, field, tmp);
                if (i % 2) {
                    col2.push(tmp);
                }
                else {
                    col1.push(tmp);
                }
            }
        }

        return {
            title: _('ms2_tab_product_data'),
            bodyCssClass: 'main-wrapper',
            items: [{
                layout: 'column',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    labelAlign: 'top',
                    items: col1,
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    labelAlign: 'top',
                    items: col2,
                }],
            }],
            listeners: {},
        };
    },

    getProductOptions: function (config) {
        var options = this.getOptionFields(config);
        if (!options.length) {
            return false;
        }

        var option_groups = [];
        for (var i = 0; i < options.length; i++) {
            var newGroup = true;
            for (var j = 0; j < option_groups.length; j++) {
                if (option_groups[j].category == options[i].category) {
                    option_groups[j].items.push(options[i]);
                    newGroup = false;
                    break;
                }
            }
            if (newGroup) {
                option_groups.push({
                    id: 'minishop2-options-tab-' + options[i].category,
                    layout: 'form',
                    labelAlign: 'top',
                    category: options[i].category,
                    title: options[i].category_name
                        ? options[i].category_name
                        : _('ms2_ft_nogroup'),
                    bodyCssClass: 'main-wrapper',
                    items: [options[i]],
                });
            }
        }

        return {
            title: _('ms2_tab_product_options'),
            items: [{
                xtype: 'modx-vtabs',
                autoTabs: true,
                border: false,
                plain: true,
                deferredRender: false,
                id: 'minishop2-options-vtabs',
                items: option_groups,
            }]
        };
    },

    getProductLinks: function (config) {
        return {
            title: _('ms2_tab_product_links'),
            items: [{
                xtype: 'minishop2-product-links',
                record: config.record,
            }]
        };
    },

    getProductCategories: function (config) {
        return {
            title: _('ms2_tab_product_categories'),
            items: [{
                xtype: 'minishop2-tree-categories',
                parent: config.record['parent'] || 0,
                resource: config.record['id'] || 0,
            }]
        };
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
            }
            fields.push(item);
        }

        return fields;
    },

    getProductSettings: function (config) {
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
                    case 'modx-resource-parent':
                        field.xtype = 'minishop2-combo-category';
                        field.listeners = {
                            select: {
                                fn: function (data) {
                                    Ext.getCmp('modx-resource-parent-hidden').setValue(data.value);
                                }
                            }
                        };
                        break;
                    case undefined:
                        if (field.xtype == 'fieldset') {
                            this.findField(field, 'modx-resource-isfolder', function (f) {
                                f.disabled = true;
                                f.hidden = true;
                            });
                            field.items[0].items[0].items = [
                                this.getExtField(config, 'show_in_tree', {xtype: 'xcheckbox'})
                            ].concat(field.items[0].items[0].items);
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

    getExtField: function (config, name, field) {
        var help = '';
        if (_('resource_' + name + '_help')) {
            help = '<br/>' + _('resource_' + name + '_help');
        }
        else if (_('ms2_product_' + name + '_help')) {
            help = '<br/>' + _('ms2_product_' + name + '_help');
        }
        field.value = field.value || config.record[name] || '';
        var properties = {
            description: '<b>[[*' + name + ']]</b>' + help,
            enableKeyEvents: true,
            listeners: config.listeners,
            name: name,
            id: 'modx-resource-' + name,
            msgTarget: 'under',
        };
        switch (field.xtype) {
            case 'minishop2-xdatetime':
            case 'minishop2-combo-user':
                properties.anchor = '95%';
                properties.fieldLabel = _('ms2_product_' + name);
                break;
            case 'xcheckbox':
                properties.boxLabel = _('ms2_product_' + name);
                properties.hideLabel = true;
                break;
            case 'textname':
                properties.maxLength = 255;
                properties.anchor = '100%';
                properties.fieldLabel = _('ms2_product_' + name);
                break;
            default:
                properties.fieldLabel = _('ms2_product_' + name);
                properties.anchor = '100%';
        }
        if (field.allowBlank === false) {
            field.fieldLabel = field.fieldLabel + ' <span class="required red">*</span>'
        }

        return Ext.applyIf(field, properties);
    },

    getAllProductFields: function (config) {
        var fields = {
            pagetitle: {
                xtype: 'textfield',
                fieldLabel: _('ms2_product_pagetitle'),
                maxLength: 255,
                allowBlank: false,
                listeners: {
                    'keyup': {
                        scope: this, fn: function (f) {
                            var title = Ext.util.Format.stripTags(f.getValue());
                            Ext.getCmp('modx-resource-header').getEl().update('<h2>' + title + '</h2>');
                            MODx.fireResourceFormChange();
                        }
                    }
                }
            },
            longtitle: {xtype: 'textfield'},
            description: {xtype: 'textarea'},
            introtext: {xtype: 'textarea', description: '<b>[[*introtext]]</b><br />' + _('resource_summary_help')},
            content: {
                xtype: 'textarea',
                name: 'ta',
                id: 'ta',
                description: '',
                height: 400,
                grow: false,
                value: (config.record.content || config.record.ta) || ''
            },
            createdby: {
                xtype: 'minishop2-combo-user',
                value: config.record.createdby,
                description: '<b>[[*createdby]]</b><br/>' + _('ms2_product_createdby_help')
            },
            publishedby: {
                xtype: 'minishop2-combo-user',
                value: config.record.publishedby,
                description: '<b>[[*publishedby]]</b><br/>' + _('ms2_product_publishedby_help')
            },
            deletedby: {
                xtype: 'minishop2-combo-user',
                value: config.record.deletedby,
                description: '<b>[[*deletedby]]</b><br/>' + _('ms2_product_deletedby_help')
            },
            editedby: {
                xtype: 'minishop2-combo-user',
                value: config.record.deletedby,
                description: '<b>[[*editedby]]</b><br/>' + _('ms2_product_editedby_help')
            },
            publishedon: {
                xtype: 'minishop2-xdatetime',
                value: config.record.publishedon,
                description: '<b>[[*publishedon]]</b><br/>' + _('ms2_product_publishedon_help')
            },
            createdon: {
                xtype: 'minishop2-xdatetime',
                value: config.record.createdon,
                description: '<b>[[*createdon]]</b><br/>' + _('ms2_product_createdon_help')
            },
            deletedon: {
                xtype: 'minishop2-xdatetime',
                value: config.record.deletedon,
                description: '<b>[[*deletedon]]</b><br/>' + _('ms2_product_deletedon_help')
            },
            editedon: {
                xtype: 'minishop2-xdatetime',
                value: config.record.editedon,
                description: '<b>[[*editedon]]</b><br/>' + _('ms2_product_editedon_help')
            },
            pub_date: {
                xtype: MODx.config.publish_document ? 'minishop2-xdatetime' : 'hidden',
                description: '<b>[[*pub_date]]</b><br />' + _('resource_publishdate_help'),
                id: 'modx-resource-pub-date',
                value: config.record.pub_date
            },
            unpub_date: {
                xtype: MODx.config.publish_document ? 'minishop2-xdatetime' : 'hidden',
                description: '<b>[[*unpub_date]]</b><br />' + _('resource_unpublishdate_help'),
                id: 'modx-resource-unpub-date',
                value: config.record.unpub_date
            },

            template: {
                xtype: 'modx-combo-template',
                editable: false,
                baseParams: {action: 'element/template/getlist', combo: '1'},
                listeners: {select: {fn: this.templateWarning, scope: this}}
            },
            parent: {
                xtype: 'minishop2-combo-category',
                value: config.record.parent,
                listeners: {
                    select: {
                        fn: function (data) {
                            Ext.getCmp('modx-resource-parent-hidden').setValue(data.value);
                            MODx.fireResourceFormChange();
                        }
                    }
                }
            },
            alias: {xtype: 'textfield', value: config.record.alias || ''},
            menutitle: {xtype: 'textfield', value: config.record.menutitle || ''},
            menuindex: {xtype: 'numberfield', value: config.record.menuindex || 0, anchor: '50%'},
            link_attributes: {
                xtype: 'textfield',
                value: config.record.link_attributes || '',
                id: 'modx-resource-link-attributes'
            },
            searchable: {xtype: 'xcheckbox', inputValue: 1, checked: parseInt(config.record.searchable)},
            cacheable: {xtype: 'xcheckbox', inputValue: 1, checked: parseInt(config.record.cacheable)},
            richtext: {xtype: 'xcheckbox', inputValue: 1, checked: parseInt(config.record.richtext)},
            hidemenu: {
                xtype: 'xcheckbox',
                inputValue: 1,
                checked: parseInt(config.record.hidemenu),
                description: '<b>[[*hidemenu]]</b><br/>' + _('resource_hide_from_menus_help')
            },
            uri_override: {
                xtype: 'xcheckbox',
                inputValue: 1,
                checked: parseInt(config.record.uri_override),
                id: 'modx-resource-uri-override'
            },
            syncsite: {
                xtype: 'xcheckbox',
                inputValue: 1,
                description: _('resource_syncsite_help'),
                checked: config.record.syncsite !== undefined && config.record.syncsite !== null ? parseInt(config.record.syncsite) : true
            },
            show_in_tree: {
                xtype: 'xcheckbox',
                inputValue: 1,
                description: '<b>[[*show_in_tree]]</b><br/>' + _('ms2_product_show_in_tree_help'),
                checked: parseInt(config.record.show_in_tree)
            },
            article: {xtype: 'textfield', description: '<b>[[+article]]</b><br />' + _('ms2_product_article_help')},
            price: {
                xtype: 'numberfield',
                decimalPrecision: 2,
                description: '<b>[[+price]]</b><br />' + _('ms2_product_price_help')
            },
            old_price: {
                xtype: 'numberfield',
                decimalPrecision: 2,
                description: '<b>[[+old_price]]</b><br />' + _('ms2_product_old_price_help')
            },
            weight: {
                xtype: 'numberfield',
                decimalPrecision: 3,
                description: '<b>[[+weight]]</b><br />' + _('ms2_product_weight_help')
            },
            remains: {xtype: 'numberfield', description: '<b>[[+remains]]</b><br />' + _('ms2_product_remains_help')},
            reserved: {
                xtype: 'numberfield',
                description: '<b>[[+reserved]]</b><br />' + _('ms2_product_reserved_help')
            },
            vendor: {
                xtype: 'minishop2-combo-vendor',
                description: '<b>[[+vendor]]</b><br />' + _('ms2_product_vendor_help')
            },
            made_in: {
                xtype: 'minishop2-combo-autocomplete',
                description: '<b>[[+made_in]]</b><br />' + _('ms2_product_made_in_help')
            },
            source: {
                xtype: config.mode == 'update' ? 'hidden' : 'minishop2-combo-source',
                name: 'source-cmb',
                disabled: config.mode == 'update',
                value: config.record.source || 1,
                description: '<b>[[+source]]</b><br />' + _('ms2_product_source_help'),
                listeners: {
                    select: {
                        fn: function (data) {
                            Ext.getCmp('modx-resource-source-hidden').setValue(data.value);
                            MODx.fireResourceFormChange();
                        }
                    }
                }
            },
            'new': {
                xtype: 'xcheckbox',
                inputValue: 1,
                checked: parseInt(config.record.new),
                description: '<b>[[+new]]</b><br />' + _('ms2_product_new_help')
            },
            favorite: {
                xtype: 'xcheckbox',
                inputValue: 1,
                checked: parseInt(config.record.favorite),
                description: '<b>[[+favorite]]</b><br />' + _('ms2_product_favorite_help')
            },
            popular: {
                xtype: 'xcheckbox',
                inputValue: 1,
                checked: parseInt(config.record.popular),
                description: '<b>[[+popular]]</b><br />' + _('ms2_product_popular_help')
            },
            tags: {
                xtype: 'minishop2-combo-options',
                description: '<b>[[+tags]]</b><br />' + _('ms2_product_tags_help')
            },
            color: {
                xtype: 'minishop2-combo-options',
                description: '<b>[[+color]]</b><br />' + _('ms2_product_color_help')
            },
            size: {xtype: 'minishop2-combo-options', description: '<b>[[+size]]</b><br />' + _('ms2_product_size_help')}
        };

        for (var i in miniShop2.plugin) {
            if (!miniShop2.plugin.hasOwnProperty(i)) {
                continue;
            }
            if (typeof(miniShop2.plugin[i]['getFields']) == 'function') {
                var add = miniShop2.plugin[i].getFields(config);
                Ext.apply(fields, add);
            }
        }

        return fields;
    },

    getOptionFields: function (config) {
        var options = miniShop2.config.option_fields;
        var fields = [];
        for (var i = 0; i < options.length; i++) {
            var field = Ext.applyIf(Ext.util.JSON.decode(options[i].ext_field), {
                fieldLabel: options[i].caption,
                allowBlank: 1 - options[i].required,
                description: '[[+' + options[i].key + ']]',
                value: options[i].value,
                category: options[i].category,
                category_name: options[i].category_name,
            });

            field.name = 'options-' + options[i].key;
            field = this.getExtField(config, options[i].key, field);
            fields.push(field);
        }

        return fields;
    }

});