miniShop2.window.CreateLink = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_create'),
        width: 600,
        baseParams: {
            action: 'mgr/settings/link/create',
        },
    });
    miniShop2.window.CreateLink.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.CreateLink, miniShop2.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('ms2_name'),
                name: 'name',
                anchor: '99%',
                id: config.id + '-name'
            }, {
                xtype: 'minishop2-combo-link-type',
                fieldLabel: _('ms2_type'),
                name: 'type',
                anchor: '99%',
                id: config.id + '-type',
                listeners: {
                    select: {
                        fn: function (combo) {
                            this.handleLinkFields(combo);
                        }, scope: this
                    },
                    afterrender: {
                        fn: function (combo) {
                            this.handleLinkFields(combo);
                        }, scope: this
                    }
                },
                disabled: config.mode == 'update'
            }, {
                xtype: 'displayfield',
                hideLabel: true,
                cls: 'desc',
                id: config.id + '-type-desc'
            }, {
                xtype: 'textarea',
                fieldLabel: _('ms2_description'),
                name: 'description',
                anchor: '99%',
                id: config.id + '-description'
            }
        ];
    },

    handleLinkFields: function (combo) {
        var value = combo.getValue();
        if (value) {
            var desc = Ext.getCmp(this.config.id + '-type-desc');
            if (desc) {
                desc.setValue(_('ms2_link_' + value + '_desc'));
            }
        }
    },

});
Ext.reg('minishop2-window-link-create', miniShop2.window.CreateLink);


miniShop2.window.UpdateLink = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_menu_update'),
        baseParams: {
            action: 'mgr/settings/link/update',
        }
    });
    miniShop2.window.UpdateLink.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.UpdateLink, miniShop2.window.CreateLink, {

    getFields: function (config) {
        var fields = miniShop2.window.CreateLink.prototype.getFields.call(this, config);

        for (var i in fields) {
            if (!fields.hasOwnProperty(i)) {
                continue;
            }
            var field = fields[i];
            if (field.name == 'type') {
                field.disabled = true;
            }
        }

        return fields;
    }

});
Ext.reg('minishop2-window-link-update', miniShop2.window.UpdateLink);