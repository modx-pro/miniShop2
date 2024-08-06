miniShop2.panel.Settings = function (config) {
    config = config || {};
    Ext.apply(config, {
        cls: 'container',
        items: [{
            html: '<h2>' + _('minishop2') + ' :: ' + _('ms2_settings') + '</h2>',
            cls: 'modx-page-header',
        }, {
            xtype: 'modx-tabs',
            id: 'minishop2-settings-tabs',
            stateful: true,
            stateId: 'minishop2-settings-tabs',
            stateEvents: ['tabchange'],
            cls: 'minishop2-panel',
            getState: function () {
                return {
                    activeTab: this.items.indexOf(this.getActiveTab())
                };
            },
            listeners: {
                tabchange: function (tabPanel, tab) {
                    window.location.hash = '#tab-' + tab.id;
                },
                render: function (tabPanel) {
                    let tabHash = window.location.hash.substring(1);
                    if (tabHash) {
                        let tabId = tabHash.replace("tab-", "");
                        let tab = tabPanel.get(tabId);
                        if (tab) {
                            tabPanel.setActiveTab(tab);
                        }
                    }
                }
            },
            items: [
              {
                title: _('ms2_deliveries'),
                layout: 'anchor',
                id: 'deliveries',
                items: [{
                    html: _('ms2_deliveries_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-grid-delivery',
                    cls: 'main-wrapper',
                }]
            }, {
                title: _('ms2_payments'),
                layout: 'anchor',
                id: 'payments',
                items: [{
                    html: _('ms2_payments_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-grid-payment',
                    cls: 'main-wrapper',
                }]
            }, {
                title: _('ms2_statuses'),
                layout: 'anchor',
                id: 'statuses',
                items: [{
                    html: _('ms2_statuses_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-grid-status',
                    cls: 'main-wrapper',
                }]
            }, {
                title: _('ms2_vendors'),
                layout: 'anchor',
                id: 'vendors',
                items: [{
                    html: _('ms2_vendors_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-grid-vendor',
                    cls: 'main-wrapper',
                }]
            }, {
                title: _('ms2_links'),
                layout: 'anchor',
                id: 'links',
                items: [{
                    html: _('ms2_links_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'minishop2-grid-link',
                    cls: 'main-wrapper',
                }]
            }, {
                title: _('ms2_options'),
                layout: 'anchor',
                id: 'options',
                items: [{
                    html: _('ms2_options_intro'),
                    bodyCssClass: 'panel-desc',
                }, {
                    layout: 'column',
                    cls: 'main-wrapper',
                    items: [{
                        xtype: 'minishop2-tree-option-categories',
                        optionGrid: 'minishop2-grid-option',
                        columnWidth: .25
                    }, {
                        xtype: 'minishop2-grid-option',
                        columnWidth: .75,
                    }]
                }]
            }]
        }]
    });
    miniShop2.panel.Settings.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.panel.Settings, MODx.Panel);
Ext.reg('minishop2-panel-settings', miniShop2.panel.Settings);
