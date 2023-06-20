miniShop2.panel.Utilities = function (config) {
    config = config || {};
    Ext.apply(config, {
        cls: 'container',
        items: [{
            html: '<h2>' + _('minishop2') + ' :: ' + _('ms2_utilities') + '</h2>',
            cls: 'modx-page-header',
        }, {
            xtype: 'modx-tabs',
            id: 'minishop2-utilities-tabs',
            stateful: true,
            stateId: 'minishop2-utilities-tabs',
            stateEvents: ['tabchange'],
            cls: 'minishop2-panel',
            getState: function () {
                return {
                    activeTab: this.items.indexOf(this.getActiveTab())
                };
            },
            items: [{
                title: _('ms2_utilities_gallery'),
                layout: 'anchor',
                items: [{
                    html: _('ms2_utilities_gallery_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-utilities-gallery',
                    cls: 'main-wrapper',
                }]
            },
            {
                title: _('ms2_utilities_import'),
                layout: 'anchor',
                items: [{
                    html: _('ms2_utilities_import_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-utilities-import',
                    cls: 'main-wrapper',
                }]
            }
            ]
        }]

    });
    miniShop2.panel.Utilities.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.Utilities, MODx.Panel);
Ext.reg('minishop2-utilities', miniShop2.panel.Utilities);
