miniShop2.grid.Option = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-option';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/option/getlist',
            sort: 'key',
            dir: 'asc'
        },
        cls: 'minishop2-grid',
        multi_select: true,
    });
    miniShop2.grid.Option.superclass.constructor.call(this, config);

    config.sm.on('selectionchange', function () {
        var ids = this._getSelectedIds();
        var btn = Ext.getCmp(config.id + '-btn-assign');
        if (btn) {
            if (ids.length > 1) {
                btn.enable();
            } else {
                btn.disable();
            }
        }
    }, this);
};

Ext.extend(miniShop2.grid.Option, miniShop2.grid.Default, {

    getFields: function () {
        return [
            'id', 'key', 'caption', 'description', 'measure_unit',
            'category', 'type', 'properties', 'rank', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('id'), dataIndex: 'id', width: 30, sortable: true},
            {
                header: _('ms2_ft_name'),
                dataIndex: 'key',
                width: 100,
                sortable: true
            }, {
                header: _('ms2_ft_caption'),
                dataIndex: 'caption',
                width: 100,
                sortable: true
            }, {
                header: _('ms2_ft_type'),
                dataIndex: 'type',
                width: 100,
                sortable: true,
                renderer: function (v) {
                    return _('ms2_ft_' + v)
                }
            }, {
                header: _('ms2_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 70,
                renderer: miniShop2.utils.renderActions
            }
        ];
    },

    getTopBar: function (config) {
        return [{
            text: '<i class="icon icon-plus"></i> ' + _('ms2_btn_create'),
            handler: this.createOption,
            scope: this
        }, {
            text: '<i class="icon icon-check"></i> ' + _('ms2_btn_assign'),
            id: config.id + '-btn-assign',
            handler: this.assignOption,
            scope: this,
            disabled: true,
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateOption(grid, e, row);
            },
        };
    },

    createOption: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-option-create');
        if (w) {
            w.hide().getEl().remove();
        }

        w = MODx.load({
            xtype: 'minishop2-window-option-create',
            id: 'minishop2-window-option-create',
            record: [],
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().setValues({
            type: 'textfield'
        });
        w.show(e.target);
    },

    updateOption: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }

        var w = Ext.getCmp('minishop2-window-option-update');
        if (w) {
            w.close();
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/settings/option/get',
                id: this.menu.record.id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        w = MODx.load({
                            xtype: 'minishop2-window-option-update',
                            id: 'minishop2-window-option-update',
                            title: r.object['caption'],
                            record: r.object,
                            listeners: {
                                afterrender: function () {
                                    var combo = Ext.getCmp(this.config.id + '-types');
                                    combo.getStore().on('load', function () {
                                        var row = combo.findRecord('name', combo.getValue());
                                        if (row && row.data['xtype']) {
                                            w.onSelectType(combo, row);
                                        }
                                    });
                                },
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
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

    removeOption: function () {
        if (!this.menu.record) {
            return false;
        }

        MODx.msg.confirm({
            title: _('ms2_menu_remove') + '"' + this.menu.record.key + '"',
            text: _('ms2_menu_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/settings/option/multiple',
                method: 'remove',
                ids: Ext.util.JSON.encode(this._getSelectedIds()),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
    },

    assignOption: function (btn, e) {
        var options = Ext.util.JSON.encode(this._getSelectedIds());
        var w = Ext.getCmp('minishop2-window-option-assign');
        if (w) {
            w.close();
        }

        w = MODx.load({
            xtype: 'minishop2-window-option-assign',
            id: 'minishop2-window-option-assign',
            options: options,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().setValues({options: options});
        w.show(e.target);
    },

});
Ext.reg('minishop2-grid-option', miniShop2.grid.Option);