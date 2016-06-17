miniShop2.window.CreateVendor = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_create'),
        width: 600,
        baseParams: {
            action: 'mgr/settings/vendor/create',
        }
    });
    miniShop2.window.CreateVendor.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.CreateVendor, miniShop2.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                layout: 'column',
                items: [{
                    columnWidth: .6,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_name'),
                        name: 'name',
                        anchor: '99%',
                        id: config.id + '-name'
                    }],
                }, {
                    columnWidth: .4,
                    layout: 'form',
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_country'),
                        name: 'country',
                        anchor: '99%',
                        id: config.id + '-country'
                    }],
                }]
            }, {
                layout: 'column',
                items: [{
                    columnWidth: .4,
                    layout: 'form',
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_email'),
                        name: 'email',
                        anchor: '99%',
                        id: config.id + '-email'
                    }],
                }, {
                    columnWidth: .6,
                    layout: 'form',
                    items: [{
                        xtype: 'minishop2-combo-resource',
                        fieldLabel: _('ms2_resource'),
                        name: 'resource',
                        anchor: '99%',
                        id: config.id + '-resource'
                    }],
                }]
            }, {
                xtype: 'minishop2-combo-browser',
                fieldLabel: _('ms2_logo'),
                name: 'logo',
                anchor: '99%',
                id: config.id + '-logo'
            }, {
                xtype: 'textarea',
                fieldLabel: _('ms2_address'),
                name: 'address',
                anchor: '99%',
                id: config.id + '-address'
            }, {
                layout: 'column',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_phone'),
                        name: 'phone',
                        anchor: '99%',
                        id: config.id + '-phone'
                    }],
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_fax'),
                        name: 'fax',
                        anchor: '99%',
                        id: config.id + '-fax'
                    }],
                }]
            }, {
                xtype: 'textarea',
                fieldLabel: _('ms2_description'),
                name: 'description',
                anchor: '99%',
                id: config.id + '-description'
            }
        ];
    }

});
Ext.reg('minishop2-window-vendor-create', miniShop2.window.CreateVendor);


miniShop2.window.UpdateVendor = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_update'),
        baseParams: {
            action: 'mgr/settings/vendor/update',
        },
    });
    miniShop2.window.UpdateVendor.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.UpdateVendor, miniShop2.window.CreateVendor);
Ext.reg('minishop2-window-vendor-update', miniShop2.window.UpdateVendor);