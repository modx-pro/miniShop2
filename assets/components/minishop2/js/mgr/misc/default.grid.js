miniShop2.grid.Default = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        url: miniShop2.config['connector_url'],
        baseParams: {},
        cls: 'main-wrapper minishop2-grid' || config['cls'],
        autoHeight: true,
        paging: true,
        remoteSort: true,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: -10,
            getRowClass: function (rec) {
                var cls = [];
                if (rec.data['published'] != undefined && rec.data['published'] == 0) {
                    cls.push('minishop2-row-unpublished');
                }
                if (rec.data['active'] != undefined && rec.data['active'] == 0) {
                    cls.push('minishop2-row-inactive');
                }
                if (rec.data['deleted'] != undefined && rec.data['deleted'] == 1) {
                    cls.push('minishop2-row-deleted');
                }
                return cls.join(' ');
            }
        },
    });
    if (typeof(config['multi_select']) != 'undefined' && config['multi_select'] == true) {
        config.sm = new Ext.grid.CheckboxSelectionModel();
    }
    miniShop2.grid.Default.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.Default, MODx.grid.Grid, {

    getFields: function () {
        return [
            'id', 'actions'
        ];
    },

    getColumns: function () {
        return [{
            header: _('id'),
            dataIndex: 'id',
            width: 35,
            sortable: true,
        }, {
            header: _('minishop2_actions'),
            dataIndex: 'actions',
            renderer: miniShop2.utils.renderActions,
            sortable: false,
            width: 75,
            id: 'actions'
        }];
    },

    getTopBar: function () {
        return ['->', this.getSearchField()];
    },

    getSearchField: function (width) {
        return {
            xtype: 'minishop2-field-search',
            width: width || 250,
            listeners: {
                search: {
                    fn: function (field) {
                        this._doSearch(field);
                    }, scope: this
                },
                clear: {
                    fn: function (field) {
                        field.setValue('');
                        this._clearSearch();
                    }, scope: this
                },
            }
        };
    },

    getListeners: function () {
        return {
            /*
             rowDblClick: function(grid, rowIndex, e) {
             var row = grid.store.getAt(rowIndex);
             this.someAction(grid, e, row);
             }
             */
        };
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);

        var menu = miniShop2.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }

        return this.processEvent('click', e);
    },

    _doSearch: function (tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    _loadStore: function () {
        this.store = new Ext.data.JsonStore({
            url: this.config.url,
            baseParams: this.config.baseParams || {action: this.config.action || 'getList'},
            fields: this.config.fields,
            root: 'results',
            totalProperty: 'total',
            remoteSort: this.config.remoteSort || false,
            storeId: this.config.storeId || Ext.id(),
            autoDestroy: true,
            listeners: {
                load: function (store, rows, data) {
                    store.sortInfo = {
                        field: data.params['sort'] || 'id',
                        direction: data.params['dir'] || 'ASC',
                    };
                    Ext.getCmp('modx-content').doLayout();
                }
            }
        });
    },

});
Ext.reg('minishop2-grid-default', miniShop2.grid.Default);