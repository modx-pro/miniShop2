miniShop2.grid.Option = function(config) {
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
							action: config.action || 'mgr/settings/option/sort'
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
		id: 'minishop2-grid-option'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/option/getlist'
		}
		,fields: ['id','key','caption','type','rank',{
            name: 'categories'
            ,convert: function(val,row) {
                var cat = [];
                Ext.each(val, function(v){
                    cat.push(v.id);
                });
                return Ext.encode(cat);
            }
        }]
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/option/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ms2_ft_name'),dataIndex: 'key',width: 150, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_ft_caption'),dataIndex: 'caption',width: 150, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_ft_type'),dataIndex: 'type',width: 100, editor: {xtype: 'minishop2-combo-option-types'}, renderer:function(v){return _('ms2_ft_'+v)}}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createOption
			,scope: this
		},{
            text: _('ms2_btn_addoption')
            ,handler: this.addOption
            ,scope: this
        },{
			text: _('ms2_btn_copy')
			,handler: this.copyOption
			,scope: this
		},'->',{
			xtype: 'textfield'
			,name: 'query'
			,width: 200
			,id: 'minishop2-options-search'
			,emptyText: _('ms2_search')
			,listeners: {
				render: {fn:function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER,function() {this.FilterByQuery(tf);},this);},scope:this}
			}
		},{
			xtype: 'button'
			,id: 'minishop2-options-clear'
			,text: '<i class="'+ (MODx.modx23 ? 'icon icon-times' : 'bicon-remove-sign') + '"></i>'/* + _('ms2_search_clear')*/
			,listeners: {
				click: {fn: this.clearFilter, scope: this}
			}
		}]
		,ddGroup: 'dd'
		,enableDragDrop: true
		,listeners: {render: {fn: this.dd, scope: this}}
	});
	miniShop2.grid.Option.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Option,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateOption
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeOption
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
		return _('ms2_option_'+value);
	}

	,createOption: function(btn,e) {
		if (!this.windows.createOption) {
			this.windows.createOption = MODx.load({
				xtype: 'minishop2-window-option-create'
				,fields: this.getOptionFields('create')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}else{
			var tree = Ext.getCmp('minishop2-tree-modal-categories-window-create');
			tree.getLoader().load(tree.root);
		}

		this.windows.createOption.fp.getForm().reset();
		this.windows.createOption.show(e.target);
	}

    ,addOption: function(btn,e) {
        if (!this.windows.addOption) {
            this.windows.addOption = MODx.load({
                xtype: 'minishop2-window-option-add'
                ,listeners: {
                    success: {fn:function() { this.refresh(); },scope:this}
                }
            });
        }

        var f = this.windows.addOption.fp.getForm();
        f.reset();
        f.setValues({category_id: MODx.request.id});
        this.windows.addOption.show(e.target);
    }

	,copyOption: function(btn,e){


	}

    ,reloadTree: function(option){
        var tree = Ext.getCmp('minishop2-tree-modal-categories-window-update');
        tree.enable();
        tree.getLoader().baseParams.option = option;
        tree.refresh()
    }

	,updateOption: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		if (!this.windows.updateOption) {
			this.windows.updateOption = MODx.load({
				xtype: 'minishop2-window-option-update'
				,record: r
				,fields: this.getOptionFields('update')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}

        this.reloadTree(r.id);

		this.windows.updateOption.fp.getForm().reset();
		this.windows.updateOption.fp.getForm().setValues(r);
		this.windows.updateOption.show(e.target);
	}

	,removeOption: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
			,text: _('ms2_menu_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/option/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) {this.refresh();}, scope:this}
			}
		});
	}

	,getOptionFields: function(type) {
		return [{
			layout:'column',
			items: [{
				xtype: 'minishop2-tree-modal-categories',
				id: 'minishop2-tree-modal-categories-window-'+type,
				baseParams: {
					action: 'settings/option/getcategorynodes'
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
					{xtype: 'hidden',name: 'id', id: 'minishop2-option-id-'+type}
					,{xtype: 'hidden',name: 'categories', id: 'minishop2-option-categories-'+type}
					,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'key', allowBlank: false, anchor: '99%', id: 'minishop2-option-name-'+type}
					,{xtype: 'textfield',fieldLabel: _('ms2_caption'), name: 'caption', allowBlank: false, anchor: '99%', id: 'minishop2-option-caption-'+type}
					,{xtype: 'minishop2-combo-option-types', anchor: '99%', id: 'minishop2-combo-option-types'+type}
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
		Ext.getCmp('minishop2-options-search').setValue('');
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

});
Ext.reg('minishop2-grid-option',miniShop2.grid.Option);




miniShop2.window.CreateOption = function(config) {
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
		,action: 'mgr/settings/option/create'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreateOption.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateOption,MODx.Window);
Ext.reg('minishop2-window-option-create',miniShop2.window.CreateOption);


miniShop2.window.UpdateOption = function(config) {
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
		,action: 'mgr/settings/option/update'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateOption.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateOption,MODx.Window);
Ext.reg('minishop2-window-option-update',miniShop2.window.UpdateOption);

miniShop2.window.AddOption = function(config) {
    config = config || {};
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('ms2_category_option_add')
        ,id: this.ident
        ,width: 600
        ,autoHeight: true
        ,labelAlign: 'left'
        ,labelWidth: 180
        ,url: miniShop2.config.connector_url
        ,action: 'mgr/settings/option/add'
        ,fields: [
            {xtype: 'hidden',name: 'category_id', id: 'minishop2-option-category'}
            ,{xtype: 'minishop2-combo-extra-options', anchor: '99%', id: 'minishop2-combo-extra-options', name: 'option_id', hiddenName: 'option_id'}
            ,{xtype: 'numberfield', width: 50, id: 'minishop2-option-rank', name: 'rank', fieldLabel: _('ms2_category_option_rank'), allowDecimals:false, allowNegative: false}
            ,{xtype: 'checkboxgroup'
                ,fieldLabel: _('ms2_options')
                ,columns: 1
                ,items: [
                    {xtype: 'xcheckbox', boxLabel: _('ms2_active'), name: 'active', id: 'minishop2-option-active'}
                    ,{xtype: 'xcheckbox', boxLabel: _('ms2_required'), name: 'required', id: 'minishop2-option-required'}
                ]
            }
        ]
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    miniShop2.window.AddOption.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.AddOption,MODx.Window);
Ext.reg('minishop2-window-option-add',miniShop2.window.AddOption);

miniShop2.tree.ModalCategories = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		url: miniShop2.config.connector_url
		,id: 'minishop2-modal-categories-tree'
		,title: ''
		,anchor: '100%'
		,rootVisible: false
        ,autoLoad: false
		,expandFirst: true
		,enableDD: false
		,ddGroup: 'modx-treedrop-dd'
		,remoteToolbar: false
		,action: 'mgr/settings/option/getcategorynodes'
		,tbarCfg: {id: config.id ? config.id+'-tbar' : 'modx-tree-resource-tbar'}
		//,tbar: []
		,listeners: {
			checkchange: function(node, checked) {
				this.mask.show();
				var type = this.baseParams.type;
				var categories = Ext.getCmp('minishop2-option-categories-'+type);
				if(categories.getValue() == ''){
					var categoriesList = [];
				}else{
					var categoriesList = Ext.decode(categories.getValue());
				}

				var index = categoriesList.indexOf(node.attributes.pk);

				if(index > -1){
					if (!checked) { categoriesList.splice(index, 1); }
				}else{
					if (checked) { categoriesList.push(node.attributes.pk); }
				}

				categories.setValue(Ext.encode(categoriesList));
				console.log(index, categoriesList, checked, categories, categories.getValue());
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