miniShop2.grid.Products = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-products';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/product/getlist',
            parent: config.resource,
            sort: 'menuindex',
            dir: 'asc',
        },
        multi_select: true,
        stateful: true,
        stateId: config.id,
        save_action: 'mgr/product/updatefromgrid',
        autosave: true,
        save_callback: this.updateRow,
        ddGroup: 'ms2-products',
        ddAction: 'mgr/product/sort',
        enableDragDrop: true,
    });
    miniShop2.grid.Products.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Products, miniShop2.grid.Default, {

    getFields: function () {
        return miniShop2.config['product_fields'];
    },

    getColumns: function () {
        var columns = {
            id: {sortable: true, width: 40},
            pagetitle: {width: 100, sortable: true, id: 'product-title', renderer: this._renderPagetitle},
            longtitle: {width: 50, sortable: true, editor: {xtype: 'textfield'}},
            description: {width: 100, sortable: false, editor: {xtype: 'textarea'}},
            alias: {width: 50, sortable: true, editor: {xtype: 'textfield'}},
            introtext: {width: 100, sortable: false, editor: {xtype: 'textarea'}},
            content: {width: 100, sortable: false, editor: {xtype: 'textarea'}},
            template: {width: 100, sortable: true, editor: {xtype: 'modx-combo-template'}},
            createdby: {width: 100, sortable: true, editor: {xtype: 'minishop2-combo-user', name: 'createdby'}},
            createdon: {
                width: 50,
                sortable: true,
                editor: {xtype: 'minishop2-xdatetime', timePosition: 'below'},
                renderer: miniShop2.utils.formatDate
            },
            editedby: {width: 100, sortable: true, editor: {xtype: 'minishop2-combo-user', name: 'editedby'}},
            editedon: {
                width: 50,
                sortable: true,
                editor: {xtype: 'minishop2-xdatetime', timePosition: 'below'},
                renderer: miniShop2.utils.formatDate
            },
            deleted: {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            deletedon: {
                width: 50,
                sortable: true,
                editor: {xtype: 'minishop2-xdatetime', timePosition: 'below'},
                renderer: miniShop2.utils.formatDate
            },
            deletedby: {width: 100, sortable: true, editor: {xtype: 'minishop2-combo-user', name: 'deletedby'}},
            published: {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            publishedon: {
                width: 50,
                sortable: true,
                editor: {xtype: 'minishop2-xdatetime', timePosition: 'below'},
                renderer: miniShop2.utils.formatDate
            },
            publishedby: {width: 100, sortable: true, editor: {xtype: 'minishop2-combo-user', name: 'publishedby'}},
            menutitle: {width: 100, sortable: true, editor: {xtype: 'textfield'}},
            menuindex: {width: 35, sortable: true, header: 'IDx', editor: {xtype: 'numberfield'}},
            uri: {width: 50, sortable: true, editor: {xtype: 'textfield'}},
            uri_override: {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            show_in_tree: {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            hidemenu: {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            richtext: {width: 100, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            searchable: {width: 100, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            cacheable: {width: 100, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},

            'new': {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            favorite: {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            popular: {width: 50, sortable: true, editor: {xtype: 'combo-boolean', renderer: 'boolean'}},
            article: {width: 50, sortable: true, editor: {xtype: 'textfield'}},
            price: {width: 50, sortable: true, editor: {xtype: 'numberfield', decimalPrecision: 2}},
            old_price: {width: 50, sortable: true, editor: {xtype: 'numberfield', decimalPrecision: 2}},
            weight: {width: 50, sortable: true, editor: {xtype: 'numberfield', decimalPrecision: 3}},
            image: {width: 50, sortable: false, renderer: miniShop2.utils.renderImage, id: 'product-image'},
            thumb: {width: 50, sortable: false, renderer: miniShop2.utils.renderImage, id: 'product-thumb'},
            vendor: {
                width: 50,
                sortable: true,
                renderer: this._renderVendor,
                editor: {xtype: 'minishop2-combo-vendor'},
            },
            vendor_name: {width: 50, sortable: true, header: _('ms2_product_vendor')},
            made_in: {width: 50, sortable: true, editor: {xtype: 'minishop2-combo-autocomplete', name: 'made_in'}},
            //color: {width:50, sortable:false, editor: {xtype: 'minishop2-combo-options', name: 'color'}},
            //size: {width:50, sortable:false, editor: {xtype: 'minishop2-combo-options', name: 'size'}},
            //tags: {width:50, sortable:false, editor: {xtype: 'minishop2-combo-options', name: 'tags'}},
            actions: {
                header: _('ms2_actions'),
                id: 'actions',
                width: 75,
                sortable: false,
                renderer: miniShop2.utils.renderActions
            },
        };

        var i;
        for (i in miniShop2.plugin) {
            if (!miniShop2.plugin.hasOwnProperty(i)) {
                continue;
            }
            if (typeof(miniShop2.plugin[i]['getColumns']) == 'function') {
                var add = miniShop2.plugin[i].getColumns();
                Ext.apply(columns, add);
            }
        }

        var fields = [];
        for (i in miniShop2.config['grid_fields']) {
            if (!miniShop2.config['grid_fields'].hasOwnProperty(i)) {
                continue;
            }
            var field = miniShop2.config['grid_fields'][i];
            if (columns[field]) {
                Ext.applyIf(columns[field], {
                    header: _('ms2_product_' + field),
                    dataIndex: field
                });
                fields.push(columns[field]);
            }
        }

        return fields;
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-tag"></i> ' + _('ms2_product_create'),
            handler: this.createProduct,
            scope: this
        }, '-', {
            text: '<i class="icon icon-folder-open"></i> ' + _('ms2_category_create'),
            handler: this.createCategory,
            scope: this
        }, '-', {
            text: '<i class="icon icon-trash-o action-red"></i>',
            handler: this._emptyRecycleBin,
            scope: this,
        }, '->', {
            xtype: 'xcheckbox',
            name: 'nested',
            width: 200,
            boxLabel: _('ms2_category_show_nested'),
            ctCls: 'tbar-checkbox',
            checked: MODx.config['ms2_category_show_nested_products'] == 1,
            listeners: {
                check: {fn: this.nestedFilter, scope: this}
            }
        }, '-', this.getSearchField()];
    },

    nestedFilter: function (checkbox, checked) {
        var s = this.getStore();
        s.baseParams.nested = checked ? 1 : 0;
        this.getBottomToolbar().changePage(1);
    },

    updateRow: function (res) {
        if (res.results && res.results[0]) {
            var data = res.results[0];
            var items = this.getStore().data.items;
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                if (item.id == data.id) {
                    item.data = data;
                    break;
                }
            }
        }
    },

    productAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/product/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        //noinspection JSUnresolvedFunction
                        this.reloadTree();
                        //noinspection JSUnresolvedFunction
                        this.refresh();
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        })
    },

    createProduct: function () {
        MODx.loadPage('resource/create', 'class_key=msProduct&parent=' + MODx.request.id + '&context_key=' + MODx.ctx);
    },

    createCategory: function () {
        MODx.loadPage('resource/create', 'class_key=msCategory&parent=' + MODx.request.id + '&context_key=' + MODx.ctx);
    },

    viewProduct: function () {
        window.open(this.menu.record['preview_url']);
        return false;
    },

    editProduct: function () {
        MODx.loadPage('resource/update', 'id=' + this.menu.record.id);
    },

    deleteProduct: function () {
        this.productAction('delete');
    },

    undeleteProduct: function () {
        this.productAction('undelete');
    },

    publishProduct: function () {
        this.productAction('publish');
    },

    unpublishProduct: function () {
        this.productAction('unpublish');
    },

    showProduct: function () {
        this.productAction('show');
    },

    hideProduct: function () {
        this.productAction('hide');
    },

    duplicateProduct: function () {
        var r = this.menu.record;
        var w = MODx.load({
            xtype: 'modx-window-resource-duplicate',
            resource: r.id,
            hasChildren: 0,
            listeners: {
                success: {
                    fn: function () {
                        this.reloadTree();
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.config.hasChildren = 0;
        w.setValues(r.data);
        w.show();
    },

    reloadTree: function (ids) {
        if (ids == undefined || typeof(ids) != 'object') {
            ids = this._getSelectedIds();
        }
        var store = this.getStore();
        var parents = {};
        for (var i in ids) {
            if (!ids.hasOwnProperty(i)) {
                continue;
            }
            var item = store.data.map[Number(ids[i])];
            if (item != undefined) {
                parents[item['data']['parent']] = item['data']['context_key'];
            }
        }
        var tree = Ext.getCmp('modx-resource-tree');
        if (tree) {
            for (var parent in parents) {
                if (!parents.hasOwnProperty(parent)) {
                    continue;
                }
                var ctx = parents[parent];
                var node = tree.getNodeById(ctx + '_' + parent);
                if (typeof(node) !== 'undefined') {
                    node.leaf = false;
                    node.reload(function () {
                        this.expand();
                    });
                }
            }
        }
    },

    _renderVendor: function (value, cell, row) {
        return row.data['vendor_name'];
    },

    _renderPagetitle: function (value, cell, row) {
        var link = miniShop2.utils.productLink(value, row['data']['id']);
        if (!row.data['category_name']) {
            return String.format(
                '<div class="native-product"><span class="id">({0})</span>{1}</div>',
                row['data']['id'],
                link
            );
        }
        else {
            var category_link = miniShop2.utils.productLink(row.data['category_name'], row.data['parent']);
            return String.format(
                '<div class="nested-product">\
                    <span class="id">({0})</span>{1}\
                    <div class="product-category">{2}</div>\
                </div>',
                row['data']['id'],
                link,
                category_link
            );
        }
    },

    _emptyRecycleBin: function () {
        MODx.msg.confirm({
            title: _('empty_recycle_bin'),
            text: _('empty_recycle_bin_confirm'),
            url: MODx.config['connector_url'],
            params: {
                action: 'resource/emptyRecycleBin',
            },
            listeners: {
                success: {
                    fn: function () {
                        var tree = Ext.getCmp('modx-resource-tree');
                        if (tree) {
                            Ext.select('div.deleted', tree.getRootNode()).remove();
                        }
                        //noinspection JSUnresolvedFunction
                        this.refresh();
                    },
                    scope: this
                }
            }
        });
    },

});
Ext.reg('minishop2-grid-products', miniShop2.grid.Products);