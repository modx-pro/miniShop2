miniShop2.page.UpdateMSCategory = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-category'
		,actions: {
			'new': MODx.action ? MODx.action['resource/create'] : 'resource/create'
			,edit: MODx.action ? MODx.action['resource/update'] : 'resource/update'
			,preview: MODx.action ? MODx.action['resource/preview'] : 'resource/preview'
		}
	});
	config.canDuplicate = false;
	config.canDelete = false;
	miniShop2.page.UpdateMSCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.UpdateMSCategory,MODx.page.UpdateResource);
Ext.reg('minishop2-page-category-update',miniShop2.page.UpdateMSCategory);



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
			,stateId: 'minishop2-category-upd-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: it
		});

		var ct = this.getProducts(config);
		if (ct) {
			its.push(miniShop2.PanelSpacer);
			its.push(ct);
			its.push(miniShop2.PanelSpacer);
		}
		if (MODx.config.tvs_below_content == 1) {
			var tvs = this.getTemplateVariablesPanel(config);
			tvs.style = 'margin-top: 10px';
			its.push(tvs);
		}
		return its;
	}

	,getProducts: function(config) {
		return [{
			xtype: 'minishop2-grid-category'
			,resource: config.resource
			,border: false
		}];
	}

	,getTemplateSettings: function(config) {
		return [{
			xtype: 'minishop2-tab-template-settings'
			,record: config.record
		}];
	}

	,getComments: function(config) {
		return [{
			xtype: 'tickets-tab-comments'
			,record: config.record
			,section: config.record.id
			,layout: 'form'
		}];
	}


});
Ext.reg('minishop2-panel-category',miniShop2.panel.Section);