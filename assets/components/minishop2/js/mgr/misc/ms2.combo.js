miniShop2.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    miniShop2.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(miniShop2.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    },

});
Ext.reg('minishop2-combo-search', miniShop2.combo.Search);
Ext.reg('minishop2-field-search', miniShop2.combo.Search);


miniShop2.combo.User = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'user',
        fieldLabel: config.name || 'createdby',
        hiddenName: config.name || 'createdby',
        displayField: 'fullname',
        valueField: 'id',
        anchor: '99%',
        fields: ['username', 'id', 'fullname'],
        pageSize: 20,
        typeAhead: false,
        editable: true,
        allowBlank: false,
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/system/user/getlist',
            combo: true,
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <small>({id})</small>\
                        <b>{username}</b>\
                        <tpl if="fullname"> - {fullname}</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    miniShop2.combo.User.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.User, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-user', miniShop2.combo.User);


miniShop2.combo.Category = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'minishop2-combo-section',
        fieldLabel: _('ms2_category'),
        description: '<b>[[*parent]]</b><br />' + _('ms2_product_parent_help'),
        fields: ['id', 'pagetitle', 'parents'],
        valueField: 'id',
        displayField: 'pagetitle',
        name: 'parent-cmb',
        hiddenName: 'parent-cmp',
        allowBlank: false,
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/category/getcats',
            combo: true,
            id: config.value
        },
        tpl: new Ext.XTemplate('\
            <tpl for="."><div class="x-combo-list-item minishop2-category-list-item">\
                <tpl if="parents">\
                    <div class="parents">\
                        <tpl for="parents">\
                            <nobr><small>{pagetitle} / </small></nobr>\
                        </tpl>\
                    </div>\
                </tpl>\
                <span>\
                    <small>({id})</small> <b>{pagetitle}</b>\
                </span>\
            </div></tpl>', {
            compiled: true
        }),
        itemSelector: 'div.minishop2-category-list-item',
        pageSize: 20,
        editable: true
    });
    miniShop2.combo.Category.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Category, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-category', miniShop2.combo.Category);


miniShop2.combo.DateTime = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        timePosition: 'right',
        allowBlank: true,
        hiddenFormat: 'Y-m-d H:i:s',
        dateFormat: MODx.config['manager_date_format'],
        timeFormat: MODx.config['manager_time_format'],
        dateWidth: 120,
        timeWidth: 120
    });
    miniShop2.combo.DateTime.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.DateTime, Ext.ux.form.DateTime);
Ext.reg('minishop2-xdatetime', miniShop2.combo.DateTime);


miniShop2.combo.Autocomplete = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: config.name,
        fieldLabel: _('ms2_product_' + config.name),
        id: 'minishop2-product-' + config.name,
        hiddenName: config.name,
        displayField: config.name,
        valueField: config.name,
        anchor: '99%',
        fields: [config.name],
        //pageSize: 20,
        forceSelection: false,
        url: miniShop2.config['connector_url'],
        typeAhead: true,
        editable: true,
        allowBlank: true,
        baseParams: {
            action: 'mgr/product/autocomplete',
            name: config.name,
            combo: true,
            limit: 0
        },
        hideTrigger: false,
    });
    miniShop2.combo.Autocomplete.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Autocomplete, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-autocomplete', miniShop2.combo.Autocomplete);


miniShop2.combo.Vendor = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: config.name || 'vendor',
        fieldLabel: _('ms2_product_' + config.name || 'vendor'),
        hiddenName: config.name || 'vendor',
        displayField: 'name',
        valueField: 'id',
        anchor: '99%',
        fields: ['name', 'id'],
        pageSize: 20,
        url: miniShop2.config['connector_url'],
        typeAhead: true,
        editable: true,
        allowBlank: true,
        emptyText: _('no'),
        baseParams: {
            action: 'mgr/settings/vendor/getlist',
            combo: true,
            id: config.value,
        }
    });
    miniShop2.combo.Vendor.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Vendor, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-vendor', miniShop2.combo.Vendor);


miniShop2.combo.Source = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: config.name || 'source-cmb',
        id: 'minishop2-product-source',
        hiddenName: 'source-cmb',
        displayField: 'name',
        valueField: 'id',
        width: 300,
        listWidth: 300,
        fieldLabel: _('ms2_product_' + config.name || 'source'),
        anchor: '99%',
        allowBlank: false
    });
    miniShop2.combo.Source.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Source, MODx.combo.MediaSource);
Ext.reg('minishop2-combo-source', miniShop2.combo.Source);


miniShop2.combo.Options = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'superboxselect',
        allowBlank: true,
        msgTarget: 'under',
        allowAddNewData: true,
        addNewDataOnBlur: true,
        pinList: false,
        resizable: true,
        name: config.name || 'tags',
        anchor: '100%',
        minChars: 1,
        store: new Ext.data.JsonStore({
            id: (config.name || 'tags') + '-store',
            root: 'results',
            autoLoad: false,
            autoSave: false,
            totalProperty: 'total',
            fields: ['value'],
            url: miniShop2.config['connector_url'],
            baseParams: {
                action: 'mgr/product/getoptions',
                key: config.name
            }
        }),
        mode: 'remote',
        displayField: 'value',
        valueField: 'value',
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls: 'x-form-trigger',
        clearBtnCls: 'x-form-trigger',
    });
    config.name += '[]';

    Ext.apply(config, {
        listeners: {
            newitem: function(bs, v) {
                bs.addNewItem({value: v});
            },
        },
    });

    miniShop2.combo.Options.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Options, Ext.ux.form.SuperBoxSelect);
