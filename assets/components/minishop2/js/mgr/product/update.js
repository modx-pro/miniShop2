miniShop2.page.UpdateProduct = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'modx-panel-product'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	miniShop2.page.UpdateProduct.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.UpdateProduct,MODx.page.UpdateResource,{

	getButtons: function(cfg) {
		var btns = [];
		if (cfg.canSave == 1) {
			btns.push({
				process: 'update'
				,text: _('save')
				,method: 'remote'
				,checkDirty: cfg.richtext || MODx.request.activeSave == 1 ? false : true
				,keys: [{
					key: MODx.config.keymap_save || 's'
					,ctrl: true
				}]
			});
			btns.push('-');
		} else if (cfg.locked) {
			btns.push({
				text: cfg.lockedText || _('locked')
				,handler: Ext.emptyFn
				,disabled: true
			});
			btns.push('-');
		}
		btns.push({
			text: _('resource_publish')
			,id: 'modx-ticket-publish'
			,hidden: cfg.record.published ? true : false
			,handler: this.publishTicket
		});
		btns.push({
			text: _('resource_unpublish')
			,id: 'modx-ticket-unpublish'
			,hidden: cfg.record.published ? false : true
			,handler: this.unpublishTicket
		});
		btns.push('-');
		btns.push({
			process: 'preview'
			,text: _('view')
			,handler: this.preview
			,scope: this
		});
		btns.push('-');
		btns.push({
			process: 'cancel'
			,text: _('cancel')
			,handler: this.cancel
			,scope: this
		});
		btns.push('-');
		btns.push({
			text: _('help_ex')
			,handler: MODx.loadHelpPane
		});
		return btns;
	}

	,publishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'publish'
				,id: MODx.request.id
			}
			,listeners: {
				'success':{fn:function(r) {
					var p = Ext.getCmp('modx-resource-published');
					if (p) {
						p.setValue(1);
					}
					var po = Ext.getCmp('modx-resource-publishedon');
					if (po) {
						po.setValue(r.object.publishedon);
					}
					var bp = Ext.getCmp('modx-ticket-publish');
					if (bp) {
						bp.hide();
					}
					var bu = Ext.getCmp('modx-ticket-unpublish');
					if (bu) {
						bu.show();
					}
				},scope:this}
			}
		});
	}

	,unpublishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'unpublish'
				,id: MODx.request.id
			}
			,listeners: {
				'success':{fn:function(r) {
					var p = Ext.getCmp('modx-resource-published');
					if (p) {
						p.setValue(0);
					}
					var po = Ext.getCmp('modx-resource-publishedon');
					if (po) {
						po.setValue('');
					}
					var bp = Ext.getCmp('modx-ticket-publish');
					if (bp) {
						bp.show();
					}
					var bu = Ext.getCmp('modx-ticket-unpublish');
					if (bu) {
						bu.hide();
					}
				},scope:this}
			}
		});
	}

	,cancel: function(btn,e) {
		var updatePage = MODx.action ? MODx.action['resource/update'] : 'resource/update';
		var fp = Ext.getCmp(this.config.formpanel);
		if (fp && fp.isDirty()) {
			Ext.Msg.confirm(_('warning'),_('resource_cancel_dirty_confirm'),function(e) {
				if (e == 'yes') {
					MODx.releaseLock(MODx.request.id);
					MODx.sleep(400);
					location.href = 'index.php?a='+updatePage+'&id='+this.config.record['parent'];
				}
			},this);
		} else {
			MODx.releaseLock(MODx.request.id);
			location.href = 'index.php?a='+updatePage+'&id='+this.config.record['parent'];
		}
	}
});
Ext.reg('minishop2-page-product-update',miniShop2.page.UpdateProduct);



