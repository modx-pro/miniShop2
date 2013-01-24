miniShop2.page.CreateMSCategory = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-category'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	miniShop2.page.CreateMSCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.CreateMSCategory,MODx.page.CreateResource,{});
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
		}];
	}

});
Ext.reg('minishop2-panel-category',miniShop2.panel.Section);