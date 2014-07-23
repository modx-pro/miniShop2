miniShop2.page.UpdateCategory = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'minishop2-panel-category-update'
		,actions: {
			'new': MODx.action ? MODx.action['resource/create'] : 'resource/create'
			,edit: MODx.action ? MODx.action['resource/update'] : 'resource/update'
			,preview: MODx.action ? MODx.action['resource/preview'] : 'resource/preview'
		}
	});
	miniShop2.page.UpdateCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.UpdateCategory,MODx.page.UpdateResource, {

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
			//,cls: ''
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
			,handler: this.duplicateResource
			,scope: this
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
		var key = this.record.context_key+'_'+ this.record.id;
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

					var tree = Ext.getCmp('modx-resource-tree');
					if (tree) {
						tree.refreshNode(key, false);
					}
				},scope:this}
			}
		});
	}

	,unpublishProduct: function(btn,e) {
		var key = this.record.context_key+'_'+ this.record.id;
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

					var tree = Ext.getCmp('modx-resource-tree');
					if (tree) {
						tree.refreshNode(key, false);
					}
				},scope:this}
			}
		});
	}

	,deleteProduct: function(btn,e) {
		var key = this.record.context_key+'_'+ this.record.id;
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

					var tree = Ext.getCmp('modx-resource-tree');
					if (tree) {
						tree.refreshNode(key, false);
					}
				},scope:this}
			}
		});
	}

	,undeleteProduct: function(btn,e) {
		var key = this.record.context_key+'_'+ this.record.id;
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

					var tree = Ext.getCmp('modx-resource-tree');
					if (tree) {
						tree.refreshNode(key, false);
					}
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
		MODx.loadPage(upPage,'id=' + id)
	}

});
Ext.reg('minishop2-page-category-update',miniShop2.page.UpdateCategory);


miniShop2.panel.UpdateCategory = function(config) {
	config = config || {};
	miniShop2.panel.UpdateCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.UpdateCategory,MODx.panel.Resource,{

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
			,stateful: MODx.config.ms2_category_remember_tabs == true
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
			,mode: "update"
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
		}
		return true;
	}

});
Ext.reg('minishop2-panel-category-update',miniShop2.panel.UpdateCategory);