miniShop2.panel.Ticket = function(config) {
	config = config || {};
	Ext.applyIf(config,{
	});
	miniShop2.panel.Ticket.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Ticket,MODx.panel.Resource,{
	getFields: function(config) {
		var it = [];
		it.push({
			title: _('ms2_product')
			,id: 'modx-resource-settings'
			,cls: 'modx-resource-tab'
			,layout: 'form'
			,labelAlign: 'top'
			,labelSeparator: ''
			,bodyCssClass: 'tab-panel-wrapper main-wrapper'
			,autoHeight: true
			,defaults: {
				border: false
				,msgTarget: 'under'
				,width: 400
			}
			,items: this.getMainFields(config)
		});

		if (miniShop2.config.show_comments) {
			it.push({
				title: _('comments')
				,id: 'modx-tickets-comments'
				,cls: 'modx-resource-tab'
				,layout: 'form'
				,labelAlign: 'top'
				,labelSeparator: ''
				,bodyCssClass: 'tab-panel-wrapper main-wrapper'
				,autoHeight: true
				,items: this.getComments(config)
			});
		}

		if (config.show_tvs && MODx.config.tvs_below_content != 1) {
			it.push(this.getTemplateVariablesPanel(config));
		}
		if (MODx.perm.resourcegroup_resource_list == 1) {
			it.push(this.getAccessPermissionsTab(config));
		}
		var its = [];
		its.push(this.getPageHeader(config),{
			id:'modx-resource-tabs'
			,xtype: 'modx-tabs'
			,forceLayout: true
			,deferredRender: false
			,collapsible: true
			,itemId: 'tabs'
			,stateful: true
			,stateId: 'minishop2-product-upd-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: it
		});

		if (MODx.config.tvs_below_content == 1) {
			var tvs = this.getTemplateVariablesPanel(config);
			tvs.style = 'margin-top: 10px';
			its.push(tvs);
		}
		return its;
	}

	,getMainLeftFields: function(config) {
		config = config || {record:{}};
		var mlf = [{
			xtype: 'textfield'
			,fieldLabel: _('resource_pagetitle')+'<span class="required">*</span>'
			,description: '<b>[[*pagetitle]]</b><br />'+_('resource_pagetitle_help')
			,name: 'pagetitle'
			,id: 'modx-resource-pagetitle'
			,maxLength: 255
			,anchor: '100%'
			,allowBlank: false
			,enableKeyEvents: true
			,listeners: {
				'keyup': {scope:this,fn:function(f,e) {
					var title = Ext.util.Format.stripTags(f.getValue());
					Ext.getCmp('modx-resource-header').getEl().update('<h2>'+title+'</h2>');
				}}
			}
		}];

		mlf.push({
			xtype: 'textfield'
			,fieldLabel: _('resource_longtitle')
			,description: '<b>[[*longtitle]]</b><br />'+_('resource_longtitle_help')
			,name: 'longtitle'
			,id: 'modx-resource-longtitle'
			,anchor: '100%'
			,value: config.record.longtitle || ''
		});

		mlf.push({
			xtype: 'textarea'
			,fieldLabel: _('resource_summary')
			,description: '<b>[[*introtext]]</b><br />'+_('resource_summary_help')
			,name: 'introtext'
			,id: 'modx-resource-introtext'
			,anchor: '100%'
			,value: config.record.introtext || ''
		});


		var ct = this.getContentField(config);
		if (ct) {
			mlf.push(ct);
		}
		return mlf;
	}

	,getContentField: function(config) {
		return [{
			id: 'modx-content-above'
			,border: false
		},{
			xtype: 'textarea'
			,fieldLabel: _('content')
			,name: 'ta'
			,id: 'ta'
			,anchor: '100%'
			,height: 400
			,grow: false
			,value: (config.record.content || config.record.ta) || ''
		},{
			id: 'modx-content-below'
			,border: false
		}];
	}


	,getMainRightFields: function(config) {
		config = config || {};
		return {};
		/*
		return [{
			xtype: 'fieldset'
			,title: _('ticket_publishing_information')
			,id: 'tickets-box-publishing-information'
			,defaults: {
				msgTarget: 'under'
			}
			,items: [{
				xtype: 'tickets-combo-publish-status'
				,id: 'modx-resource-published'
				,name: 'published'
				,hiddenName: 'published'
				,fieldLabel: _('ticket_status')
			},{
				xtype: 'xdatetime'
				,fieldLabel: _('resource_publishedon')
				,description: '<b>[[*publishedon]]</b><br />'+_('resource_publishedon_help')
				,name: 'publishedon'
				,id: 'modx-resource-publishedon'
				,allowBlank: true
				,dateFormat: MODx.config.manager_date_format
				,timeFormat: MODx.config.manager_time_format
				,dateWidth: 120
				,timeWidth: 120
				,value: config.record.publishedon
			},{
				xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden'
				,fieldLabel: _('resource_publishdate')
				,description: '<b>[[*pub_date]]</b><br />'+_('resource_publishdate_help')
				,name: 'pub_date'
				,id: 'modx-resource-pub-date'
				,allowBlank: true
				,dateFormat: MODx.config.manager_date_format
				,timeFormat: MODx.config.manager_time_format
				,dateWidth: 120
				,timeWidth: 120
				,value: config.record.pub_date
			},{
				xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden'
				,fieldLabel: _('resource_unpublishdate')
				,description: '<b>[[*unpub_date]]</b><br />'+_('resource_unpublishdate_help')
				,name: 'unpub_date'
				,id: 'modx-resource-unpub-date'
				,allowBlank: true
				,dateFormat: MODx.config.manager_date_format
				,timeFormat: MODx.config.manager_time_format
				,dateWidth: 120
				,timeWidth: 120
				,value: config.record.unpub_date
			},{
				xtype: MODx.config.publish_document ? 'modx-combo-user' : 'hidden'
				,fieldLabel: _('resource_createdby')
				,description: '<b>[[*createdby]]</b><br />'+_('resource_createdby_help')
				,name: 'created_by'
				,hiddenName: 'createdby'
				,id: 'modx-resource-createdby'
				,allowBlank: true
				,baseParams: {
					action: 'getList'
					,combo: '1'
					,limit: 0
				}
				,anchor: '90%'
				,value: config.record.createdby
			},{
				xtype: MODx.config.publish_document ? 'tickets-combo-section' : 'hidden'
				,id: 'tickets-combo-section'
				,fieldLabel: _('resource_parent')
				,description: '<b>[[*parent]]</b><br />'+_('resource_parent_help')
				,value: config.record.parent
				,url: miniShop2.config.connector_url
				,listeners: {
					'select': {
						fn:function(data) {
							Ext.getCmp('modx-resource-parent-hidden').setValue(data.value);
						}
					}
				}
				,anchor: '90%'
			}]
		},{
			html: '<hr />'
			,border: false
		},{
			xtype: 'fieldset'
			,title: _('ticket_ticket_options')
			,id: 'tickets-box-options'
			,anchor: '100%'
			,defaults: {
				labelSeparator: ''
				,labelAlign: 'right'
				,layout: 'form'
				,msgTarget: 'under'
			}
			,items: [{
				xtype: 'modx-combo-template'
				,fieldLabel: _('resource_template')
				,description: '<b>[[*template]]</b><br />'+_('resource_template_help')
				,name: 'template'
				,id: 'modx-resource-template'
				,anchor: '90%'
				,editable: false
				,baseParams: {
					action: 'getList'
					,combo: '1'
				}
			},{
				xtype: 'xcheckbox'
				,name: 'richtext'
				,boxLabel: _('resource_richtext')
				,description: '<b>[[*richtext]]</b><br />'+_('resource_richtext_help')
				,id: 'modx-resource-richtext'
				,inputValue: 1
				,checked: parseInt(config.record.richtext)
			},{
				xtype: 'xcheckbox'
				,name: 'properties[disable_jevix]'
				,boxLabel: _('ticket_disable_jevix')
				,description: _('ticket_dialiassable_jevix_help')
				,id: 'modx-resource-disablejevix'
				,inputValue: 1
				,checked: parseInt(config.record.properties.disable_jevix)
			},{
				xtype: 'xcheckbox'
				,name: 'properties[process_tags]'
				,boxLabel: _('ticket_process_tags')
				,description: _('ticket_process_tags_help')
				,id: 'modx-resource-process_tags'
				,inputValue: 1
				,checked: parseInt(config.record.properties.process_tags)
			},{
				xtype: 'xcheckbox'
				,name: 'privateweb'
				,boxLabel: _('ticket_private')
				,description: _('ticket_private_help')
				,id: 'modx-resource-privateweb'
				,inputValue: 1
				,checked: parseInt(config.record.properties.privateweb)
			},{
				xtype: 'hidden'
				,name: 'alias'
				,id: 'modx-resource-alias'
				,value: config.record.alias || ''
			},{
				xtype: 'hidden'
				,name: 'menutitle'
				,id: 'modx-resource-menutitle'
				,value: config.record.menutitle || ''
			},{
				xtype: 'hidden'
				,name: 'link_attributes'
				,id: 'modx-resource-link-attributes'
				,value: config.record.link_attributes || ''
			},{
				xtype: 'hidden'
				,name: 'hidemenu'
				,id: 'modx-resource-hidemenu'
				,value: config.record.hidemenu
			}]
		}]
		*/
	}

	,getComments: function(config) {
		return [{
			xtype: 'tickets-tab-comments'
			,record: config.record
			,parents: config.record.id
			,layout: 'form'
		}];
	}

});
Ext.reg('modx-panel-product',miniShop2.panel.Ticket);