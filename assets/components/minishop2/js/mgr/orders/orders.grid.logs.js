miniShop2.grid.Logs = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-order-logs';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/orders/getlog',
            order_id: config.order_id,
            type: 'status'
        },
        cls: 'minishop2-grid',
        multi_select: false,
        stateful: true,
        stateId: config.id,
        pageSize: Math.round(MODx.config['default_per_page'] / 2),
    });
    miniShop2.grid.Logs.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Logs, miniShop2.grid.Default, {

    getFields: function () {
        return ['id', 'user_id', 'username', 'fullname', 'timestamp', 'action', 'entry'];
    },

    getColumns: function () {
        return [
            {header: _('ms2_id'), dataIndex: 'id', hidden: true, sortable: true, width: 50},
            {header: _('ms2_username'), dataIndex: 'username', width: 75, renderer: function(val, cell, row) {
                return miniShop2.utils.userLink(val, row.data['user_id'], true);
            }},
            {header: _('ms2_fullname'), dataIndex: 'fullname', width: 100},
            {
                header: _('ms2_timestamp'),
                dataIndex: 'timestamp',
                sortable: true,
                renderer: miniShop2.utils.formatDate,
                width: 75
            },
            {header: _('ms2_action'), dataIndex: 'action', width: 50},
            {header: _('ms2_entry'), dataIndex: 'entry', width: 50}
        ];
    },

    getTopBar: function () {
        return [];
    },

});
Ext.reg('minishop2-grid-order-logs', miniShop2.grid.Logs);
