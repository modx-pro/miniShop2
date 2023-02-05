miniShop2.combo.ComboBoxDefault = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        assertValue : function () {
            var val = this.getRawValue(),
                rec;
            if (this.valueField && Ext.isDefined(this.value)) {
                rec = this.findRecord(this.valueField, this.value);
            }
            /* fix for https://github.com/bezumkin/miniShop2/pull/350
            if(!rec || rec.get(this.displayField) != val){
                rec = this.findRecord(this.displayField, val);
            }*/
            if (rec && rec.get(this.displayField) != val) {
                rec = null;
            }
            if (!rec && this.forceSelection) {
                if (val.length > 0 && val != this.emptyText) {
                    this.el.dom.value = Ext.value(this.lastSelectionText, '');
                    this.applyEmptyText();
                } else {
                    this.clearValue();
                }
            } else {
                if (rec && this.valueField) {
                    if (this.value == val) {
                        return;
                    }
                    val = rec.get(this.valueField || this.displayField);
                }
                this.setValue(val);
            }
        },

    });
    miniShop2.combo.ComboBoxDefault.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.ComboBoxDefault, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-combobox-default', miniShop2.combo.ComboBoxDefault);


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
        tpl: new Ext.XTemplate(
            '\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <small>({id})</small>\
                        <b>{username}</b>\
                        <tpl if="fullname && fullname != username"> - {fullname}</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    miniShop2.combo.User.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.User, miniShop2.combo.ComboBoxDefault);
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
    this.on('expand', function () {
        if (!!this.pageTb) {
            this.pageTb.show();
        }
    });
};
Ext.extend(miniShop2.combo.Category, miniShop2.combo.ComboBoxDefault);
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
Ext.extend(miniShop2.combo.Autocomplete, miniShop2.combo.ComboBoxDefault);
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
        minChars: 1,
        forceSelection: false,
        baseParams: {
            action: 'mgr/settings/vendor/getlist',
            combo: true,
            id: config.value,
        }
    });
    miniShop2.combo.Vendor.superclass.constructor.call(this, config);
    this.on('expand', function () {
        if (!!this.pageTb) {
            this.pageTb.show();
        }
    });
};
Ext.extend(miniShop2.combo.Vendor, miniShop2.combo.ComboBoxDefault);
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

    if (config.mode == 'remote') {
        Ext.applyIf(config, {
            pageSize: 10,
            paging: true,
        });
    }

    Ext.applyIf(config, {
        xtype: 'superboxselect',
        allowBlank: true,
        msgTarget: 'under',
        allowAddNewData: true,
        addNewDataOnBlur: true,
        allowSorting: true,
        pinList: false,
        resizable: true,
        lazyInit: false,
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
    displayFieldTpl: config.displayFieldTpl || '{value}',
        // fix for setValue
    addValue : function (value) {
        if (Ext.isEmpty(value)) {
            return;
        }
        var values = value;
        if (!Ext.isArray(value)) {
            value = '' + value;
            values = value.split(this.valueDelimiter);
        }
        Ext.each(values,function (val) {
            var record = this.findRecord(this.valueField, val);
            if (record) {
                this.addRecord(record);
            }
            this.remoteLookup.push(val);
        },this);
        if (this.mode === 'remote') {
            var q = this.remoteLookup.join(this.queryValuesDelimiter);
            this.doQuery(q,false, true);
        }
    },
        // fix similar queries
        shouldQuery : function (q) {
            if (this.lastQuery) {
                return (q !== this.lastQuery);
            }
            return true;
        },
        onRender: function (ct, position) {
            this.constructor.prototype.onRender.apply(this, arguments);
            if (config.allowSorting) {
                this.initSorting();
            }
        },
        setValueEx : function (data) {
            // fix for setValue
            if (this.rendered && this.valueField) {
                if (!Ext.isArray(data)) {
                    data = [data];
                }
                var values = [];
                Ext.each(data,function (value, i) {
                    if (typeof value == 'string' && value != '') {
                        value = {};
                        value[this.valueField] = data[i];
                    }
                    if (typeof value == 'object' && value[this.valueField]) {
                        values.push(value);
                    }
                },this);
                data = values;
            }

            this.constructor.prototype.setValueEx.apply(this, [data]);
        },
    });
    config.name += '[]';

    Ext.apply(config, {
        listeners: {
            beforequery: {
                fn: this.beforequery,
                scope: this
            },
            newitem: {
                fn: this.newitem,
                scope: this
            },
        }
    });

    miniShop2.combo.Options.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Options, Ext.ux.form.SuperBoxSelect, {

    beforequery: function (o) {
        // reset sort
        o.combo.store.sortInfo = '';
        if (o.forceAll !== false) {
            exclude = o.combo.getValue().split(o.combo.valueDelimiter);
        } else {
            exclude = [];
        }
        o.combo.store.baseParams.exclude = Ext.util.JSON.encode(exclude);
    },

    newitem: function (bs, v) {
        bs.addNewItem({value: v});
    },

    initSorting: function () {
        var _this = this;

        if (typeof Sortable != 'undefined') {
            var item = document.querySelectorAll("#" + this.outerWrapEl.id + " ul")[0];
            if (item) {
                item.setAttribute("data-xcomponentid", this.id);
                new Sortable(item, {
                    onEnd: function (evt) {
                        if (evt.target) {
                            var cmpId = evt.target.getAttribute("data-xcomponentid");
                            var cmp = Ext.getCmp(cmpId);
                            if (cmp) {
                                _this.refreshSorting(cmp);
                                MODx.fireResourceFormChange();
                            } else {
                                console.log("Unable to reference xComponentContext.");
                            }
                        }
                    }
                });
            } else {
                console.log("Unable to find select element");
            }
        } else {
            console.log("Sortable undefined");
        }
    },

    refreshSorting: function (cmp) {
        var viewList = cmp.items.items;
        var dataInputList = document.querySelectorAll("#" + cmp.outerWrapEl.dom.id + " .x-superboxselect-input");
        var getElementIndex = function (item) {
            var nodeList = Array.prototype.slice.call(item.parentElement.children);
            return nodeList.indexOf(item);
        };
        var getElementByIndex = function (index) {
            return nodeList[index];
        };
        var getElementByValue = function (val, list) {
            for (var i = 0; i < list.length; i += 1) {
                if (list[i].value == val) {
                    return list[i];
                }
            }
        };
        var sortElementsByListIndex = function (list, callback) {
            list.sort(compare);
            if (callback instanceof Function) {
                callback();
            }
        };
        var syncElementsByValue = function (list1, list2, callback) {
            var targetListRootElement = list2[0].parentElement;
            if (targetListRootElement) {
                for (var i = 0; i < list1.length; i += 1) {
                    var targetItemIndex;
                    var item = list1[i];
                    var targetItem = getElementByValue(item.value, list2);
                    var initialTargetElement = list2[i];
                    if (targetItem !== null && initialTargetElement !== undefined) {
                        targetListRootElement.insertBefore(targetItem, initialTargetElement);
                    }
                }
            } else {
                console.debug("syncElementsByValue(), Unable to reference list root element.");
                return false;
            }
            if (callback instanceof Function) {
                callback();
            }
        };
        var compare = function (a, b) {
            var aIndex = getElementIndex(a.el.dom);
            var bIndex = getElementIndex(b.el.dom);
            if (aIndex < bIndex) {
                return -1;
            }
            if (aIndex > bIndex) {
                return 1;
            }
            return 0;
        };
        sortElementsByListIndex(viewList);
        syncElementsByValue(viewList, dataInputList[0].children);
        cmp.value = cmp.getValue();
    },

});
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
        },
        tpl: new Ext.XTemplate(
            '\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <small>({id})</small>\
                        <b>{name}</b>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    miniShop2.combo.Chunk.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Chunk, miniShop2.combo.ComboBoxDefault);
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
Ext.extend(miniShop2.combo.Resource, miniShop2.combo.ComboBoxDefault);
Ext.reg('minishop2-combo-resource', miniShop2.combo.Resource);


miniShop2.combo.Context = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'context',
        hiddenName: 'context',
        displayField: 'name',
        valueField: 'key',
        editable: true,
        fields: ['key', 'name'],
        pageSize: 20,
        emptyText: _('ms2_combo_select'),
        hideMode: 'offsets',
        url: miniShop2.config['connector_url'],
        baseParams: {
            action: 'mgr/system/element/context/getlist',
            combo: true
        }
    });
    miniShop2.combo.Context.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Context, miniShop2.combo.ComboBoxDefault);
