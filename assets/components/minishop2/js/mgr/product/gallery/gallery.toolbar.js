miniShop2.panel.Toolbar = function (config) {
    config = config || {};

    Ext.apply(config, {
        id: 'minishop2-gallery-page-toolbar',
        items: [{
            id: 'minishop2-resource-upload-btn',
            text: '<i class="icon icon-upload"></i> ' + _('ms2_gallery_button_upload'),
        }, {
            text: '<i class="icon icon-cogs"></i> ',
            cls: 'minishop2-btn-actions',
            menu: [{
                text: '<i class="icon icon-refresh"></i> ' + _('ms2_gallery_file_generate_all'),
                cls: 'minishop2-btn-action',
                handler: function () {
                    this.fileAction('generateAllThumbs')
                },
                scope: this,
            }, '-', {
                text: '<i class="icon icon-trash-o action-red"></i> ' + _('ms2_gallery_file_delete_all'),
                cls: 'minishop2-btn-action',
                handler: function () {
                    this.fileAction('deleteAllFiles')
                },
                scope: this,
            },]
        },'->', {
            xtype: 'displayfield',
            html: '<b>' + _('ms2_product_source') + '</b>:&nbsp;&nbsp;'
        }, '-', {
            xtype: 'minishop2-combo-source',
            id: 'minishop2-resource-source',
            description: '<b>[[+source]]</b><br />' + _('ms2_product_source_help'),
            value: config.record.source,
            name: 'source',
            hiddenName: 'source',
            listeners: {
                select: {
                    fn: this.sourceWarning,
                    scope: this
                }
            }
        }]
    });
    miniShop2.panel.Toolbar.superclass.constructor.call(this, config);
    this.config = config;
};
Ext.extend(miniShop2.panel.Toolbar, Ext.Toolbar, {

    sourceWarning: function (combo) {
        var source_id = this.config.record.source;
        var sel_id = combo.getValue();
        if (source_id != sel_id) {
            Ext.Msg.confirm(_('warning'), _('ms2_product_change_source_confirm'), function (e) {
                if (e == 'yes') {
                    combo.setValue(sel_id);
                    MODx.activePage.submitForm({
                        success: {
                            fn: function (r) {
                                var page = 'resource/update';
                                MODx.loadPage(page, 'id=' + r.result.object.id);
                            }, scope: this
                        }
                    });
                } else {
                    combo.setValue(source_id);
                }
            }, this);
        }
    },

    fileAction: function (method) {
        var view = Ext.getCmp('minishop2-gallery-images-view');
        if (view && typeof view[method] === 'function') {
            return view[method].call(view, arguments);
        }
    },

});
Ext.reg('minishop2-gallery-page-toolbar', miniShop2.panel.Toolbar);
