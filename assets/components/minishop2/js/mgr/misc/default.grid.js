miniShop2.grid.Default = function (config) {
    config = config || {};

    if (typeof(config['multi_select']) != 'undefined' && config['multi_select'] == true) {
        config.sm = new Ext.grid.CheckboxSelectionModel();
    }

    Ext.applyIf(config, {
        url: miniShop2.config['connector_url'],
        baseParams: {},
        cls: config['cls'] || 'main-wrapper minishop2-grid',
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
                if (rec.data['required'] != undefined && rec.data['required'] == 1) {
                    cls.push('minishop2-row-required');
                }
                return cls.join(' ');
            }
        },
    });
    miniShop2.grid.Default.superclass.constructor.call(this, config);

    if (config.enableDragDrop && config.ddAction) {
        this.on('render', function (grid) {
            grid._initDD(config);
        });
    }
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
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        } else if (elem.nodeName == 'A' && elem.href.match(/(\?|\&)a=resource/)) {
            if (e.button == 1 || (e.button == 0 && e.ctrlKey == true)) {
                // Bypass
            } else if (elem.target && elem.target == '_blank') {
                // Bypass
            } else {
                e.preventDefault();
                MODx.loadPage('', elem.href);
            }
        }
        return this.processEvent('click', e);
    },

    refresh: function () {
        this.getStore().reload();
        if (this.config['enableDragDrop'] == true) {
            this.getSelectionModel().clearSelections(true);
        }
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

    _initDD: function (config) {
        var grid = this;
        var el = grid.getEl();

        new Ext.dd.DropTarget(el, {
            ddGroup: grid.ddGroup,
            dropMove: 'x-tree-drop-ok-append',
            dropSort: 'x-tree-drop-ok-between',
            notifyDrop: function (dd, e, data) {
                var store = grid.getStore();
                var target = store.getAt(dd.getDragData(e).rowIndex);
                var sources = [];
                if (data.selections.length < 1 || !target || data.selections[0].id == target.id) {
                    return false;
                }
                for (var i in data.selections) {
                    if (!data.selections.hasOwnProperty(i)) {
                        continue;
                    }
                    var row = data.selections[i];
                    sources.push(row.id);
                }

                el.mask(_('loading'), 'x-mask-loading');
                MODx.Ajax.request({
                    url: config.url,
                    params: {
                        action: config.ddAction,
                        sources: Ext.util.JSON.encode(sources),
                        target: target.id,
                    },
                    listeners: {
                        success: {
                            fn: function () {
                                el.unmask();
                                grid.refresh();

                                if (typeof(grid.reloadTree) == 'function') {
                                    sources.push(target.id);
                                    grid.reloadTree(sources);
                                }

                                if (grid.xtype !== 'minishop2-grid-products' && grid.defaultNotify) {
                                    return;
                                }

                                const sourceNodes = data.selections;
                                if (!Ext.isArray(sourceNodes) && !sourceNodes.length) {
                                    return;
                                }

                                let message = '';

                                if (sourceNodes.every((node) => node.data.parent == sourceNodes[0].data.parent)) { // Each product has the same parent
                                    if (sourceNodes[0].data.parent !== target.data.parent) {
                                        if (!target.data.category_name) {
                                            message = (sourceNodes.length > 1) ? _('ms2_drag_move_current_many_success') : _('ms2_drag_move_current_one_success');
                                        } else {
                                            message = (sourceNodes.length > 1) ? String.format(_('ms2_drag_move_many_success'), target.data.category_name) : String.format(_('ms2_drag_move_one_success'), target.data.category_name);
                                        }
                                    }
                                    // else {
                                    //     message = (sourceNodes.length > 1) ? _('ms2_drag_sort_many_success') : _('ms2_drag_sort_once_success');
                                    // }
                                } else {
                                    message = (sourceNodes.length > 1) ? String.format(_('ms2_drag_move_many_success'), target.data.category_name) : String.format(_('ms2_drag_move_one_success'), target.data.category_name);
                                }

                                if (message) {
                                    MODx.msg.status({
                                        title: _('success'),
                                        message,
                                    });
                                }
                            }, scope: grid
                        },
                        failure: {
                            fn: function () {
                                el.unmask();
                            }, scope: grid
                        },
                    }
                });
            },
            notifyOver: function (dd, e, data) {
                let returnCls = this.dropAllowed;

                if (grid.xtype !== 'minishop2-grid-products' && grid.defaultNotify) {
                    return this.dropNotAllowed;
                }

                if (!dd.getDragData(e)) {
                    return this.notifyOut(dd);
                }

                const sourceNodes = data.selections;
                const targetNode = dd.getDragData(e).selections[0];

                if (!Ext.isArray(sourceNodes) && !sourceNodes.length) {
                    return this.notifyOut(dd);
                }

                if (sourceNodes.every((node) => node.data.parent == sourceNodes[0].data.parent)) { // Each product has the same parent
                    if ((sourceNodes[0].data.id == targetNode.data.id)) {
                        this._notifySelf(sourceNodes.length, dd);
                        returnCls = this.dropNotAllowed;
                    } else if (sourceNodes[0].data.parent != targetNode.data.parent) {
                        this._notifyMove(sourceNodes.length, targetNode, dd);
                        returnCls = this.dropMove;
                    } else {
                        this._notifySort(sourceNodes.length, dd);
                        returnCls = this.dropSort;
                    }
                } else {
                    this._notifyMove(sourceNodes.length, targetNode, dd);
                    returnCls = this.dropMove;
                }

                dd.proxy.update(dd.ddel);
                return returnCls;
            },
            notifyOut: function (dd) {
                dd.ddel.innerHTML = _('ms2_drag_self_one');
                return this.dropNotAllowed;
            },
            _notifyMove: function (count, targetNode, dd) {
                if (targetNode.data.category_name == '') {
                    dd.ddel.innerHTML = (count > 1) ? _('ms2_drag_move_current_many') : _('ms2_drag_move_current_one');
                } else {
                    dd.ddel.innerHTML = (count > 1) ? String.format(_('ms2_drag_move_many'), targetNode.data.category_name) : String.format(_('ms2_drag_move_one'), targetNode.data.category_name);
                }
            },
            _notifySort: function (count, dd) {
                dd.ddel.innerHTML = (count > 1) ? _('ms2_drag_sort_many') : _('ms2_drag_sort_one');
            },
            _notifySelf: function (count, dd) {
                dd.ddel.innerHTML = (count > 1) ? _('ms2_drag_self_many') : _('ms2_drag_self_one');
            }
        });
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
