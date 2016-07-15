miniShop2.panel.OrdersForm = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-form-orders';
    }

    Ext.apply(config, {
        layout: 'form',
        cls: 'main-wrapper',
        defaults: {msgTarget: 'under', border: false},
        anchor: '100% 100%',
        border: false,
        items: this.getFields(config),
        listeners: this.getListeners(config),
        buttons: this.getButtons(config),
        keys: this.getKeys(config),
    });
    miniShop2.panel.OrdersForm.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.OrdersForm, MODx.FormPanel, {

    grid: null,

    getFields: function (config) {
        return [{
            layout: 'column',
            items: [{
                columnWidth: .308,
                layout: 'form',
                defaults: {anchor: '100%', hideLabel: true},
                items: this.getLeftFields(config),
            }, {
                columnWidth: .37,
                layout: 'form',
                defaults: {anchor: '100%', hideLabel: true},
                items: this.getCenterFields(config),
            }, {
                columnWidth: .322,
                layout: 'form',
                defaults: {anchor: '100%', hideLabel: true},
                items: this.getRightFields(config),
            }],
        }];
    },

    getLeftFields: function (config) {
        return [{
            xtype: 'datefield',
            id: config.id + '-begin',
            emptyText: _('ms2_orders_form_begin'),
            name: 'date_start',
            format: MODx.config['manager_date_format'] || 'Y-m-d',
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            },
        }, {
            xtype: 'datefield',
            id: config.id + '-end',
            emptyText: _('ms2_orders_form_end'),
            name: 'date_end',
            format: MODx.config['manager_date_format'] || 'Y-m-d',
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            },
        }, {
            xtype: 'minishop2-combo-status',
            id: config.id + '-status',
            emptyText: _('ms2_orders_form_status'),
            name: 'status',
            addall: true,
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change')
                    }, scope: this
                }
            }
        }];
    },

    getCenterFields: function () {
        return [{
            xtype: 'displayfield',
            id: 'minishop2-orders-info',
            html: String.format('\
                <table>\
                    <tr class="top">\
                        <td><span id="minishop2-orders-info-num">0</span><br>{0}</td>\
                        <td><span id="minishop2-orders-info-sum">0</span><br>{1}</td>\
                    </tr>\
                    <tr class="bottom">\
                        <td><span id="minishop2-orders-info-month-num">0</span><br>{2}</td>\
                        <td><span id="minishop2-orders-info-month-sum">0</span><br>{3}</td>\
                    </tr>\
                </table>',
                _('ms2_orders_form_selected_num'), _('ms2_orders_form_selected_sum'),
                _('ms2_orders_form_month_num'), _('ms2_orders_form_month_sum')
            ),
        }];
    },

    getRightFields: function (config) {
        return [{
            xtype: 'textfield',
            id: config.id + '-search',
            emptyText: _('ms2_orders_form_search'),
            name: 'query',
        }, {
            xtype: 'minishop2-combo-user',
            id: config.id + '-user',
            emptyText: _('ms2_orders_form_customer'),
            name: 'customer',
            allowBlank: true,
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change')
                    }, scope: this
                }
            }
        }];
    },

    getListeners: function () {
        return {
            beforerender: function () {
                this.grid = Ext.getCmp('minishop2-grid-orders');
                var store = this.grid.getStore();
                var form = this;
                store.on('load', function (res) {
                    form.updateInfo(res.reader['jsonData']);
                });
            },
            afterrender: function() {
                var form = this;
                window.setTimeout(function() {
                    form.on('resize', function() {
                        form.updateInfo();
                    });
                }, 100);
            },
            change: function () {
                this.submit();
            },
        }
    },

    getButtons: function () {
        return [{
            text: '<i class="icon icon-times"></i> ' + _('ms2_orders_form_reset'),
            handler: this.reset,
            scope: this,
        }, {
            text: '<i class="icon icon-check"></i> ' + _('ms2_orders_form_submit'),
            handler: this.submit,
            scope: this,
            cls: 'primary-button',
        }];
    },

    getKeys: function () {
        return [{
            key: Ext.EventObject.ENTER,
            fn: function () {
                this.submit();
            },
            scope: this
        }];
    },

    submit: function () {
        var store = this.grid.getStore();
        var form = this.getForm();

        var values = form.getFieldValues();
        for (var i in values) {
            if (i != undefined && values.hasOwnProperty(i)) {
                store.baseParams[i] = values[i];
            }
        }
        this.refresh();
    },

    reset: function () {
        var store = this.grid.getStore();
        var form = this.getForm();

        form.items.each(function(f) {
            if (f.name == 'status') {
                f.clearValue();
            }
            else {
                f.reset();
            }
        });

        var values = form.getValues();
        for (var i in values) {
            if (values.hasOwnProperty(i)) {
                store.baseParams[i] = '';
            }
        }
        this.refresh();
    },

    refresh: function () {
        this.grid.getBottomToolbar().changePage(1);
    },

    updateInfo: function (data) {
        var arr = {
            'num': 'num',
            'sum': 'sum',
            'month-num': 'month_total',
            'month-sum': 'month_sum',
        };
        for (var i in arr) {
            if (!arr.hasOwnProperty(i)) {
                continue;
            }
            var text_size = 30;
            var elem = Ext.get('minishop2-orders-info-' + i);
            if (elem) {
                elem.setStyle('font-size', text_size + 'px');
                var val = data != undefined
                    ? data[arr[i]]
                    : elem.dom.innerText;
                var elem_width = elem.parent().getWidth();
                var text_width = val.length * text_size * .6;
                if (text_width > elem_width) {
                    for (var m = text_size; m >= 10; m--) {
                        if ((val.length * m * .6) < elem_width) {
                            break;
                        }
                    }
                    elem.setStyle('font-size', m + 'px');
                }
                elem.update(val);
            }
        }
    },

    focusFirstField: function () {
    },

});
Ext.reg('minishop2-form-orders', miniShop2.panel.OrdersForm);