miniShop2.panel.Utilites = function (config) {
    config = config || {};
    Ext.apply(config, { 
		cls: 'container', 
        items: [{
            html: '<h2>' + _('minishop2') + ' :: ' + _('ms2_utilites') + '</h2>',
            cls: 'modx-page-header',
        }, {
            xtype: 'modx-tabs',
            id: 'minishop2-utilites-tabs',
            stateful: true,
            stateId: 'minishop2-utilites-tabs',
            stateEvents: ['tabchange'],
            cls: 'minishop2-panel',
            getState: function () {
                return {
                    activeTab: this.items.indexOf(this.getActiveTab())
                };
            },
            items: [{
                title: _('ms2_utilites_gallery'),
                layout: 'anchor',
                items: [{
                    html: _('ms2_utilites_gallery_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-utilites-gallery',
                    cls: 'main-wrapper',
                }]
            },
            /*
            // todo
            {
                title: _('ms2_utilites_import'),
                layout: 'anchor',
                items: [{
                    html: _('ms2_utilites_import_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-utilites-import',
                    cls: 'main-wrapper',
                }]
            }*/
        ]
        }]

    });
    miniShop2.panel.Utilites.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.Utilites, MODx.Panel);
Ext.reg('minishop2-utilites', miniShop2.panel.Utilites);
