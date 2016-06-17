miniShop2.window.CreateOption = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_create'),
        width: 800,
        baseParams: {
            action: 'mgr/settings/option/create',
        },
    });
    miniShop2.window.CreateOption.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.CreateOption, miniShop2.window.Default, {

    getFields: function (config) {
        return [{
            layout: 'column',
            items: [{
                columnWidth: .3,
                items: [
                    this.getTree(config)
                ]
            }, {
                columnWidth: .7,
                layout: 'form',
                items: this.getForm(config)
            }]
        }];
    },

    getTree: function (config) {
        return [{
            xtype: 'minishop2-tree-option-categories',
            id: config.id + '-option-categories',
            categories: config.record['categories'] || '',
            listeners: {
                checkchange: function () {
                    var nodes = this.getChecked();
                    var categories = [];
                    for (var i = 0; i < nodes.length; i++) {
                        categories.push(nodes[i].attributes.pk);
                    }

                    var catField = Ext.getCmp(config.id + '-categories');
                    if (catField) {
                        catField.setValue(Ext.util.JSON.encode(categories));
                    }
                }
            }
        }];
    },

    getForm: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {xtype: 'hidden', name: 'categories', id: config.id + '-categories'},
            {
                layout: 'column',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_ft_name'),
                        name: 'key',
                        //allowBlank: false,
                        anchor: '99%',
                        id: config.id + '-name'
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_ft_caption'),
                        name: 'caption',
                        //allowBlank: false,
                        anchor: '99%',
                        id: config.id + '-caption'
                    }]
                }]
            }, {
                xtype: 'minishop2-combo-option-types',
                anchor: '99%',
                id: config.id + '-types',
                listeners: {
                    select: {fn: this.onSelectType, scope: this},
                }
            }, {
                xtype: 'panel',
                anchor: '99%',
                id: config.id + '-properties-panel',
            }, {
                layout: 'column',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_ft_measure_unit'),
                        name: 'measure_unit',
                        allowBlank: true,
                        anchor: '99%',
                        id: config.id + '-measure-unit',
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'modx-combo-category',
                        fieldLabel: _('ms2_ft_group'),
                        name: 'category',
                        anchor: '99%',
                        id: config.id + '-category',
                    }]
                }]
            }, {
                xtype: 'textarea',
                fieldLabel: _('ms2_ft_description'),
                name: 'description',
                anchor: '99%',
                id: config.id + '-description'
            }
        ];
    },

    onSelectType: function (combo, row) {
        var panel = Ext.getCmp(this.config.id + '-properties-panel');
        if (panel) {
            panel.getEl().update('');
        }
        if (!row.data || !row.data['xtype']) {
            return;
        }

        MODx.load({
            xtype: row.data['xtype'],
            renderTo: this.config.id + '-properties-panel',
            record: this.record,
            name: 'properties',
        });
    },
});
Ext.reg('minishop2-window-option-create', miniShop2.window.CreateOption);


miniShop2.window.UpdateOption = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_update'),
        baseParams: {
            action: 'mgr/settings/option/update',
        }
    });
    miniShop2.window.UpdateOption.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.UpdateOption, miniShop2.window.CreateOption);
Ext.reg('minishop2-window-option-update', miniShop2.window.UpdateOption);


miniShop2.window.AssignOptions = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_category_options_assign'),
        width: 600,
        baseParams: {
            action: 'mgr/settings/option/multiple',
            method: 'assign',
        }
    });
    miniShop2.window.AssignOptions.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.AssignOptions, miniShop2.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'options', id: config.id + '-options'},
            {xtype: 'hidden', name: 'categories', id: config.id + '-categories'},
            {
                xtype: 'minishop2-tree-option-categories',
                id: config.id + '-assign-tree',
                options: config['options'] || '',
                listeners: {
                    checkchange: function () {
                        var nodes = this.getChecked();
                        var categories = [];
                        for (var i = 0; i < nodes.length; i++) {
                            categories.push(nodes[i].attributes.pk);
                        }

                        var catField = Ext.getCmp(config.id + '-categories');
                        if (catField) {
                            catField.setValue(Ext.util.JSON.encode(categories));
                        }
                    }
                }
            }
        ];
    }

});
Ext.reg('minishop2-window-option-assign', miniShop2.window.AssignOptions);