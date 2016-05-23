miniShop2.panel.Images = function (config) {
    config = config || {};

    this.view = MODx.load({
        xtype: 'minishop2-gallery-images-view',
        id: 'minishop2-gallery-images-view',
        cls: 'minishop2-gallery-images',
        containerScroll: true,
        pageSize: parseInt(config.pageSize || MODx.config.default_per_page),
        product_id: config.product_id,
        emptyText: _('ms2_gallery_emptymsg'),
    });

    Ext.applyIf(config, {
        id: 'minishop2-gallery-images',
        cls: 'browser-view',
        border: false,
        items: [this.view],
        tbar: this.getTopBar(config),
        bbar: this.getBottomBar(config),
    });
    miniShop2.panel.Images.superclass.constructor.call(this, config);

    var dv = this.view;
    dv.on('render', function () {
        dv.dragZone = new miniShop2.DragZone(dv);
        dv.dropZone = new miniShop2.DropZone(dv);
    });
};
Ext.extend(miniShop2.panel.Images, MODx.Panel, {

    _doSearch: function (tf) {
        this.view.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.view.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    getTopBar: function () {
        return new Ext.Toolbar({
            items: ['->', {
                xtype: 'minishop2-field-search',
                width: 300,
                listeners: {
                    search: {
                        fn: function (field) {
                            //noinspection JSUnresolvedFunction
                            this._doSearch(field);
                        }, scope: this
                    },
                    clear: {
                        fn: function (field) {
                            field.setValue('');
                            //noinspection JSUnresolvedFunction
                            this._clearSearch();
                        }, scope: this
                    },
                },
            }]
        })
    },

    getBottomBar: function (config) {
        return new Ext.PagingToolbar({
            pageSize: parseInt(config.pageSize || MODx.config.default_per_page),
            store: this.view.store,
            displayInfo: true,
            autoLoad: true,
            items: ['-',
                _('per_page') + ':',
                {
                    xtype: 'textfield',
                    value: parseInt(config.pageSize || MODx.config.default_per_page),
                    width: 50,
                    listeners: {
                        change: {
                            fn: function (tf, nv) {
                                if (Ext.isEmpty(nv)) {
                                    return;
                                }
                                nv = parseInt(nv);
                                //noinspection JSUnresolvedFunction
                                this.getBottomToolbar().pageSize = nv;
                                this.view.getStore().load({params: {start: 0, limit: nv}});
                            }, scope: this
                        },
                        render: {
                            fn: function (cmp) {
                                new Ext.KeyMap(cmp.getEl(), {
                                    key: Ext.EventObject.ENTER,
                                    fn: function () {
                                        this.fireEvent('change', this.getValue());
                                        this.blur();
                                        return true;
                                    },
                                    scope: cmp
                                });
                            }, scope: this
                        }
                    }
                }
            ]
        });
    },

});
Ext.reg('minishop2-gallery-images-panel', miniShop2.panel.Images);


miniShop2.view.Images = function (config) {
    config = config || {};

    this._initTemplates();

    Ext.applyIf(config, {
        url: miniShop2.config.connector_url,
        fields: [
            'id', 'product_id', 'name', 'description', 'url', 'createdon', 'createdby', 'file',
            'thumbnail', 'source', 'source_name', 'type', 'rank', 'active', 'properties', 'class',
            'add', 'alt', 'actions'
        ],
        id: 'minishop2-gallery-images-view',
        baseParams: {
            action: 'mgr/gallery/getlist',
            product_id: config.product_id,
            parent: 0,
            type: 'image',
            limit: config.pageSize || MODx.config.default_per_page
        },
        //loadingText: _('loading'),
        enableDD: true,
        multiSelect: true,
        tpl: this.templates.thumb,
        itemSelector: 'div.modx-browser-thumb-wrap',
        listeners: {},
        prepareData: this.formatData.createDelegate(this)
    });
    miniShop2.view.Images.superclass.constructor.call(this, config);

    this.addEvents('sort', 'select');
    this.on('sort', this.onSort, this);
    this.on('dblclick', this.onDblClick, this);

    var widget = this;
    this.getStore().on('beforeload', function () {
        widget.getEl().mask(_('loading'), 'x-mask-loading');
    });
    this.getStore().on('load', function () {
        widget.getEl().unmask();
    });
};
Ext.extend(miniShop2.view.Images, MODx.DataView, {

    templates: {},
    windows: {},

    onSort: function (o) {
        var el = this.getEl();
        el.mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: miniShop2.config.connector_url,
            params: {
                action: 'mgr/gallery/sort',
                product_id: this.config.product_id,
                source: o.source.id,
                target: o.target.id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        el.unmask();
                        this.store.reload();
                        //noinspection JSUnresolvedFunction
                        this.updateThumb(r.object['thumb']);
                    }, scope: this
                }
            }
        });
    },

    onDblClick: function (e) {
        var node = this.getSelectedNodes()[0];
        if (!node) {
            return;
        }

        this.cm.activeNode = node;
        this.updateFile(node, e);
    },

    updateFile: function (btn, e) {
        var node = this.cm.activeNode;
        var data = this.lookup[node.id];
        if (!data) {
            return;
        }

        var w = MODx.load({
            xtype: 'minishop2-gallery-image',
            record: data,
            listeners: {
                success: {
                    fn: function () {
                        this.store.reload()
                    }, scope: this
                }
            }
        });
        w.setValues(data);
        w.show(e.target);
    },

    showFile: function () {
        var node = this.cm.activeNode;
        var data = this.lookup[node.id];
        if (!data) {
            return;
        }

        window.open(data.url);
    },

    fileAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        this.getEl().mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: miniShop2.config.connector_url,
            params: {
                action: 'mgr/gallery/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        if (method == 'remove') {
                            //noinspection JSUnresolvedFunction
                            this.updateThumb(r.object['thumb']);
                        }
                        this.store.reload();
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

    deleteFiles: function () {
        var ids = this._getSelectedIds();
        var title = ids.length > 1
            ? 'ms2_gallery_file_delete_multiple'
            : 'ms2_gallery_file_delete';
        var message = ids.length > 1
            ? 'ms2_gallery_file_delete_multiple_confirm'
            : 'ms2_gallery_file_delete_confirm';
        Ext.MessageBox.confirm(
            _(title),
            _(message),
            function (val) {
                if (val == 'yes') {
                    this.fileAction('remove');
                }
            },
            this
        );
    },

    generateThumbs: function () {
        this.fileAction('generate');
    },

    updateThumb: function (url) {
        var thumb = Ext.get('minishop2-product-image');
        if (thumb && url) {
            thumb.set({'src': url});
        }
    },

    run: function (p) {
        p = p || {};
        var v = {};
        Ext.apply(v, this.store.baseParams);
        Ext.apply(v, p);
        this.changePage(1);
        this.store.baseParams = v;
        this.store.load();
    },

    formatData: function (data) {
        data.shortName = Ext.util.Format.ellipsis(data.name, 20);
        data.createdon = miniShop2.utils.formatDate(data.createdon);
        data.size = (data.properties['width'] && data.properties['height'])
            ? data.properties['width'] + 'x' + data.properties['height']
            : '';
        if (data.properties['size'] && data.size) {
            data.size += ', ';
        }
        data.size += data.properties['size']
            ? miniShop2.utils.formatSize(data.properties['size'])
            : '';
        this.lookup['ms2-gallery-image-' + data.id] = data;
        return data;
    },

    _initTemplates: function () {
        this.templates.thumb = new Ext.XTemplate(
            '<tpl for=".">\
                <div class="modx-browser-thumb-wrap modx-pb-thumb-wrap minishop2-gallery-thumb-wrap {class}" id="ms2-gallery-image-{id}">\
                    <div class="modx-browser-thumb modx-pb-thumb minishop2-gallery-thumb">\
                        <img src="{thumbnail}" title="{name}" />\
                    </div>\
                    <small>{rank}. {shortName}</small>\
                </div>\
            </tpl>'
        );
        this.templates.thumb.compile();
    },

    _showContextMenu: function (v, i, n, e) {
        e.preventDefault();
        var data = this.lookup[n.id];
        var m = this.cm;
        m.removeAll();

        var menu = miniShop2.utils.getMenu(data.actions, this, this._getSelectedIds());
        for (var item in menu) {
            if (!menu.hasOwnProperty(item)) {
                continue;
            }
            m.add(menu[item]);
        }

        m.show(n, 'tl-c?');
        m.activeNode = n;
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectedRecords();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

});
Ext.reg('minishop2-gallery-images-view', miniShop2.view.Images);