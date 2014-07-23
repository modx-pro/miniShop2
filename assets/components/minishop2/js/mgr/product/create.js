miniShop2.page.CreateProduct = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-product'
	});
	miniShop2.page.CreateProduct.superclass.constructor.call(this,config);
};

Ext.extend(miniShop2.page.CreateProduct,MODx.page.CreateResource,{

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
Ext.reg('minishop2-page-product-create',miniShop2.page.CreateProduct);



miniShop2.panel.Product = function(config) {
	config = config || {};
	//Ext.applyIf(config,{});
	miniShop2.panel.Product.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Product,MODx.panel.Resource,{
	getFields: function(config) {
		var it = [];

		it.push({
			title: _('ms2_product_properties')
			,id: 'minishop2-product-settings'
			,cls: 'modx-resource-tab'
			,labelAlign: 'top'
			,labelSeparator: ''
			,bodyCssClass: 'tab-panel-wrapper form-with-labels'
			,autoHeight: true
			,items: this.getTabSettings(config)
		});

		if (config.show_tvs && MODx.config.tvs_below_content != 1) {
			it.push(this.getTemplateVariablesPanel(config));
		}
		/*
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
		 */

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
			,stateful: MODx.config.ms2_product_remember_tabs == true
			,stateId: 'minishop2-product-new-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return {activeTab:
				MODx.config.ms2_product_remember_tabs ? this.items.indexOf(this.getActiveTab()) : 0
			};}
			,items: it
		});

		if (MODx.config.tvs_below_content == 1) {
			var tvs = this.getTemplateVariablesPanel(config);
			tvs.style = 'margin-top: 10px';
			its.push(tvs);
		}
		return its;
	}

	,getTabSettings: function(config) {
		var xtype;
		if (!miniShop2.config.product_tab_extra && !miniShop2.config.product_tab_gallery && !miniShop2.config.product_tab_links) {
			xtype = 'minishop2-product-settings-simple';
		}
		else {
			xtype = miniShop2.config.vertical_tabs
				? 'minishop2-product-settings'
				: 'minishop2-product-settings-horizontal';
		}

		return [{
			xtype: xtype
			,record: config.record
			,mode: config.mode
		}];
	}

	,success: function(o) {
		var g = Ext.getCmp('modx-grid-resource-security');
		if (g) {g.getStore().commitChanges();}
		var t = Ext.getCmp('modx-resource-tree');

		if (t) {
			var ctx = Ext.getCmp('modx-resource-context-key').getValue();
			var pa = Ext.getCmp('modx-resource-parent-hidden').getValue();
			var pao = Ext.getCmp('modx-resource-parent-old-hidden').getValue();
			var v = ctx+'_'+pa;

			if (pa !== pao) {
				t.refresh();
				Ext.getCmp('modx-resource-parent-old-hidden').setValue(pa);
			}
			else {
				var n = t.getNodeById(v);
				if (n) {
					n.leaf = false;
					t.refreshNode(v,true);
				}
			}
		}

		var action = 'resource/update';
		var page = MODx.action ? MODx.action[action] : action;

		if ((o.result.object.class_key != this.defaultClassKey) || (o.result.object.parent != this.defaultValues.parent) || (o.result.object.richtext != this.defaultValues.richtext)) {
			MODx.loadPage(page, 'id='+o.result.object.id);
		} else {
			this.getForm().setValues(o.result.object);
			Ext.getCmp('modx-page-update-resource').config.preview_url = o.result.object.preview_url;
		}
	}

});
Ext.reg('minishop2-panel-product',miniShop2.panel.Product);