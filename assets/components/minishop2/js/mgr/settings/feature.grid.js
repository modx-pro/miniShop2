miniShop2.grid.Feature = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{description}</p>')
		,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});

	this.dd = function(grid) {
		this.dropTarget = new Ext.dd.DropTarget(grid.container, {
			ddGroup : 'dd',
			copy:false,
			notifyDrop : function(dd, e, data) {
				var store = grid.store.data.items;
				var target = store[dd.getDragData(e).rowIndex].id;
				var source = store[data.rowIndex].id;
				if (target != source) {
					dd.el.mask(_('loading'),'x-mask-loading');
					MODx.Ajax.request({
						url: miniShop2.config.connector_url
						,params: {
							action: config.action || 'mgr/settings/feature/sort'
							,source: source
							,target: target
						}
						,listeners: {
							success: {fn:function(r) {dd.el.unmask();grid.refresh();},scope:grid}
							,failure: {fn:function(r) {dd.el.unmask();},scope:grid}
						}
					});
				}
			}
		});
	};

	Ext.applyIf(config,{
		id: 'minishop2-grid-feature'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/feature/getlist'
		}
		,fields: ['id','name','caption','type','rank']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/feature/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ms2_ft_name'),dataIndex: 'name',width: 150, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_ft_caption'),dataIndex: 'caption',width: 150, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_ft_type'),dataIndex: 'type',width: 100, editor: {xtype: 'minishop2-combo-feature-types'}}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createFeature
			,scope: this
		},{
			text: _('ms2_btn_copy')
			,handler: this.copyFeature
			,scope: this
		},'->',{
			xtype: 'textfield'
			,name: 'query'
			,width: 200
			,id: 'minishop2-features-search'
			,emptyText: _('ms2_search')
			,listeners: {
				render: {fn:function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER,function() {this.FilterByQuery(tf);},this);},scope:this}
			}
		},{
			xtype: 'button'
			,id: 'minishop2-features-clear'
			,text: '<i class="'+ (MODx.modx23 ? 'icon icon-times' : 'bicon-remove-sign') + '"></i>'/* + _('ms2_search_clear')*/
			,listeners: {
				click: {fn: this.clearFilter, scope: this}
			}
		}]
		,ddGroup: 'dd'
		,enableDragDrop: true
		,listeners: {render: {fn: this.dd, scope: this}}
	});
	miniShop2.grid.Feature.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Feature,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateFeature
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeFeature
		});
		this.addContextMenuItem(m);
	}

	,renderLogo: function(value) {
		if (/(jpg|png|gif|jpeg)$/i.test(value)) {
			if (!/^\//.test(value)) {value = '/' + value;}
			return '<img src="'+value+'" height="35" />';
		}
		else {
			return '';
		}
	}

	,renderType: function(value) {
		return _('ms2_feature_'+value);
	}

	,createFeature: function(btn,e) {
		if (!this.windows.createFeature) {
			this.windows.createFeature = MODx.load({
				xtype: 'minishop2-window-feature-create'
				,fields: this.getFeatureFields('create')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}else{
			var tree = Ext.getCmp('minishop2-tree-modal-categories-window-create');
			tree.getLoader().load(tree.root);
		}

		this.windows.createFeature.fp.getForm().reset();
		this.windows.createFeature.show(e.target);
	}

	,copyFeature: function(btn,e){


	}

	,updateFeature: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		if (!this.windows.updateFeature) {
			this.windows.updateFeature = MODx.load({
				xtype: 'minishop2-window-feature-update'
				,record: r
				,fields: this.getFeatureFields('update')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}else{
			var tree = Ext.getCmp('minishop2-tree-modal-categories-window-update');
			tree.getLoader().load(tree.root);
		}

		this.windows.updateFeature.fp.getForm().reset();
		this.windows.updateFeature.fp.getForm().setValues(r);
		this.windows.updateFeature.show(e.target);
	}

	,removeFeature: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
			,text: _('ms2_menu_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/feature/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) {this.refresh();}, scope:this}
			}
		});
	}

	,getFeatureFields: function(type) {
		return [{
			layout:'column',
			items: [{
				xtype: 'minishop2-tree-modal-categories',
				id: 'minishop2-tree-modal-categories-window-'+type,
				baseParams: {
					action: 'mgr/category/getnodes'
					,type: type
					,currentResource: MODx.request.id || 0
					,currentAction: MODx.request.a || 0
				},
				columnWidth: .40
			},{
				layout: 'form',
				columnWidth: .60,
				labelWidth: 120,
				items: [
					{xtype: 'hidden',name: 'id', id: 'minishop2-feature-id-'+type}
					,{xtype: 'hidden',name: 'categories', id: 'minishop2-feature-categories-'+type}
					,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'name', allowBlank: false, anchor: '99%', id: 'minishop2-feature-name-'+type}
					,{xtype: 'textfield',fieldLabel: _('ms2_caption'), name: 'caption', allowBlank: false, anchor: '99%', id: 'minishop2-feature-caption-'+type}
					,{xtype: 'minishop2-combo-feature-types', anchor: '99%', id: 'minishop2-combo-feature-types'+type}
					,{xtype: 'checkboxgroup'
						,fieldLabel: _('ms2_options')
						,columns: 1
						,items: [
							{xtype: 'xcheckbox', boxLabel: _('ms2_active'), name: 'active', id: 'minishop2-feature-active-'+type}
							,{xtype: 'xcheckbox', boxLabel: _('ms2_required'), name: 'required', id: 'minishop2-feature-required-'+type}
						]
					}
				]
			}]
		}];
	}

	,FilterByQuery: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function(btn,e) {
		var s = this.getStore();
		s.baseParams.query = '';
		Ext.getCmp('minishop2-features-search').setValue('');
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

});
Ext.reg('minishop2-grid-feature',miniShop2.grid.Feature);




