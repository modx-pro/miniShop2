miniShop2.grid.OrderProducts = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-order-products';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/orders/product/getlist',
            order_id: config.order_id,
        },
        cls: 'minishop2-grid',
        multi_select: false,
        stateful: true,
        stateId: config.id,
        pageSize: Math.round(MODx.config['default_per_page'] / 2),
    });
    miniShop2.grid.OrderProducts.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.OrderProducts, miniShop2.grid.Default, {

    getFields: function () {
        return miniShop2.config['order_product_fields'];
    },

    getColumns: function () {
        var fields = {
            //id: {hidden: true, sortable: true, width: 40},
            product_id: {hidden: true, sortable: true, width: 40},
            name: {
                header: _('ms2_name'),
                width: 100,
                renderer: function (value, metaData, record) {
                    return miniShop2.utils.productLink(value, record['data']['product_id']);
                }
            },
            product_weight: {header: _('ms2_product_weight'), width: 50},
            product_price: {header: _('ms2_product_price'), width: 50},
            product_article: {width: 50},
            weight: {sortable: true, width: 50},
            price: {sortable: true, header: _('ms2_product_price'), width: 50},
            count: {sortable: true, width: 50},
            cost: {width: 50},
            options: {width: 100},
            actions: {width: 75, id: 'actions', renderer: miniShop2.utils.renderActions, sortable: false},
        };

        var columns = [];
        for (var i = 0; i < miniShop2.config['order_product_fields'].length; i++) {
            var field = miniShop2.config['order_product_fields'][i];
            if (fields[field]) {
                Ext.applyIf(fields[field], {
                    header: _('ms2_' + field),
                    dataIndex: field
                });
                columns.push(fields[field]);
            }
            else if (/^option_/.test(field)) {
                columns.push(
                    {header: _(field.replace(/^option_/, 'ms2_')), dataIndex: field, width: 50}
                );
            }
            else if (/^product_/.test(field)) {
                columns.push(
                    {header: _(field.replace(/^product_/, 'ms2_')), dataIndex: field, width: 75}
                );
            }
            else if (/^category_/.test(field)) {
                columns.push(
                    {header: _(field.replace(/^category_/, 'ms2_')), dataIndex: field, width: 75}
                );
            }
        }

        return columns;
    },

    getTopBar: function () {
        return [{
            xtype: 'minishop2-combo-product',
            allowBlank: true,
            width: '50%',
            listeners: {
                select: {
                    fn: this.addOrderProduct,
                    scope: this
                }
            }
        }];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateOrderProduct(grid, e, row);
            }
        };
    },

    addOrderProduct: function (combo, row) {
        var id = row.id;
        combo.reset();

        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/product/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('minishop2-window-orderproduct-update');
                        if (w) {
                            w.close();
                        }

                        r.object.order_id = this.config.order_id;
                        r.object.count = 1;
                        r.object.name = r.object['pagetitle'];
                        w = MODx.load({
                            xtype: 'minishop2-window-orderproduct-update',
                            id: 'minishop2-window-orderproduct-update',
                            record: r.object,
                            action: 'mgr/orders/product/create',
                            listeners: {
                                success: {
                                    fn: function () {
                                        miniShop2.grid.Orders.changed = true;
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.fp.getForm().reset();
                        w.fp.getForm().setValues(r.object);
                        w.show(Ext.EventObject.target);
                    }, scope: this
                }
            }
        });
    },

    updateOrderProduct: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/orders/product/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('minishop2-window-orderproduct-update');
                        if (w) {
                            w.close();
                        }

                        r.object.order_id = this.config.order_id;
                        w = MODx.load({
                            xtype: 'minishop2-window-orderproduct-update',
                            id: 'minishop2-window-orderproduct-update',
                            record: r.object,
                            action: 'mgr/orders/product/update',
                            listeners: {
                                success: {
                                    fn: function () {
                                        miniShop2.grid.Orders.changed = true;
                                        this.refresh();
                                    }, scope: this
                                },
                            }
                        });
                        w.fp.getForm().reset();
                        w.fp.getForm().setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    removeOrderProduct: function () {
        if (!this.menu.record) {
            return;
        }

        MODx.msg.confirm({
            title: _('ms2_menu_remove'),
            text: _('ms2_menu_remove_confirm'),
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/orders/product/remove',
                id: this.menu.record.id
            },
            listeners: {
                success: {
                    fn: function () {
                        miniShop2.grid.Orders.changed = true;
                        this.refresh();
                    }, scope: this
                }
            }
        });
    }
});
Ext.reg('minishop2-grid-order-products', miniShop2.grid.OrderProducts);
