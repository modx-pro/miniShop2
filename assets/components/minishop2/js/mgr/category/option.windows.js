miniShop2.window.AddOption = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_category_option_add'),
        width: 600,
        baseParams: {
            action: 'mgr/category/option/add',
        },
    });
    miniShop2.window.AddOption.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.AddOption, miniShop2.window.Default, {
    getFields: function () {
        return [
            {xtype: 'hidden', name: 'category_id'},
            {
                xtype: 'minishop2-combo-extra-options',
                anchor: '99%',
                name: 'option_id',
                hiddenName: 'option_id'
            }, {
                xtype: 'textfield',
                anchor: '99%',
                name: 'value',
                fieldLabel: _('ms2_default_value')
            }, {
                xtype: 'checkboxgroup',
                fieldLabel: _('ms2_options'),
                columns: 1,
                items: [
                    {xtype: 'xcheckbox', boxLabel: _('ms2_active'), name: 'active'},
                    {xtype: 'xcheckbox', boxLabel: _('ms2_required'), name: 'required'}
                ]
            }
        ];
    },

    getKeys: function () {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            },
            scope: this
        }];
    }
});
Ext.reg('minishop2-window-option-add', miniShop2.window.AddOption);


miniShop2.window.CopyCategory = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_category_option_copy'),
        width: 600,
        baseParams: {
            action: 'mgr/category/option/duplicate',
        },
    });
    miniShop2.window.CopyCategory.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.CopyCategory, miniShop2.window.Default, {

    getFields: function () {
        return [
            {xtype: 'hidden', name: 'category_to'},
            {
                xtype: 'minishop2-combo-category',
                anchor: '99%',
                name: 'category_from',
                hiddenName: 'category_from'
            }
        ];
    },

    getKeys: function () {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            },
            scope: this
        }];
    }
});
Ext.reg('minishop2-window-copy-category', miniShop2.window.CopyCategory);