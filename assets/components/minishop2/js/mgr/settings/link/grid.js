miniShop2.grid.Link = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-link';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/link/getlist'
        },
        stateful: true,
        stateId: config.id,
        multi_select: true,
    });
    miniShop2.grid.Link.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Link, miniShop2.grid.Default, {

    getFields: function () {
        return ['id', 'type', 'name', 'description', 'actions'];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i> ' + _('ms2_btn_create'),
            handler: this.createLink,
            scope: this
        }, '->', this.getSearchField()];
    },

    getColumns: function () {
        return [
            {header: _('ms2_id'), dataIndex: 'id', width: 50, sortable: true},
            {header: _('ms2_name'), dataIndex: 'name', width: 100, sortable: true},
            {
                header: _('ms2_type'),
                dataIndex: 'type',
                width: 100,
                renderer: function (value) {
                    return _('ms2_link_' + value);
                }
            },
            {header: _('ms2_description'), dataIndex: 'description', width: 100},
            {
                header: _('ms2_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 50,
                renderer: miniShop2.utils.renderActions
            }
        ];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateLink(grid, e, row);
            },
        };
    },

    linkAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/settings/link/multiple',
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

    createLink: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-link-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-link-create',
            id: 'minishop2-window-link-create',
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

    updateLink: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }

        var w = Ext.getCmp('minishop2-window-link-update');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-link-update',
            id: 'minishop2-window-link-update',
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

    removeLink: function () {
        var ids = this._getSelectedIds();

        Ext.MessageBox.confirm(
            _('ms2_menu_remove_title'),
            ids.length > 1
                ? _('ms2_menu_remove_multiple_confirm')
                : _('ms2_menu_remove_confirm'),
            function (val) {
                if (val == 'yes') {
                    this.linkAction('remove');
                }
            }, this
        );
    },

});
Ext.reg('minishop2-grid-link', miniShop2.grid.Link);