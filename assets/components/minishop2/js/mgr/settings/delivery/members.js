miniShop2.grid.DeliveryPayments = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-delivery-payments';
    }

    Ext.applyIf(config, {
        cls: 'minishop2-grid',
        baseParams: {
            action: 'mgr/settings/delivery/payments/getlist',
            sort: 'rank',
            dir: 'asc',
            delivery: config.record.id,
        },
        pageSize: 5,
        multi_select: true,
    });
    miniShop2.grid.DeliveryPayments.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.DeliveryPayments, miniShop2.grid.Default, {

    getFields: function () {
        return ['id', 'name', 'price', 'logo', 'rank', 'active', 'class', 'actions'];
    },

    getColumns: function () {
        return [
            {header: _('ms2_logo'), dataIndex: 'logo', id: 'image', width: 30, renderer: miniShop2.utils.renderImage},
            {header: _('ms2_name'), dataIndex: 'name', width: 75},
            {header: _('ms2_add_cost'), dataIndex: 'price', width: 50},
            {
                header: _('ms2_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 35,
                renderer: miniShop2.utils.renderActions
            }
        ];
    },

    getTopBar: function () {
        return [];
    },

    getListeners: function () {
        return [];
    },

    paymentsAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/settings/delivery/payments/multiple',
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

    enablePayment: function () {
        this.paymentsAction('enable');
    },

    disablePayment: function () {
        this.paymentsAction('disable');
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push({
                delivery_id: this.config.record.id,
                payment_id: selected[i]['id'],
            });
        }

        return ids;
    },
});
Ext.reg('minishop2-grid-delivery-payments', miniShop2.grid.DeliveryPayments);