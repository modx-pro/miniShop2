miniShop2.grid.Option = function(config) {
	config = config || {};

    this.sm = new Ext.grid.CheckboxSelectionModel();

	Ext.applyIf(config,{
		id: 'minishop2-grid-option'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/option/getlist'
		}
		,fields: ['id','key','caption','description','measure_unit','category','type','properties','rank',{
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
        ,sm: this.sm
        ,columns: [this.sm
			,{header: _('id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ms2_ft_name'),dataIndex: 'key',width: 150, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_ft_caption'),dataIndex: 'caption',width: 150, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_ft_type'),dataIndex: 'type',width: 100, editor: {xtype: 'minishop2-combo-option-types'}, sortable: true, renderer:function(v){return _('ms2_ft_'+v)}}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createOption
			,scope: this
		},{
            text: '<i class="'+ (MODx.modx23 ? 'icon icon-list' : 'bicon-list') + '"></i> ' + _('ms2_bulk_actions')
            ,menu: [
                {text: _('ms2_ft_selected_assign'),handler: this.assignSelected,scope: this}
                ,'-'
                ,{text: _('ms2_menu_remove'),handler: this.removeSelected,scope: this}
            ]
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

	,createOption: function(btn,e) {
        delete this.menu.record;
        var w = Ext.getCmp('minishop2-window-option-create');
        if (w) {w.hide().getEl().remove();}

        this.windows.createOption = MODx.load({
            xtype: 'minishop2-window-option-create'
            ,id: 'minishop2-window-option-create'
            ,fields: this.getOptionFields('create')
            ,listeners: {
                success: {fn:function() { this.refresh(); },scope:this}
            }
        });

        var f = this.windows.createOption.fp.getForm();
        f.reset();
        f.setValues({type: 'textfield'});
        this.windows.createOption.show(e.target);
	}

   	,copyOption: function(btn,e){


	}

    ,assignSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        if (!this.windows.assignOptions) {
            this.windows.assignOptions = MODx.load({
                xtype: 'minishop2-window-options-assign'
                ,listeners: {
                    success: {fn:function() { this.refresh(); },scope:this}
                }
            });
        }

        var f = this.windows.assignOptions.fp.getForm();
        f.reset();
        f.setValues({options: cs});
        this.windows.assignOptions.show(e.target);
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

        var w = Ext.getCmp('minishop2-window-option-update');
        if (w) {w.hide().getEl().remove();}

        this.windows.updateOption = MODx.load({
            xtype: 'minishop2-window-option-update'
            ,id: 'minishop2-window-option-update'
            ,record: r
            ,fields: this.getOptionFields('update')
            ,listeners: {
                success: {fn:function() { this.refresh(); },scope:this}
            }
        });

        this.reloadTree(r.id);
		this.windows.updateOption.fp.getForm().reset();
		this.windows.updateOption.fp.getForm().setValues(r);
		this.windows.updateOption.show(e.target);
	}

	,removeOption: function(btn,e) {
		if (!this.menu.record) return false;
		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.key + '"'
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

    ,removeSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.msg.confirm({
            title: _('ms2_menu_remove_multiple')
            ,text: _('ms2_options_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/settings/option/remove_multiple'
                ,ids: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });

        return true;
    }

	,getOptionFields: function(type) {
        var propPanel = 'minishop2-option-properties-'+type;
		return [{
			layout:'column',
			items: [{
				xtype: 'minishop2-tree-modal-categories',
				id: 'minishop2-tree-modal-categories-window-'+type,
                categories: 'minishop2-option-categories-'+type,
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
					,{xtype: 'textfield',fieldLabel: _('ms2_ft_name'), name: 'key', allowBlank: false,
                        anchor: '99%', id: 'minishop2-option-name-'+type}
					,{xtype: 'textfield',fieldLabel: _('ms2_ft_caption'), name: 'caption', allowBlank: false,
                        anchor: '99%', id: 'minishop2-option-caption-'+type}
                    ,{xtype: 'textarea',fieldLabel: _('ms2_ft_description'), name: 'description', allowBlank: true,
                        anchor: '99%', id: 'minishop2-option-description-'+type}
                    ,{xtype: 'textfield',fieldLabel: _('ms2_ft_measure_unit'), name: 'measure_unit', allowBlank: true,
                        anchor: '99%', id: 'minishop2-option-measure_unit-'+type}
                    ,{xtype: 'modx-combo-category',fieldLabel: _('ms2_ft_group'), name: 'category', allowBlank: true,
                        anchor: '99%', id: 'minishop2-option-category-'+type,pageSize: parseInt(MODx.config.default_per_page)}
					,{xtype: 'minishop2-combo-option-types', anchor: '99%', id: 'minishop2-combo-option-types-'+type
                        ,propertiesPanel: propPanel
                        ,listeners: {
                            select: {fn:this.onSelectType, scope: this}
                            ,afterrender: {fn:function(c) {
                                if (!this.menu.record) return;
                                var record = this.menu.record;
                                c.store.on('load', function(){
                                    var xtype = c.findRecord('name', record.type).data.xtype;
                                    if (!xtype) {
                                        return;
                                    }
                                    MODx.load({
                                        xtype: xtype
                                        ,renderTo: c.propertiesPanel
                                        ,record: record
                                        ,name: 'properties'
                                    });
                                });
                            }, scope: this}
                        }
                    }
                    ,{xtype: 'panel', id: propPanel, cls:'main-wrapper', comboTypes: 'minishop2-combo-option-types-'+type
                    }
				]
			}]
		}];
	}

    ,clearProperties: function(panel) {
        panel = Ext.getCmp(panel);
        panel.getEl().update('');
    }

    ,onSelectType: function(combo,record,index) {
        this.clearProperties(combo.propertiesPanel);

        if (!record) return;

        var xtype = record.data.xtype;
        if (!xtype) {
            return;
        }

        MODx.load({
            xtype: xtype
            ,renderTo: combo.propertiesPanel
            ,name: 'properties'
        });

    }

	,FilterByQuery: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
	}

	,clearFilter: function(btn,e) {
		var s = this.getStore();
		s.baseParams.query = '';
		Ext.getCmp('minishop2-options-search').setValue('');
		this.getBottomToolbar().changePage(1);
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
                var checkedNodes = this.getChecked();
                var categories = [];
                for (var i = 0; i < checkedNodes.length; i++) {
                    categories.push(checkedNodes[i].attributes.pk);
                }

                var catField = Ext.getCmp(this.categories);
                if (!catField) return false;
                catField.setValue(Ext.util.JSON.encode(categories));
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

miniShop2.window.AssignOptions = function(config) {
    config = config || {};
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('ms2_category_options_assign')
        ,id: this.ident
        ,width: 600
        ,autoHeight: true
        ,labelAlign: 'left'
        ,labelWidth: 180
        ,url: miniShop2.config.connector_url
        ,action: 'mgr/settings/option/add_multiple'
        ,fields: [{
            xtype: 'minishop2-tree-modal-categories',
            id: 'minishop2-tree-modal-options-assign-window',
            categories: 'minishop2-categories-ids',
            baseParams: {
                action: 'settings/option/getcategorynodes'
                , currentResource: MODx.request.id || 0
                , currentAction: MODx.request.a || 0
            }
        },{
            xtype: 'hidden',name: 'options', id: 'minishop2-options-ids'
        },{
            xtype: 'hidden',name: 'categories', id: 'minishop2-categories-ids'
        }]
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    miniShop2.window.AssignOptions.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.AssignOptions,MODx.Window);
Ext.reg('minishop2-window-options-assign',miniShop2.window.AssignOptions);