Ext.reg('minishop2-combo-context', miniShop2.combo.Context);


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
            } else {
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
Ext.extend(miniShop2.combo.Status, miniShop2.combo.ComboBoxDefault);
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
        listeners: miniShop2.combo.listeners_disable
    });
    miniShop2.combo.Delivery.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Delivery, miniShop2.combo.ComboBoxDefault);
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
Ext.extend(miniShop2.combo.Payment, miniShop2.combo.ComboBoxDefault);
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
Ext.extend(MODx.combo.LinkType, miniShop2.combo.ComboBoxDefault, {

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
Ext.extend(miniShop2.combo.Link, miniShop2.combo.ComboBoxDefault);
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
                <div class="x-combo-list-item minishop2-product-list-item" ext:qtip="{pagetitle}">\
                    <tpl if="parents">\
                        <span class="parents">\
                            <tpl for="parents">\
                                <nobr><small>{pagetitle} / </small></nobr>\
                            </tpl>\
                        </span><br/>\
                    </tpl>\
                    <span><small>({id})</small> <b>{pagetitle}</b></span>\
                </div>\
            </tpl>', {compiled: true}),
    pageSize: 5,
    emptyText: _('ms2_combo_select'),
    editable: true,
    });
    miniShop2.combo.Product.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.combo.Product, miniShop2.combo.ComboBoxDefault);
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
Ext.extend(miniShop2.combo.ExtraOptions, miniShop2.combo.ComboBoxDefault);
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
Ext.extend(miniShop2.combo.OptionTypes, miniShop2.combo.ComboBoxDefault);
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
Ext.extend(miniShop2.combo.Classes, miniShop2.combo.ComboBoxDefault);
Ext.reg('minishop2-combo-classes', miniShop2.combo.Classes);


miniShop2.combo.ModCategory = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: config.name || 'modcategory',
        hiddenName: config.name || 'modcategory',
        displayField: 'category',
        valueField: 'id',
        anchor: '99%',
        fields: ['category', 'id'],
        pageSize: 20,
        url: miniShop2.config['connector_url'],
        typeAhead: false,
        editable: false,
        allowBlank: true,
        emptyText: _('category'),
        baseParams: {
            action: 'mgr/settings/option/getcategories',
            combo: true,
            id: config.value,
        }
    });
    miniShop2.combo.ModCategory.superclass.constructor.call(this, config);
    this.on('expand', function () {
        if (!!this.pageTb && this.pageSize < this.getStore().totalLength) {
            this.pageTb.show();
        }
    });
};
Ext.extend(miniShop2.combo.ModCategory, miniShop2.combo.ComboBoxDefault);
Ext.reg('minishop2-combo-modcategory', miniShop2.combo.ModCategory);
