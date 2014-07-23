miniShop2.page.CreateCategory = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-category-create'
		,mode: "create"
	});
	miniShop2.page.CreateCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.CreateCategory,MODx.page.CreateResource,{

	getButtons: function(cfg) {
		var btns = [];

		if (cfg.canSave == 1) {
			btns.push({
				process: MODx.modx23 ? 'resource/create' : 'create'
				,text: '<i class="'+ (MODx.modx23 ? 'icon icon-check' : 'bicon-ok') + '"></i> ' + _('ms2_btn_save')
				,method: 'remote'
				,checkDirty: cfg.richtext || MODx.request.activeSave == 1 ? false : true
				,cls: 'primary-button'
				,keys: [{
					key: MODx.config.keymap_save || 's'
					,ctrl: true
				}]
			});
			btns.push('-');
		}

		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-ban' : 'bicon-ban-circle') + '"></i> ' + _('ms2_btn_cancel')
			,handler: this.upPage
			,scope: this
			,tooltip: _('ms2_btn_back')
			,keys: [{key: 38,alt: true, scope: this, fn: this.upPage}]
		});

		return btns;
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

	,upPage: function(btn,e) {
		var id = MODx.request.parent;
		if (id != 0) {var upPage = MODx.action ? MODx.action['resource/update'] : 'resource/update';}
		else {var upPage = MODx.action['welcome'];}

		var fp = Ext.getCmp(this.config.formpanel);
		if (fp && fp.isDirty()) {
			Ext.Msg.confirm(_('warning'),_('ms2_product_dirty_confirm'),function(e) {
				if (e == 'yes') {
					MODx.loadPage(upPage, 'id=' + id)
				}
			},this);
		} else {
			MODx.loadPage(upPage, 'id=' + id)
		}
	}
});
Ext.reg('minishop2-page-category-create',miniShop2.page.CreateCategory);


miniShop2.panel.CreateCategory = function(config) {
	config = config || {};
	miniShop2.panel.CreateCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.CreateCategory,MODx.panel.Resource,{

	getFields: function(config) {
		var it = [];
		it.push({
			title: _('ms2_category')
			,id: 'modx-resource-settings'
			,cls: 'modx-resource-tab'
			,layout: 'form'
			,labelAlign: 'top'
			,labelSeparator: ''
			,bodyCssClass: 'tab-panel-wrapper main-wrapper'
			,autoHeight: true
			,defaults: {
				border: false
				,msgTarget: 'side'
				,width: 400
			}
			,items: this.getMainFields(config)
		});
		it.push({
			title: _('settings')
			,id: 'modx-minishop2-template'
			,cls: 'modx-resource-tab'
			,layout: 'form'
			,labelAlign: 'top'
			,labelSeparator: ''
			,bodyCssClass: 'tab-panel-wrapper main-wrapper'
			,autoHeight: true
			,defaults: {
				border: false
				,msgTarget: 'side'
				,width: 400
			}
			,items: this.getTemplateSettings(config)
		});
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
			,stateful: MODx.config.ms2_category_remember_tabs == true
			,stateId: 'minishop2-category-new-tabpanel'
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

	,getPageHeader: function(config) {
		config = config || {record:{}};
		return {
			html: '<h2>'+_('ms2_category_new')+'</h2>'
			,id: 'modx-resource-header'
			,cls: 'modx-page-header'
			,border: false
			,forceLayout: true
			,anchor: '100%'
		};
	}

	,getTemplateSettings: function(config) {
		return [{
			xtype: 'minishop2-tab-template-settings'
			,record: config.record
			,mode: "create"
		}];
	}

	,templateWarning: function() {
		var t = Ext.getCmp('modx-resource-template');
		if (!t) { return false; }
		if(t.getValue() !== t.originalValue) {
			Ext.Msg.confirm(_('warning'), _('resource_change_template_confirm'), function(e) {
				if (e == 'yes') {
					var nt = t.getValue();
					var f = Ext.getCmp('modx-page-update-resource');
					f.config.action = MODx.modx23 ? 'resource/reload' : 'reload';
					MODx.activePage.submitForm({
						success: {fn:function(r) {
							var page = MODx.action ? MODx.action[r.result.object.action] : r.result.object.action;
							MODx.loadPage(page, 'id='+r.result.object.id+'&reload='+r.result.object.reload+'&class_key='+this.config.record.class_key);
						},scope:this}
					},{
						bypassValidCheck: true
					},{
						reloadOnly: true
					});
				} else {
					t.setValue(this.config.record.template);
				}
			},this);
			return true;
		}
	}
});
Ext.reg('minishop2-panel-category-create',miniShop2.panel.CreateCategory);