miniShop2.window.CreateFeature = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_create')
		,id: this.ident
		,width: 600
		,minHeight: 500
		,autoHeight: true
		,labelAlign: 'left'
		,labelWidth: 180
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/feature/create'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreateFeature.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateFeature,MODx.Window);
Ext.reg('minishop2-window-feature-create',miniShop2.window.CreateFeature);


miniShop2.window.UpdateFeature = function(config) {
	config = config || {};
	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_update')
		,id: this.ident
		,width: 600
		,autoHeight: true
		,labelAlign: 'left'
		,labelWidth: 180
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/feature/update'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateFeature.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateFeature,MODx.Window);
Ext.reg('minishop2-window-feature-update',miniShop2.window.UpdateFeature);

miniShop2.tree.ModalCategories = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		url: miniShop2.config.connector_url
		,id: 'minishop2-modal-categories-tree'
		,title: ''
		,anchor: '100%'
		,rootVisible: false
		,expandFirst: true
		,enableDD: false
		,ddGroup: 'modx-treedrop-dd'
		,remoteToolbar: false
		,action: 'mgr/settings/feature/getcategorynodes'
		,tbarCfg: {id: config.id ? config.id+'-tbar' : 'modx-tree-resource-tbar'}
		,baseParams: {
			action: 'mgr/settings/feature/getcategorynodes'
			,currentResource: MODx.request.id || 0
			,currentAction: MODx.request.a || 0
		}
		//,tbar: []
		,listeners: {
			checkchange: function(node, checked) {
				this.mask.show();
				var type = this.baseParams.type;
				var categories = Ext.getCmp('minishop2-feature-categories-'+type);
				if(categories.getValue() == ''){
					var categoriesList = [];
				}else{
					var categoriesList = Ext.decode(categories.getValue());
				}

				var index = categoriesList.indexOf(node.attributes.pk);

				if(index > -1){
					categoriesList.splice(index, 1);
				}else{
					categoriesList.push(node.attributes.pk);
				}

				categories.setValue(Ext.encode(categoriesList));
				console.log(categories.getValue());
				this.mask.hide();
			}
			,afterrender: function() {
				this.mask = new Ext.LoadMask(this.getEl());
			}
		}
	});
	miniShop2.tree.ModalCategories.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.tree.ModalCategories, MODx.tree.Tree,{

	_showContextMenu: function(n,e) {
		n.select();
		this.cm.activeNode = n;
		this.cm.removeAll();
		var m = [];
		m.push({text: _('directory_refresh'),handler: function() {this.refreshNode(this.cm.activeNode.id,true);}});
		this.addContextMenuItem(m);
		this.cm.showAt(e.xy);
		e.stopEvent();
	}

});
Ext.reg('minishop2-tree-modal-categories',miniShop2.tree.ModalCategories);