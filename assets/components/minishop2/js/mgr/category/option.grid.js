miniShop2.grid.CategoryOption = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-category-option';
    }

    Ext.applyIf(config, {
        cls: 'minishop2-grid' || config['cls'],
        baseParams: {
            action: 'mgr/category/option/getlist',
            category: config.record['id'],
            sort: 'rank',
            dir: 'asc',
        },
        multi_select: true,
        stateful: true,
        stateId: config.id,
        autosave: true,
        save_action: 'mgr/category/option/updatefromgrid',
        plugins: this.getPlugins(config),
        ddGroup: 'dd-option-grid',
        enableDragDrop: true,
    });
    miniShop2.grid.CategoryOption.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.CategoryOption, miniShop2.grid.Default, {

    getFields: function () {
        return [
            'id', 'key', 'caption', 'type', 'active', 'required', 'rank', 'value',
            'category_id', 'option_id', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('id'), dataIndex: 'id', width: 35, sortable: true},
            {header: _('ms2_ft_name'), dataIndex: 'key', width: 50, sortable: true},
            {header: _('ms2_ft_caption'), dataIndex: 'caption', width: 75, sortable: true},
            {header: _('ms2_ft_type'), dataIndex: 'type', width: 75, renderer: this._renderType},
            {header: _('ms2_default_value'), dataIndex: 'value', width: 75, editor: {xtype: 'textfield'}},
            {header: _('ms2_ft_rank'), dataIndex: 'rank', width: 50, editor: {xtype: 'numberfield'}, hidden: true},
            {
                header: _('ms2_actions'),
                width: 75,
                id: 'actions',
                renderer: miniShop2.utils.renderActions,
                sortable: false
            }
        ];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i> ' + _('ms2_btn_addoption'),
            handler: this.addOption,
            scope: this
        }, {
            text: '<i class="icon icon-files-o"></i> ' + _('ms2_btn_copy'),
            handler: this.copyCategory,
            scope: this
        }, '->', this.getSearchField()];
    },

    getPlugins: function () {
        return [new Ext.ux.dd.GridDragDropRowOrder({
            copy: false,
            scrollable: true,
            targetCfg: {},
            listeners: {
                afterrowmove: {
                    fn: this.onAfterRowMove,
                    scope: this
                }
            }
        })];
    },

    addOption: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-option-add');
        if (w) {
            return false;
        }
        w = MODx.load({
            id: 'minishop2-window-option-add',
            xtype: 'minishop2-window-option-add',
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });

        var f = w.fp.getForm();
        f.reset();
        f.setValues({category_id: MODx.request.id});
        w.show(e.target);
    },

    copyCategory: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-copy-category');
        if (w) {
            return false;
        }
        w = MODx.load({
            id: 'minishop2-window-copy-category',
            xtype: 'minishop2-window-copy-category',
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });

        var f = w.fp.getForm();
        f.reset();
        f.setValues({category_to: MODx.request.id});
        w.show(e.target);
    },

    optionAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }

        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/category/option/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        //noinspection JSUnresolvedFunction
                        this.refresh();
                        //noinspection JSUnresolvedFunction
                        this.getSelectionModel().clearSelections(true)
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

    activateOption: function () {
        this.optionAction('activate');
    },

    deactivateOption: function () {
        this.optionAction('deactivate');
    },

    requireOption: function () {
        this.optionAction('require');
    },

    unrequireOption: function () {
        this.optionAction('unrequire');
    },

    deleteOption: function () {
        this.optionAction('delete');
    },

    onAfterRowMove: function () {
        var s = this.getStore();
        var start = this.getBottomToolbar().cursor;
        var size = this.getBottomToolbar().pageSize;
        var total = s.getTotalCount();
        if (size > total) {
            size = total;
        }
        for (var x = 0; x < size; x++) {
            var brec = s.getAt(x);
            brec.set('rank', start + x);
            brec.commit();
            this.saveRecord({record: brec});
        }
        return true;
    },

    _renderType: function (value) {
        return _('ms2_ft_' + value);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();
        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push({
                option_id: selected[i]['data']['option_id'],
                category_id: selected[i]['data']['category_id'],
            });
        }

        return ids;
    },

});
Ext.reg('minishop2-grid-category-option', miniShop2.grid.CategoryOption);