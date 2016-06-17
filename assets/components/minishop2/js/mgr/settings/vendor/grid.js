miniShop2.grid.Vendor = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-vendor';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/vendor/getlist'
        },
        stateful: true,
        stateId: config.id,
        multi_select: true,
    });
    miniShop2.grid.Vendor.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Vendor, miniShop2.grid.Default, {
    getFields: function () {
        return [
            'id', 'name', 'resource', 'country', 'email', 'logo', 'pagetitle',
            'address', 'phone', 'fax', 'description', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('ms2_id'), dataIndex: 'id', width: 30, sortable: true},
            {header: _('ms2_logo'), dataIndex: 'logo', id: 'image', width: 50, renderer: miniShop2.utils.renderImage},
            {header: _('ms2_name'), dataIndex: 'name', width: 100, sortable: true},
            {
                header: _('ms2_resource'),
                dataIndex: 'resource',
                width: 100,
                sortable: true,
                hidden: true,
                renderer: this._renderResource
            },
            {header: _('ms2_country'), dataIndex: 'country', width: 75, sortable: true},
            {header: _('ms2_email'), dataIndex: 'email', width: 100, sortable: true},
            {header: _('ms2_address'), dataIndex: 'address', width: 100, sortable: true, hidden: true},
            {header: _('ms2_phone'), dataIndex: 'phone', width: 75, sortable: true},
            {header: _('ms2_fax'), dataIndex: 'fax', width: 75, sortable: true, hidden: true},
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
            handler: this.createVendor,
            scope: this
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateVendor(grid, e, row);
            },
        };
    },

    vendorAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/settings/vendor/multiple',
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

    createVendor: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-vendor-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-vendor-create',
            id: 'minishop2-window-vendor-create',
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

    updateVendor: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }

        var w = Ext.getCmp('minishop2-window-vendor-update');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-vendor-update',
            id: 'minishop2-window-vendor-update',
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

    enableVendor: function () {
        this.vendorAction('enable');
    },

    disableVendor: function () {
        this.vendorAction('disable');
    },

    removeVendor: function () {
        var ids = this._getSelectedIds();

        Ext.MessageBox.confirm(
            _('ms2_menu_remove_title'),
            ids.length > 1
                ? _('ms2_menu_remove_multiple_confirm')
                : _('ms2_menu_remove_confirm'),
            function (val) {
                if (val == 'yes') {
                    this.vendorAction('remove');
                }
            }, this
        );
    },

    _renderResource: function (value, cell, row) {
        return value
            ? String.format('({0}) {1}', value, row.data['pagetitle'])
            : '';
    }
});
Ext.reg('minishop2-grid-vendor', miniShop2.grid.Vendor);