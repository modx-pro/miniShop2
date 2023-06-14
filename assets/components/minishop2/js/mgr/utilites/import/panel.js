miniShop2.panel.UtilitesImport = function (config) {
    config = config || {};

    Ext.apply(config, {
		cls: 'container form-with-labels',
		autoHeight: true,
		url: miniShop2.config.connector_url,
		progress:true,
		id: 'ms2-panel-import',
		baseParams: {
			action: 'mgr/utilites/import/import'
		},
        items: [{
            layout: 'column',
            border: false,
            anchor: '100%',
            cls: 'main-wrapper',
            labelAlign: 'top',
            buttonAlign: 'left',
            style: 'padding: 0 0 0 7px',
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                defaults: { msgTarget: 'under' },
                border: false,
                style: { margin: '0' },
                items: [
                    {
                        xtype: 'modx-combo-browser',
                        fieldLabel: _('ms2_utilites_import_label_file'),
                        emptyText: _('ms2_utilites_import_label_file_empty'),
                        anchor: '81%',
                        name: 'importfile',
                        allowBlank: false,
                    },
                    {
                        layout: 'column',
                        items:[{
                            columnWidth: 0.8,
                            layout: 'form',
                            border: false,
                            style: { margin: '0' },
                            items:[
                                {
                                    xtype: 'textfield',
                                    name: 'fields',
                                    value: miniShop2.config.utility_import_fields,
                                    width: '99%',
                                    fieldLabel: _('ms2_utilites_import_label_fields'),
                                    allowBlank: false,
                                },
                                {
                                    xtype: 'textfield',
                                    name: 'delimiter',
                                    value:  miniShop2.config.utility_import_fields_delimiter,
                                    width: '99%',
                                    allowBlank: false,
                                    fieldLabel: _('ms2_utilites_import_label_delimiter'),
                                }
                            ]
                        },
                        {
                            columnWidth: 0.2,
                            layout: 'form',
                            border: false,
                            style: { margin: '20px 0 0 15px' },
                            items:[
                                {
                                    xtype: 'button',
                                    style: 'padding: 4px 10px 7px; margin: 18px 0 0 0',
                                    tooltip: _('ms2_utilites_import_save_fields'),
                                    text: '<i class="icon icon-save"></i>',
                                    handler: function() {
                                        this.saveConfig(this);
                                    }, scope: this
                                }
                            ]

                        }]
                    },
                    {
                        xtype: 'xcheckbox',
                        name: 'update',
                        value: 1,
                        id: 'ms-utilites-import-update',
                        boxLabel: _('ms2_utilites_import_update_products'),
                        labelAlign: 'right',
                        listeners: {
                            check: {
                                fn : this.onUpdateNeed,
                                scope : this
                            },
                        }
                    },
                    {
                        xtype: 'textfield',
                        name: 'key',
                        value: 'article',
                        width: '99%',
                        hidden: true,
                        id: 'ms-utilites-import-key',
                        fieldLabel: _('ms2_utilites_import_update_key'),
                    },
                    {
                        xtype: 'xcheckbox',
                        name: 'debug',
                        value: 1,
                        hideLabel: true,
                        id: 'ms-utilites-import-debug',
                        boxLabel: _('ms2_utilites_import_debug'),
                        labelAlign: 'right',
                    },
                    {
                        xtype: 'xcheckbox',
                        name: 'scheduler',
                        value: 1,
                        hideLabel: true,
                        id: 'ms-utilites-import-scheduler',
                        boxLabel: _('ms2_utilites_import_use_scheduler'),
                        labelAlign: 'right',
                    },
                    {
                        xtype: 'xcheckbox',
                        name: 'skip_header',
                        value: 1,
                        hideLabel: true,
                        id: 'ms-utilites-import-header',
                        boxLabel: _('ms2_utilites_import_skip_header'),
                        labelAlign: 'right',
                    },
                    {
                        xtype: 'button',
                        style: 'margin: 25px 0 0 2px',
                        text: '<i class="icon icon-download"></i> &nbsp;'+_('ms2_utilites_import_submit'),
                        handler: function() {
                            this.submit(this);
                        }, scope: this
                    }
                ]
            }/*, {
                columnWidth: 0.5,
                layout: 'form',
                defaults: { msgTarget: 'under' },
                border: false,
                style: { margin: '0 0 0 20px' },
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Инструкция',
                        id: 'ms2-utilites-import-instruction',
                        cls: 'x-fieldset-checkbox-toggle',
                        style: 'margin: 5px 0 15px; padding: 20px; ',
                        collapsible: true,
                        collapsed: true,
                        stateful: true,
                        labelAlign: 'top',
                        stateEvents: ['collapse', 'expand'],
                        items: [
                            {
                                html:  ''
                            },
                        ]
                    }
                ]
            }*/]
        }] ,
        listeners: {
            success: {fn: function(response) {
				var data = response.result.object;
                MODx.msg.alert(_('info'), data.message);
			},scope: this},
			failure: {
			    fn:function(response) {
				    console.log(response);
		        },scope: this
			}
        }
    });

    miniShop2.panel.UtilitesImport.superclass.constructor.call(this, config);
};

Ext.extend(miniShop2.panel.UtilitesImport, MODx.FormPanel,{

    onUpdateNeed: function (cb) {
       var updateKey = Ext.getCmp('ms-utilites-import-key');
        if (cb.getValue()) {
            updateKey.show();
        } else {
            updateKey.hide();
        }
    },

    saveConfig: function () {
        var form = this.getForm();
        var values = form.getValues();

        MODx.Ajax.request({
            url: miniShop2.config['connector_url'],
            params: {
                action: 'mgr/utilites/import/saveconfig',
                fields: values.fields,
                delimiter: values.delimiter
            },
            listeners: {
                success: {
                    fn: function (r) {
                        MODx.msg.status({
    						title: _('ms2_utilites_import_save_fields_title'),
    						message:  _('ms2_utilites_import_save_fields_message'),
    						delay: 7
    					});
                    }, scope: this
                }
            }
        });

    }

});
Ext.reg('minishop2-utilites-import', miniShop2.panel.UtilitesImport);
