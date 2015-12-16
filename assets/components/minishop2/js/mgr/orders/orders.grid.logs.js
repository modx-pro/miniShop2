miniShop2.grid.Logs = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/orders/getlog',
            order_id: config.order_id,
            type: 'status'
        },
        fields: ['id', 'user_id', 'username', 'fullname', 'timestamp', 'action', 'entry'],
        pageSize: Math.round(MODx.config.default_per_page / 2),
        autoHeight: true,
        paging: true,
        remoteSort: true,
        columns: [
            {header: _('ms2_id'), dataIndex: 'id', hidden: true, sortable: true, width: 50},
            {header: _('ms2_username'), dataIndex: 'username', width: 75, renderer: miniShop2.utils.userLink},
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
        ]
    });
    miniShop2.grid.Logs.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Logs, MODx.grid.Grid);
Ext.reg('minishop2-grid-order-logs', miniShop2.grid.Logs);
