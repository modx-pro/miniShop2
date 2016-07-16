miniShop2.grid.Status = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-status';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/status/getlist',
            sort: 'rank',
            dir: 'asc',
        },
        stateful: true,
        stateId: config.id,
        ddGroup: 'ms2-settings-status',
        ddAction: 'mgr/settings/status/sort',
        enableDragDrop: true,
        multi_select: true,
    });
    miniShop2.grid.Status.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Status, miniShop2.grid.Default, {

    getFields: function () {
        return [
            'id', 'name', 'description', 'color', 'email_user', 'email_manager',
            'subject_user', 'subject_manager', 'body_user', 'body_manager', 'active',
            'final', 'fixed', 'rank', 'editable', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('ms2_id'), dataIndex: 'id', width: 30},
            {header: _('ms2_name'), dataIndex: 'name', width: 50, renderer: this._renderColor},
            {header: _('ms2_email_user'), dataIndex: 'email_user', width: 50, renderer: this._renderBoolean},
            {header: _('ms2_email_manager'), dataIndex: 'email_manager', width: 50, renderer: this._renderBoolean},
            {header: _('ms2_status_final'), dataIndex: 'final', width: 50, renderer: this._renderBoolean},
            {header: _('ms2_status_fixed'), dataIndex: 'fixed', width: 50, renderer: this._renderBoolean},
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
            handler: this.createStatus,
            scope: this
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateStatus(grid, e, row);
            },
        };
    },

    statusAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/settings/status/multiple',
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

    createStatus: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-status-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-status-create',
            id: 'minishop2-window-status-create',
            record: {
                color: '000000',
                active: 1
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.show(e.target);
    },

    updateStatus: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }

        var w = Ext.getCmp('minishop2-window-status-update');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-status-update',
            id: 'minishop2-window-status-update',
            title: this.menu.record['name'],
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
        w.fp.getForm().setValues(this.menu.record);
        w.show(e.target);
    },

    enableStatus: function () {
        this.statusAction('enable');
    },

    disableStatus: function () {
        this.statusAction('disable');
    },

    removeStatus: function () {
        var ids = this._getSelectedIds();

        Ext.MessageBox.confirm(
            _('ms2_menu_remove_title'),
            ids.length > 1
                ? _('ms2_menu_remove_multiple_confirm')
                : _('ms2_menu_remove_confirm'),
            function (val) {
                if (val == 'yes') {
                    this.statusAction('remove');
                }
            }, this
        );
    },

    _renderColor: function (value, cell, row) {
        //noinspection CssInvalidPropertyValue
        return row.data['active']
            ? String.format('<span style="color:#{0}">{1}</span>', row.data['color'], value)
            : value;
    },

    _renderBoolean: function(value, cell, row) {
        var color, text;

        if (value == 0 || value == false || value == undefined) {
            color = 'red';
            text = _('no');
        }
        else {
            color = 'green';
            text = _('yes');
        }

        return row.data['active']
            ? String.format('<span class="{0}">{1}</span>', color, text)
            : text;
    },
});
Ext.reg('minishop2-grid-status', miniShop2.grid.Status);