Ext.reg('minishop2-combo-options', miniShop2.combo.Options);


miniShop2.combo.Chunk = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'chunk',
        hiddenName: config.name || 'chunk',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name'],
        pageSize: 20,
        emptyText: _('ms2_combo_select'),
        hideMode: 'offsets',
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/system/element/chunk/getlist',
            mode: 'chunks'
        }
    });
    miniShop2.combo.Chunk.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Chunk, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-chunk', miniShop2.combo.Chunk);


miniShop2.combo.Resource = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'resource',
        hiddenName: 'resource',
        displayField: 'pagetitle',
        valueField: 'id',
        editable: true,
        fields: ['id', 'pagetitle'],
        pageSize: 20,
        emptyText: _('ms2_combo_select'),
        hideMode: 'offsets',
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/system/element/resource/getlist',
            combo: true
        }
    });
    miniShop2.combo.Resource.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Resource, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-resource', miniShop2.combo.Resource);


miniShop2.combo.Browser = function (config) {
    config = config || {};

    if (config.length != 0 && config.openTo != undefined) {
        if (!/^\//.test(config.openTo)) {
            config.openTo = '/' + config.openTo;
        }
        if (!/$\//.test(config.openTo)) {
            var tmp = config.openTo.split('/');
            delete tmp[tmp.length - 1];
            tmp = tmp.join('/');
            config.openTo = tmp.substr(1)
        }
    }

    Ext.applyIf(config, {
        width: 300,
        triggerAction: 'all'
    });
    miniShop2.combo.Browser.superclass.constructor.call(this, config);
    this.config = config;
};
Ext.extend(miniShop2.combo.Browser, Ext.form.TriggerField, {
    browser: null,

    onTriggerClick: function () {
        if (this.disabled) {
            return false;
        }
        var browser = MODx.load({
            xtype: 'modx-browser',
            id: Ext.id(),
            multiple: true,
            source: this.config.source || MODx.config['default_media_source'],
            rootVisible: this.config.rootVisible || false,
            allowedFileTypes: this.config.allowedFileTypes || '',
            wctx: this.config.wctx || 'web',
            openTo: this.config.openTo || '',
            rootId: this.config.rootId || '/',
            hideSourceCombo: this.config.hideSourceCombo || false,
            hideFiles: this.config.hideFiles || true,
            listeners: {
                select: {
                    fn: function (data) {
                        this.setValue(data.fullRelativeUrl);
                        this.fireEvent('select', data);
                    }, scope: this
                }
            },
        });
        browser.win.buttons[0].on('disable', function () {
            this.enable()
        });
        browser.win.tree.on('click', function (n) {
            this.setValue(this.getPath(n));
        }, this);
        browser.win.tree.on('dblclick', function (n) {
            this.setValue(this.getPath(n));
            browser.hide()
        }, this);
        browser.show();
    },

    getPath: function (n) {
        if (n.id == '/') {
            return '';
        }

        return n.attributes.path + '/';
    }
});
Ext.reg('minishop2-combo-browser', miniShop2.combo.Browser);


miniShop2.combo.listeners_disable = {
    render: function () {
        this.store.on('load', function () {
            if (this.store.getTotalCount() == 1 && this.store.getAt(0).id == this.value) {
                this.readOnly = true;
                this.addClass('disabled');
            }
            else {
                this.readOnly = false;
                this.removeClass('disabled');
            }
        }, this);
    }
};


miniShop2.combo.Status = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'status',
        id: 'minishop2-combo-status',
        hiddenName: 'status',
        displayField: 'name',
        valueField: 'id',
        fields: ['id', 'name'],
        pageSize: 10,
        emptyText: _('ms2_combo_select_status'),
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/settings/status/getlist',
            combo: true,
            addall: config.addall || 0,
            order_id: config.order_id || 0
        },
        listeners: miniShop2.combo.listeners_disable
    });
    miniShop2.combo.Status.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Status, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-status', miniShop2.combo.Status);


miniShop2.combo.Delivery = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'delivery',
        id: 'minishop2-combo-delivery',
        hiddenName: 'delivery',
        displayField: 'name',
        valueField: 'id',
        fields: ['id', 'name'],
        pageSize: 10,
        emptyText: _('ms2_combo_select'),
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/settings/delivery/getlist',
            combo: true
        },
        listeners: {
            render: function () {
                this.store.on('load', function (store) {
                    if (store.getTotalCount() == 1 && store.getAt(0).id == this.getValue()) {
                        this.readOnly = true;
                        this.wrap.addClass('disabled');
                    }
                    else {
                        this.readOnly = false;
                        this.wrap.removeClass('disabled');
                    }
                }, this);
            },
            select: function (combo, row) {
                var payments = Ext.getCmp('minishop2-combo-payment');
                var store = payments.getStore();
                payments.setValue('');
                store.baseParams.delivery_id = row.id;
                store.load();
            }
        }
    });
    miniShop2.combo.Delivery.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Delivery, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-delivery', miniShop2.combo.Delivery);


