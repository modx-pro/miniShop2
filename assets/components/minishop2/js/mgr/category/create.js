miniShop2.page.CreateMSCategory = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-category'
		,mode: "create"
	});
	miniShop2.page.CreateMSCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.CreateMSCategory,MODx.page.CreateResource,{

	getButtons: function(cfg) {
		var btns = [];

		if (cfg.canSave == 1) {
			btns.push({
				process: 'create'
				,text: '<i class="bicon-ok"></i> ' + _('ms2_btn_save')
				,method: 'remote'
				,checkDirty: cfg.richtext || MODx.request.activeSave == 1 ? false : true
				,keys: [{
					key: MODx.config.keymap_save || 's'
					,ctrl: true
				}]
			});
			btns.push('-');
		}

		btns.push({
			text: '<i class="bicon-ban-circle"></i>' + _('ms2_btn_cancel')
			,handler: this.upPage
			,scope: this
			,tooltip: _('ms2_btn_back')
		});

		/*
		 btns.push({
		 text: '<i class="bicon-question-sign"></i>'
		 ,handler: this.loadHelpPane
		 ,tooltip: _('ms2_product_help')
		 });
		 */

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
Ext.reg('minishop2-page-category-create',miniShop2.page.CreateMSCategory);





miniShop2.panel.Section = function(config) {
	config = config || {};
	miniShop2.panel.Section.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Section,MODx.panel.Resource,{

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
			,stateful: true
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

});
Ext.reg('minishop2-panel-category',miniShop2.panel.Section);