miniShop2.grid.CategoryFeature = function(config) {
	config = config || {};

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

    this.sm = new Ext.grid.CheckboxSelectionModel();

	Ext.applyIf(config,{
		id: 'minishop2-grid-category-feature'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/feature/getlist'
            ,category: MODx.request.id
		}
		,fields: ['id','name','caption','type','active','required','rank','category_id']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/feature/updatefromgrid'
		,autosave: true
        ,sm: this.sm
		,columns: [this.sm
			,{header: _('id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ms2_ft_name'),dataIndex: 'name',width: 150, sortable: true}
			,{header: _('ms2_ft_caption'),dataIndex: 'caption',width: 150, sortable: true}
			,{header: _('ms2_ft_type'),dataIndex: 'type',width: 100}
            ,{header: _('ms2_ft_required'),dataIndex: 'required',width: 100, editor: {xtype:'combo-boolean', renderer:'boolean'}}
            ,{header: _('ms2_ft_active'),dataIndex: 'active',width: 100, editor: {xtype:'combo-boolean', renderer:'boolean'}}
            ,{header: _('ms2_ft_rank'),dataIndex: 'rank',width: 100, editor: {xtype: 'numberfield'}}
		]
		,tbar: [{
            text: _('ms2_btn_addfeature')
            ,handler: this.addFeature
            ,scope: this
        },{
			text: _('ms2_btn_copy')
			,handler: this.copyCategory
			,scope: this
		},{
            text: '<i class="'+ (MODx.modx23 ? 'icon icon-list' : 'bicon-list') + '"></i> ' + _('ms2_bulk_actions')
            ,menu: [
                {text: _('ms2_ft_selected_remove'),handler: this.removeSelected,scope: this}
                ,'-'
                ,{text: _('ms2_ft_selected_activate'),handler: this.activateSelected,scope: this}
                ,{text: _('ms2_ft_selected_deactivate'),handler: this.deactivateSelected,scope: this}
                ,'-'
                ,{text: _('ms2_ft_selected_require'),handler: this.requireSelected,scope: this}
                ,{text: _('ms2_ft_selected_unrequire'),handler: this.unrequireSelected,scope: this}
            ]
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
	miniShop2.grid.CategoryFeature.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.CategoryFeature,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [
            {text: _('ms2_ft_selected_remove'),handler: this.removeSelected,scope: this}
            ,'-'
            ,{text: _('ms2_ft_selected_activate'),handler: this.activateSelected,scope: this}
            ,{text: _('ms2_ft_selected_deactivate'),handler: this.deactivateSelected,scope: this}
            ,'-'
            ,{text: _('ms2_ft_selected_require'),handler: this.requireSelected,scope: this}
            ,{text: _('ms2_ft_selected_unrequire'),handler: this.unrequireSelected,scope: this}
        ];

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

	,addFeature: function(btn,e) {
        if (!this.windows.addFeature) {
            this.windows.addFeature = MODx.load({
                xtype: 'minishop2-window-feature-add'
                ,listeners: {
                    success: {fn:function() { this.refresh(); },scope:this}
                }
            });
        }

        var f = this.windows.addFeature.fp.getForm();
        f.reset();
        f.setValues({category_id: MODx.request.id});
        this.windows.addFeature.show(e.target);
    }

	,copyCategory: function(btn,e){
        if (!this.windows.copyCategory) {
            this.windows.copyCategory = MODx.load({
                xtype: 'minishop2-window-copy-category'
                ,listeners: {
                    success: {fn:function() { this.refresh(); },scope:this}
                }
            });
        }

        var f = this.windows.copyCategory.fp.getForm();
        f.reset();
        f.setValues({category_to: MODx.request.id});
        this.windows.copyCategory.show(e.target);
	}

    ,removeSelected: function(btn,e) {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        for (var i=0;i<sels.length;i++) {
            console.log(sels[i].data);
            MODx.Ajax.request({
                url: miniShop2.config.connector_url
                ,params: {
                    action: 'mgr/settings/feature/delete'
                    ,id: sels[i].data['id']
                    ,category_id: sels[i].data['category_id']
                }
                ,listeners: {
                    'success': {fn:function(r) {
                        this.getSelectionModel().clearSelections(true);
                        this.refresh();
                    },scope:this}
                }
            });
        }

        return true;
    }

    ,updateSelected: function(params) {
        params['action'] = 'mgr/settings/feature/category_update';
        MODx.Ajax.request({
            url: miniShop2.config.connector_url
            ,params: params
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
    }

    ,activateSelected: function(btn,e) {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        for (var i=0;i<sels.length;i++) {
            if (sels[i].data['active'] == 0) {
                sels[i].data['active'] = 1;
                this.updateSelected(sels[i].data)
            }
        }

        return true;
    }

    ,deactivateSelected: function(btn,e) {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        for (var i=0;i<sels.length;i++) {
            if (sels[i].data['active'] == 1) {
                sels[i].data['active'] = 0;
                this.updateSelected(sels[i].data)
            }
        }

        return true;
    }

    ,requireSelected: function(btn,e) {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        for (var i=0;i<sels.length;i++) {
            if (sels[i].data['required'] == 0) {
                sels[i].data['required'] = 1;
                this.updateSelected(sels[i].data)
            }
        }

        return true;
    }

    ,unrequireSelected: function(btn,e) {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        for (var i=0;i<sels.length;i++) {
            if (sels[i].data['required'] == 1) {
                sels[i].data['required'] = 0;
                this.updateSelected(sels[i].data)
            }
        }

        return true;
    }

	,getFeatureFields: function(type) {
		return [{
			layout:'column',
			items: [{
				xtype: 'minishop2-tree-modal-categories',
				id: 'minishop2-tree-modal-categories-window-'+type,
				baseParams: {
					action: 'mgr/category/getcategorynodes'
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
Ext.reg('minishop2-grid-category-feature',miniShop2.grid.CategoryFeature);

miniShop2.window.AddFeature = function(config) {
    config = config || {};
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('ms2_category_feature_add')
        ,id: this.ident
        ,width: 600
        ,autoHeight: true
        ,labelAlign: 'left'
        ,labelWidth: 180
        ,url: miniShop2.config.connector_url
        ,action: 'mgr/settings/feature/add'
        ,fields: [
            {xtype: 'hidden',name: 'category_id', id: 'minishop2-feature-category'}
            ,{xtype: 'minishop2-combo-features', anchor: '99%', id: 'minishop2-combo-features', name: 'feature_id', hiddenName: 'feature_id'}
            ,{xtype: 'numberfield', width: 50, id: 'minishop2-feature-rank', name: 'rank', fieldLabel: _('ms2_category_feature_rank'), allowDecimals:false, allowNegative: false}
            ,{xtype: 'checkboxgroup'
                ,fieldLabel: _('ms2_options')
                ,columns: 1
                ,items: [
                    {xtype: 'xcheckbox', boxLabel: _('ms2_active'), name: 'active', id: 'minishop2-feature-active'}
                    ,{xtype: 'xcheckbox', boxLabel: _('ms2_required'), name: 'required', id: 'minishop2-feature-required'}
                ]
            }
        ]
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    miniShop2.window.AddFeature.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.AddFeature,MODx.Window);
Ext.reg('minishop2-window-feature-add',miniShop2.window.AddFeature);

miniShop2.window.CopyCategory = function(config) {
    config = config || {};
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('ms2_category_feature_copy')
        ,id: this.ident
        ,width: 600
        ,autoHeight: true
        ,labelAlign: 'left'
        ,labelWidth: 180
        ,url: miniShop2.config.connector_url
        ,action: 'mgr/settings/feature/category_duplicate'
        ,fields: [
            {xtype: 'hidden',name: 'category_to', id: 'minishop2-feature-category'}
            ,{xtype: 'minishop2-combo-categories', anchor: '99%', id: 'minishop2-combo-categories', name: 'category_from', hiddenName: 'category_from'}
        ]
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    miniShop2.window.CopyCategory.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CopyCategory,MODx.Window);
Ext.reg('minishop2-window-copy-category',miniShop2.window.CopyCategory);

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