miniShop2.combo.Payment = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'payment',
        id: 'minishop2-combo-payment',
        hiddenName: 'payment',
        displayField: 'name',
        valueField: 'id',
        fields: ['id', 'name'],
        pageSize: 10,
        emptyText: _('ms2_combo_select'),
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/settings/payment/getlist',
            combo: true,
            delivery_id: config.delivery_id || 0
        },
        listeners: miniShop2.combo.listeners_disable
    });
    miniShop2.combo.Payment.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Payment, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-payment', miniShop2.combo.Payment);


MODx.combo.LinkType = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['type', 'name', 'description'],
            data: this.getTypes()
        }),
        emptyText: _('ms2_combo_select'),
        displayField: 'name',
        valueField: 'type',
        hiddenName: 'type',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        selectOnFocus: false,
        preventRender: true,
        forceSelection: true,
        enableKeyEvents: true
    });
    MODx.combo.LinkType.superclass.constructor.call(this, config);
};
Ext.extend(MODx.combo.LinkType, MODx.combo.ComboBox, {

    getTypes: function () {
        var array = [];
        var types = ['many_to_many', 'one_to_many', 'many_to_one', 'one_to_one'];
        for (var i = 0; i < types.length; i++) {
            var t = types[i];
            array.push([t, _('ms2_link_' + t), _('ms2_link_' + t + '_desc')]);
        }
        return array;
    }
});
Ext.reg('minishop2-combo-link-type', MODx.combo.LinkType);


miniShop2.combo.Link = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'link',
        id: 'minishop2-combo-link',
        hiddenName: 'link',
        displayField: 'name',
        valueField: 'id',
        fields: ['id', 'name'],
        pageSize: 10,
        editable: true,
        emptyText: _('ms2_combo_select'),
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/settings/link/getlist',
            combo: true
        }
    });
    miniShop2.combo.Link.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Link, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-link', miniShop2.combo.Link);


miniShop2.combo.Product = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'minishop2-combo-product',
        fieldLabel: _('ms2_product_name'),
        fields: ['id', 'pagetitle', 'parents'],
        valueField: 'id',
        displayField: 'pagetitle',
        name: 'product',
        hiddenName: 'product',
        allowBlank: false,
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/product/getlist',
            combo: true,
            id: config.value
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item minishop2-product-list-item">\
                    <tpl if="parents">\
                        <span class="parents">\
                            <tpl for="parents">\
                                <nobr><small>{pagetitle} / </small></nobr>\
                            </tpl>\
                        </span><br/>\
                    </tpl>\
                    <span><small>({id})</small> <b>{pagetitle}</b></span>\
                </div>\
            </tpl>', {compiled: true}
        ),
        pageSize: 5,
        emptyText: _('ms2_combo_select'),
        editable: true,
    });
    miniShop2.combo.Product.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Product, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-product', miniShop2.combo.Product);


miniShop2.combo.ExtraOptions = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'minishop2-combo-extra-options',
        fieldLabel: _('ms2_option'),
        name: 'option',
        hiddenName: 'option',
        displayField: 'key',
        valueField: 'id',
        pageSize: 20,
        fields: ['id', 'key', 'caption', 'type'],
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/settings/option/getlist'
        },
        tpl: new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{caption}</span>'
            , ' - <span style="font-style:italic">{key}</span><br />{[this.getLang(values.type)]}</div></tpl>', {
                getLang: function (type) {
                    return _("ms2_ft_" + type);
                }
            }),
        allowBlank: false,
        editable: true,
    });
    miniShop2.combo.ExtraOptions.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.ExtraOptions, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-extra-options', miniShop2.combo.ExtraOptions);


miniShop2.combo.OptionTypes = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'minishop2-combo-option-type',
        fieldLabel: _('ms2_option_type'),
        name: 'type',
        hiddenName: 'type',
        displayField: 'caption',
        valueField: 'name',
        pageSize: 20,
        fields: ['name', 'caption', 'xtype'],
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/settings/option/gettypes'
        },
        allowBlank: false,
        editable: true
    });
    miniShop2.combo.OptionTypes.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.OptionTypes, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-option-types', miniShop2.combo.OptionTypes);


miniShop2.combo.Classes = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'minishop2-combo-classes',
        fieldLabel: _('ms2_class'),
        name: 'class',
        hiddenName: 'class',
        displayField: 'class',
        valueField: 'class',
        pageSize: 20,
        fields: ['type', 'class'],
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/settings/getclass',
            type: config.type || '',
        },
        allowBlank: true,
        editable: true,
    });
    miniShop2.combo.Classes.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Classes, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-classes', miniShop2.combo.Classes);