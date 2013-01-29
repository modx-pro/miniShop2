miniShop2.page.UpdateProduct = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-product'
	});
	miniShop2.page.UpdateProduct.superclass.constructor.call(this,config);

	new Ext.KeyMap(Ext.getBody(), [
		{key: 37,alt: true,fn: this.prevPage,scope: this}
		,{key: 38,alt: true,fn: this.upPage,scope: this}
		,{key: 39,alt: true,fn: this.nextPage,scope: this}
	]);
};

Ext.extend(miniShop2.page.UpdateProduct,MODx.page.UpdateResource,{

	getButtons: function(cfg) {
		var btns = [];

		if (cfg.canSave == 1) {
			btns.push({
				process: 'update'
				,text: '<i class="bicon-ok"></i> ' + _('ms2_btn_save')
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
			text: '<i class="bicon-off"></i> ' + _('ms2_btn_publish')
			,id: 'minishop2-panel-btn-publish'
			,handler: this.publishProduct
			,hidden: !cfg.canPublish || cfg.record.published
			,disabled: cfg.locked
			,scope: this
			,cls: 'btn-orange'
		});
		btns.push({
			text: '<i class="bicon-off"></i> ' + _('ms2_btn_unpublish')
			,id: 'minishop2-panel-btn-unpublish'
			,handler: this.unpublishProduct
			,hidden: !cfg.canPublish || !cfg.record.published
			,disabled: cfg.locked
			,scope: this
			//,cls: ''
		});
		btns.push('-');

		btns.push({
			text: '<i class="bicon-trash"></i> ' + _('ms2_btn_delete')
			,id: 'minishop2-panel-btn-delete'
			,handler: this.deleteProduct
			,hidden: !cfg.canDelete || cfg.record.deleted
			,disabled: cfg.locked
			,scope: this
			,cls: 'btn-brown'
		});
		btns.push({
			text: '<i class="bicon-trash"></i> ' + _('ms2_btn_undelete')
			,id: 'minishop2-panel-btn-undelete'
			,handler: this.undeleteProduct
			,hidden: !cfg.canDelete || !cfg.record.deleted
			,disabled: cfg.locked
			,scope: this
			,cls: 'btn-green'
		});
		btns.push('-');

		btns.push({
			text: '<i class="bicon-eye-open"></i> ' + _('ms2_btn_view')
			,handler: this.preview
			,scope: this
		});
		btns.push('-');

		btns.push({
			text: '<i class="bicon-arrow-left"></i>'
			,handler: this.prevPage
			,disabled: !cfg.prev_page ? 1 : 0
			,scope: this
			,tooltip: _('ms2_btn_prev')
		});
		btns.push({
			text: '<i class="bicon-arrow-up"></i>'
			,handler: this.upPage
			,scope: this
			,tooltip: _('ms2_btn_back')
		});
		btns.push({
			text: '<i class="bicon-arrow-right"></i>'
			,handler: this.nextPage
			,disabled: !cfg.next_page ? 1 : 0
			,scope: this
			,tooltip: _('ms2_btn_next')

		});
		btns.push('-');

		/*
		btns.push({
			text: '<i class="bicon-question-sign"></i>'
			,handler: this.loadHelpPane
			,tooltip: _('ms2_btn_help')
		});
		*/

		return btns;
	}

	,publishProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/publish'
				,id: this.record.id
			}
			,listeners: {
				'success':{fn:function(r) {
					var bp = Ext.getCmp('minishop2-panel-btn-publish');
					if (bp) {bp.hide();}
					var bu = Ext.getCmp('minishop2-panel-btn-unpublish');
					if (bu) {bu.show();}

					var p = Ext.getCmp('modx-resource-published');
					if (p) {p.setValue(1); }
					var po = Ext.getCmp('modx-resource-publishedon');
					if (po) {po.setValue(r.object.publishedon);}
				},scope:this}
			}
		});
	}

	,unpublishProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/unpublish'
				,id: this.record.id
			}
			,listeners: {
				'success':{fn:function(r) {
					var bp = Ext.getCmp('minishop2-panel-btn-publish');
					if (bp) {bp.show();}
					var bu = Ext.getCmp('minishop2-panel-btn-unpublish');
					if (bu) {bu.hide();}

					var p = Ext.getCmp('modx-resource-published');
					if (p) {p.setValue(0); }
					var po = Ext.getCmp('modx-resource-publishedon');
					if (po) {po.setValue('');}
				},scope:this}
			}
		});
	}

	,deleteProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/delete'
				,id: this.record.id
			}
			,listeners: {
				success: {fn:function(r) {
					var bd = Ext.getCmp('minishop2-panel-btn-delete');
					if (bd) {bd.hide();}
					var bu = Ext.getCmp('minishop2-panel-btn-undelete');
					if (bu) {bu.show();}

					var d = Ext.getCmp('modx-resource-deleted');
					if (d) {d.setValue(1);}
					var dd = Ext.getCmp('modx-resource-deletedon');
					if (dd) {dd.setValue(r.object.deletedon);}
				},scope:this}
			}
		});
	}

	,undeleteProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/undelete'
				,id: this.record.id
			}
			,listeners: {
				success: {fn:function(r) {
					var bd = Ext.getCmp('minishop2-panel-btn-delete');
					if (bd) {bd.show();}
					bu = Ext.getCmp('minishop2-panel-btn-undelete').show();
					if (bu) {bu.hide();}

					var d = Ext.getCmp('modx-resource-deleted');
					if (d) {d.setValue(0);}
					var dd = Ext.getCmp('modx-resource-deletedon');
					if (dd) {dd.setValue('');}
				},scope:this}
			}
		});
	}

	,loadHelpPane: function(b) {
		var url = MODx.config.help_url;
		if (!url) { return false; }
		MODx.helpWindow = new Ext.Window({
			title: _('help')
			,width: 850
			,height: 500
			,resizable: true
			,maximizable: true
			,modal: false
			,layout: 'fit'
			,html: '<iframe src="' + url + '" width="100%" height="100%" frameborder="0"></iframe>'
		});
		MODx.helpWindow.show(b);
		return true;
	}

	,prevPage: function(btn,e) {
		if (this.prev_page > 0) {
			var updatePage = MODx.action ? MODx.action['resource/update'] : 'resource/update';
			var id = this.prev_page;

			MODx.releaseLock(MODx.request.id);
			MODx.sleep(400);
			MODx.loadPage(action = updatePage, extraParams = 'id=' + id)
		}
	}

	,nextPage: function(btn,e) {
		if (this.next_page > 0) {
			var updatePage = MODx.action ? MODx.action['resource/update'] : 'resource/update';
			var id = this.next_page;

			MODx.releaseLock(MODx.request.id);
			MODx.sleep(400);
			MODx.loadPage(action = updatePage, extraParams = 'id=' + id)
		}
	}

	,upPage: function(btn,e) {
		var id = this.up_page;
		if (id != 0) {var upPage = MODx.action ? MODx.action['resource/update'] : 'resource/update';}
		else {var upPage = MODx.action['welcome'];}

		MODx.releaseLock(MODx.request.id);
		MODx.sleep(400);
		MODx.loadPage(action = upPage, extraParams = 'id=' + id)
	}

});
Ext.reg('minishop2-page-product-update',miniShop2.page.UpdateProduct);



miniShop2.panel.Product = function(config) {
	config = config || {};
	Ext.applyIf(config,{
	});
	miniShop2.panel.Product.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Product,MODx.panel.Resource,{
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
Ext.reg('minishop2-panel-product',miniShop2.panel.Product);