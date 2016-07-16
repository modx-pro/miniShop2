miniShop2.grid.Delivery = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-delivery';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/delivery/getlist',
            sort: 'rank',
            dir: 'asc',
        },
        stateful: true,
        stateId: config.id,
        ddGroup: 'ms2-settings-delivery',
        ddAction: 'mgr/settings/delivery/sort',
        enableDragDrop: true,
        multi_select: true,
    });
    miniShop2.grid.Delivery.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Delivery, miniShop2.grid.Default, {

    getFields: function () {
        return [
            'id', 'name', 'price', 'weight_price', 'distance_price', 'rank', 'payments',
            'logo', 'active', 'class', 'description', 'requires', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('ms2_id'), dataIndex: 'id', width: 20},
            {header: _('ms2_logo'), dataIndex: 'logo', id: 'image', width: 30, renderer: miniShop2.utils.renderImage},
            {header: _('ms2_name'), dataIndex: 'name', width: 75},
            {header: _('ms2_add_cost'), dataIndex: 'price', width: 50},
            {header: _('ms2_weight_price'), dataIndex: 'weight_price', width: 50, hidden: true},
            {header: _('ms2_distance_price'), dataIndex: 'distance_price', width: 50, hidden: true},
            {header: _('ms2_payments'), dataIndex: 'payments', width: 50},
            {header: _('ms2_class'), dataIndex: 'class', width: 50},
            {header: _('ms2_rank'), dataIndex: 'rank', width: 35, hidden: true},
            {
                header: _('ms2_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 50,
                renderer: miniShop2.utils.renderActions
            }
        ];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i> ' + _('ms2_btn_create'),
            handler: this.createDelivery,
            scope: this
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateDelivery(grid, e, row);
            },
        };
    },

    deliveryAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/settings/delivery/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
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

    createDelivery: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-delivery-create');
        if (w) {
            w.hide().getEl().remove();
        }

        w = MODx.load({
            xtype: 'minishop2-window-delivery-create',
            id: 'minishop2-window-delivery-create',
            record: this.menu.record,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues({
            price: 0,
            weight_price: 0,
            distance_price: 0,
            active: true,
        });
        w.show(e.target);
    },

    updateDelivery: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }

        var w = Ext.getCmp('minishop2-window-delivery-update');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-delivery-update',
            id: 'minishop2-window-delivery-update',
            record: this.menu.record,
            title: this.menu.record['name'],
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(this.menu.record);
        w.show(e.target);
    },

    enableDelivery: function () {
        this.deliveryAction('enable');
    },

    disableDelivery: function () {
        this.deliveryAction('disable');
    },

    removeDelivery: function () {
        var ids = this._getSelectedIds();

        Ext.MessageBox.confirm(
            _('ms2_menu_remove_title'),
            ids.length > 1
                ? _('ms2_menu_remove_multiple_confirm')
                : _('ms2_menu_remove_confirm'),
            function (val) {
                if (val == 'yes') {
                    this.deliveryAction('remove');
                }
            }, this
        );
    },
});
Ext.reg('minishop2-grid-delivery', miniShop2.grid.Delivery);