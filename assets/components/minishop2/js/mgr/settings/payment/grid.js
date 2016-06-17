miniShop2.grid.Payment = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-payment';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/payment/getlist',
            sort: 'rank',
            dir: 'asc',
        },
        stateful: true,
        stateId: config.id,
        ddGroup: 'ms2-settings-payment',
        ddAction: 'mgr/settings/payment/sort',
        enableDragDrop: true,
        multi_select: true,
    });
    miniShop2.grid.Payment.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Payment, miniShop2.grid.Default, {

    getFields: function () {
        return ['id', 'name', 'description', 'price', 'logo', 'rank', 'active', 'class', 'deliveries', 'actions'];
    },

    getColumns: function () {
        return [
            {header: _('ms2_id'), dataIndex: 'id', width: 20},
            {header: _('ms2_logo'), dataIndex: 'logo', id: 'image', width: 30, renderer: miniShop2.utils.renderImage},
            {header: _('ms2_name'), dataIndex: 'name', width: 75},
            {header: _('ms2_add_cost'), dataIndex: 'price', width: 50},
            {header: _('ms2_deliveries'), dataIndex: 'deliveries', width: 50},
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
            handler: this.createPayment,
            scope: this
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updatePayment(grid, e, row);
            },
        };
    },

    paymentAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/settings/payment/multiple',
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

    createPayment: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-payment-create');
        if (w) {
            w.hide().getEl().remove();
        }

        w = MODx.load({
            xtype: 'minishop2-window-payment-create',
            id: 'minishop2-window-payment-create',
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
            active: true,
        });
        w.show(e.target);
    },

    updatePayment: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }

        var w = Ext.getCmp('minishop2-window-payment-update');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-payment-update',
            id: 'minishop2-window-payment-update',
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

    enablePayment: function () {
        this.paymentAction('enable');
    },

    disablePayment: function () {
        this.paymentAction('disable');
    },

    removePayment: function () {
        var ids = this._getSelectedIds();

        Ext.MessageBox.confirm(
            _('ms2_menu_remove_title'),
            ids.length > 1
                ? _('ms2_menu_remove_multiple_confirm')
                : _('ms2_menu_remove_confirm'),
            function (val) {
                if (val == 'yes') {
                    this.paymentAction('remove');
                }
            }, this
        );
    },
});
Ext.reg('minishop2-grid-payment', miniShop2.grid.Payment);