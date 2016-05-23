miniShop2.grid.ProductLinks = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'minishop2-grid-product-link',
        baseParams: {
            action: 'mgr/product/productlink/getlist',
            master: config.record.id,
            sort: 'name',
            dir: 'ASC',
        },
        multi_select: true,
    });
    miniShop2.grid.ProductLinks.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.ProductLinks, miniShop2.grid.Default, {

    getFields: function () {
        return [
            'link', 'type', 'name', 'master', 'slave', 'description',
            'master_pagetitle', 'slave_pagetitle', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('ms2_link_name'), dataIndex: 'name', width: 75, sortable: true},
            {header: _('ms2_type'), dataIndex: 'type', width: 75, sortable: true, renderer: this._renderType},
            {
                header: _('ms2_link_master'),
                dataIndex: 'master_pagetitle',
                width: 125,
                sortable: true,
                renderer: this._renderMaster,
                scope: this,
            },
            {
                header: _('ms2_link_slave'),
                dataIndex: 'slave_pagetitle',
                width: 125,
                sortable: true,
                renderer: this._renderSlave,
                scope: this
            },
            {header: '', dataIndex: 'actions', width: 35, id: 'actions', renderer: miniShop2.utils.renderActions}
        ];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i> ' + _('ms2_btn_create'),
            handler: this.createLink,
            scope: this
        }, '->', this.getSearchField()];
    },

    createLink: function (btn, e) {
        var w = Ext.getCmp('minishop2-product-link-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-product-link-create',
            id: 'minishop2-product-link-create',
            baseParams: {
                action: 'mgr/product/productlink/create',
                master: btn.scope.record.id
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

    linkAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/product/productlink/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
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

    _renderType: function (value) {
        return _('ms2_link_' + value);
    },

    _renderMaster: function (value, cell, row) {
        return row.data.master == this.record.id
            ? value
            : miniShop2.utils.productLink(value, row.data.master);
    },

    _renderSlave: function (value, cell, row) {
        return row.data.slave == this.record.id
            ? value
            : miniShop2.utils.productLink(value, row.data.slave);
    },

    _getSelectedIds: function() {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push({
                link: selected[i]['data']['link'],
                master: selected[i]['data']['master'],
                slave: selected[i]['data']['slave'],
            });
        }

        return ids;
    },

});
Ext.reg('minishop2-product-links', miniShop2.grid.ProductLinks);