miniShop2.page.UpdateProduct = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-product'
	});
	miniShop2.page.UpdateProduct.superclass.constructor.call(this,config);
};

Ext.extend(miniShop2.page.UpdateProduct,MODx.page.UpdateResource,{

	getButtons: function(cfg) {
		var btns = [];

		if (cfg.canSave == 1) {
			btns.push({
				process: MODx.modx23 ? 'resource/update' : 'update'
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
		} else if (cfg.locked) {
			btns.push({
				text: cfg.lockedText || _('locked')
				,handler: Ext.emptyFn
				,disabled: true
			});
			btns.push('-');
		}

		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-power-off' : 'bicon-off') + '"></i> ' + _('ms2_btn_publish')
			,id: 'minishop2-panel-btn-publish'
			,handler: this.publishProduct
			,hidden: !cfg.canPublish || cfg.record.published
			,disabled: cfg.locked
			,scope: this
			,cls: 'btn-orange'
		});
		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-power-off' : 'bicon-off') + '"></i> ' + _('ms2_btn_unpublish')
			,id: 'minishop2-panel-btn-unpublish'
			,handler: this.unpublishProduct
			,hidden: !cfg.canPublish || !cfg.record.published
			,disabled: cfg.locked
			,scope: this
		});
		btns.push('-');

		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-trash-o' : 'bicon-trash') + '"></i> ' + _('ms2_btn_delete')
			,id: 'minishop2-panel-btn-delete'
			,handler: this.deleteProduct
			,hidden: !cfg.canDelete || cfg.record.deleted
			,disabled: cfg.locked
			,scope: this
			,cls: 'btn-brown'
		});
		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-trash-o' : 'bicon-trash') + '"></i> ' + _('ms2_btn_undelete')
			,id: 'minishop2-panel-btn-undelete'
			,handler: this.undeleteProduct
			,hidden: !cfg.canDelete || !cfg.record.deleted
			,disabled: cfg.locked
			,scope: this
			,cls: 'btn-green'
		});
		btns.push('-');

		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-eye' : 'bicon-eye-open') + '"></i> ' + _('ms2_btn_view')
			,handler: this.preview
			,scope: this
		});
		btns.push('-');

		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-copy' : 'bicon-file') + '"></i>'
			,handler: this.duplicateProduct
			,scope: this
			,hidden: !cfg.canSave
			,tooltip: _('ms2_btn_duplicate')
		});
		btns.push('-');

		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-arrow-left' : 'bicon-arrow-left') + '"></i>'
			,handler: this.prevPage
			,disabled: !cfg.prev_page ? 1 : 0
			,scope: this
			,tooltip: _('ms2_btn_prev')
			,keys: [{key: 37,alt: true, scope: this, fn: this.prevPage}]
		});
		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-arrow-up' : 'bicon-arrow-up') + '"></i>'
			,handler: this.upPage
			,scope: this
			,tooltip: _('ms2_btn_back')
			,keys: [{key: 38,alt: true, scope: this, fn: this.upPage}]
		});
		btns.push({
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-arrow-right' : 'bicon-arrow-right') + '"></i>'
			,handler: this.nextPage
			,disabled: !cfg.next_page ? 1 : 0
			,scope: this
			,tooltip: _('ms2_btn_next')
			,keys: [{key: 39,alt: true, scope: this, fn: this.nextPage}]
		});

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
					var pb = Ext.getCmp('modx-resource-publishedby');
					if (pb) {pb.setValue(r.object.publishedby);}

					this.refreshNode();
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
					var pb = Ext.getCmp('modx-resource-publishedby');
					if (pb) {pb.setValue(0);}

					this.refreshNode();
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
					var db = Ext.getCmp('modx-resource-deletedby');
					if (db) {db.setValue(r.object.deletedby);}

					this.refreshNode();
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
					var db = Ext.getCmp('modx-resource-deletedby');
					if (db) {db.setValue(0);}

					this.refreshNode();
				},scope:this}
			}
		});
	}

	,duplicateProduct: function(btn,e) {
		MODx.msg.confirm({
			url: MODx.modx23 ? MODx.config.connector_url : MODx.config.connectors_url + 'resource/index.php'
			,text: _('ms2_product_duplicate_confirm')
			,params: {
				action: MODx.modx23 ? 'resource/duplicate' : 'duplicate'
				,id: this.record.id
			}
			,listeners: {
				success: {fn:function(response) {
					var page = MODx.action ? MODx.action['resource/update'] : 'resource/update';
					MODx.loadPage(page, 'id='+response.object.id);
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
			MODx.loadPage(updatePage, 'id=' + id)
		}
	}

	,nextPage: function(btn,e) {
		if (this.next_page > 0) {
			var updatePage = MODx.action ? MODx.action['resource/update'] : 'resource/update';
			var id = this.next_page;

			MODx.releaseLock(MODx.request.id);
			MODx.sleep(400);
			MODx.loadPage(updatePage, 'id=' + id)
		}
	}

	,upPage: function(btn,e) {
		var id = this.up_page;
		if (id != 0) {var upPage = MODx.action ? MODx.action['resource/update'] : 'resource/update';}
		else {var upPage = MODx.action['welcome'];}

		MODx.releaseLock(MODx.request.id);
		MODx.sleep(400);
		MODx.loadPage(upPage, 'id=' + id)
	}

	,refreshNode: function() {
		var t = Ext.getCmp('modx-resource-tree');

		if (t) {
			var ctx = Ext.getCmp('modx-resource-context-key').getValue();
			var pa = Ext.getCmp('modx-resource-parent-hidden').getValue();
			var v = ctx+'_'+pa;

			var n = t.getNodeById(v);
			if (n) {
				n.leaf = false;
				t.refreshNode(v,true);
			}
		}
	}

});
Ext.reg('minishop2-page-product-update',miniShop2.page.UpdateProduct);



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

	,getComments: function(config) {
		return [{
			xtype: 'tickets-tab-comments'
			,record: config.record
			,parents: config.record.id
			,layout: 'form'
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