miniShop2.grid.Category = function(config) {
	config = config || {};

	var baseParams = {
		action: 'mgr/product/getlist'
		,parent: config.resource
		,sort: 'rank'
	};
	var params = {};
	if (MODx.config.ms2_category_remember_grid) {
		params = Ext.util.JSON.decode(
			Ext.util.Cookies.get('minishop2-category-grid-' + config.resource)
		);
		if (params.query) {baseParams.query = params.query;}
		if (params.sort) {baseParams.sort = params.sort;}
		if (params.dir) {baseParams.dir = params.dir;}
	}

	this.sm = new Ext.grid.CheckboxSelectionModel();
	Ext.applyIf(config,{
		id: 'minishop2-grid-category'
		,url: miniShop2.config.connector_url
		,baseParams: baseParams
		,pageStart: params.start || ''
		,pageSize: params.limit || ''
		,save_action: 'mgr/product/updatefromgrid'
		,fields: miniShop2.config.product_fields
		,autosave: true
		,save_callback: this.updateRow
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,bodyCssClass: 'grid-with-buttons'
		,sm: this.sm
		,cls: 'minishop2-grid'
		,columns: this.getColumns()
		,stateful: true
		,stateId: 'minishop2-category-grid'
		,stateEvents: ['columnresize']
		,tbar: [{
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-list' : 'bicon-list') + '"></i> ' + _('ms2_bulk_actions')
			,menu: [
				{text: _('ms2_product_selected_publish'),handler: this.publishSelected,scope: this}
				,{text: _('ms2_product_selected_unpublish'),handler: this.unpublishSelected,scope: this}
				,'-'
				,{text: _('ms2_product_selected_delete'),handler: this.deleteSelected,scope: this}
				,{text: _('ms2_product_selected_undelete'),handler: this.undeleteSelected,scope: this}
			]
		},'-',{
			text: '<i class="'+ (MODx.modx23 ? 'icon icon-plus' : 'bicon-plus-sign') + '"></i> ' + _('ms2_product_create')
			,handler: this.createProduct
			,scope: this
		},'->',{
			xtype: 'textfield'
			,name: 'query'
			,width: 200
			,id: 'minishop2-product-search'
			,emptyText: _('ms2_search')
			,listeners: {render: {fn: function(tf) {
				tf.getEl().addKeyListener(Ext.EventObject.ENTER, function() {this.search(tf);}, this);
				tf.setValue(baseParams.query);
			},scope: this}}
		},{
			xtype: 'button'
			,id: 'minishop2-product-clear'
			,text: '<i class="'+ (MODx.modx23 ? 'icon icon-times' : 'bicon-remove-sign') + '"></i>' // + _('ms2_search_clear')
			,listeners: {
				click: {fn: this.clearFilter, scope: this}
			}
		}]
		,ddGroup: 'dd'
		,enableDragDrop: true
		,listeners: {
			render: {fn: this._initDD, scope: this}
		}
	});
	miniShop2.grid.Category.superclass.constructor.call(this,config);
	this._makeTemplates();

	if (MODx.config.ms2_category_remember_grid) {
		this.getStore().on('load', function(grid, records, options) {
			var params = Ext.util.JSON.encode(options.params);
			Ext.util.Cookies.set('minishop2-category-grid-' + config.resource, params);
		});
	}
};
Ext.extend(miniShop2.grid.Category,MODx.grid.Grid,{

	_initDD: function(grid) {
		new Ext.dd.DropTarget(grid.el, {
			ddGroup : 'dd',
			copy:false,
			notifyDrop : function(dd, e, data) {
				var store = grid.store.data.items;
				var target = store[dd.getDragData(e).rowIndex].data;
				var source = store[data.rowIndex].data;
				if ((target.parent == source.parent) && (target.id != source.id)) {
					dd.el.mask(_('loading'),'x-mask-loading');
					MODx.Ajax.request({
						url: miniShop2.config.connector_url
						,params: {
							action: 'mgr/product/sort'
							,source: source.id
							,target: target.id
							,parent: MODx.request.id // this should be page ID
						}
						,listeners: {
							success: {fn:function(r) {dd.el.unmask();grid.refresh();},scope:grid}
							,failure: {fn:function(r) {dd.el.unmask();},scope:grid}
						}
					});
				}
			}
		});
	}

	,_makeTemplates: function() {
		var updatePage = MODx.action ? MODx.action['resource/update'] : 'resource/update';
		this.tplPageTitle = new Ext.XTemplate(''
			+'<tpl for="."><div class="product-title-column {cls}">'
				+'<h3 class="main-column"><span class="product-id">({id})</span><a href="?a=' + updatePage + '&id={id}" onclick="if (event.which == 1) {MODx.loadPage(\''+updatePage+'\', \'id={id}\'); return false;}">{pagetitle}</a></h3><div class="product-category">{category_name}</div>'
				+'<tpl if="actions">'
					+'<ul class="actions">'
						+'<tpl for="actions">'
							+'<li><a href="#" class="controlBtn {className}">{text}</a></li>'
						+'</tpl>'
					+'</ul>'
				+'</tpl>'
			+'</div></tpl>',{
			compiled: true
		});
	}

	,_renderPageTitle:function(v,md,rec) {
		return this.tplPageTitle.apply(rec.data);
	}

	,onClick: function(e){
		var t = e.getTarget();
		var elm = t.className.split(' ')[0];
		if(elm == 'controlBtn') {
			var action = t.className.split(' ')[1];
			this.menu.record = this.getSelectionModel().getSelected();
			switch (action) {
				case 'delete':
					this.deleteProduct();
					break;
				case 'undelete':
					this.undeleteProduct();
					break;
				case 'edit':
					this.editProduct();
					break;
				case 'publish':
					this.publishProduct();
					break;
				case 'unpublish':
					this.unpublishProduct();
					break;
				case 'view':
					this.viewProduct();
					break;
			}
		}
		this.processEvent('click', e);
	}

	,search: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function(btn,e) {
		var s = this.getStore();
		s.baseParams.query = '';
		Ext.getCmp('minishop2-product-search').setValue('');
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,viewProduct: function(btn,e) {
		window.open(this.menu.record.data.preview_url);
		return false;
	}

	,createProduct: function(btn,e) {
		var createPage = MODx.action ? MODx.action['resource/create'] : 'resource/create';
		MODx.loadPage(createPage, 'class_key=msProduct&parent='+MODx.request.id+'&context_key='+MODx.ctx);
	}

	,editProduct: function(btn,e) {
		var updatePage = MODx.action ? MODx.action['resource/update'] : 'resource/update';
		MODx.loadPage(updatePage, 'id=' + this.menu.record.id);
	}

	,deleteProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/delete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,undeleteProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/undelete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,publishProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/publish'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,unpublishProduct: function(btn,e) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/unpublish'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,deleteSelected: function(btn,e) {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/delete_multiple'
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

	,undeleteSelected: function(btn,e) {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/undelete_multiple'
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

	,publishSelected: function(btn,e) {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/publish_multiple'
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

	,unpublishSelected: function(btn,e) {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/unpublish_multiple'
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

	,getColumns: function() {
		var columns =  {
			pagetitle: {width:150, sortable:true, renderer: {fn:this._renderPageTitle,scope:this}, id: 'main'}
			,longtitle: {width:50, sortable:true, editor:{xtype:'textfield'}}
			,description: {width:100, sortable:false, editor:{xtype:'textarea'}}
			,alias: {width:50, sortable:true, editor:{xtype:'textfield'}}
			,introtext: {width:100, sortable:false, editor:{xtype:'textarea'}}
			,content: {width:100, sortable:false, editor:{xtype:'textarea'}}
			,template: {width:100, sortable:true, editor:{xtype:'modx-combo-template'}}
			,createdby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'createdby'}}
			,createdon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime', timePosition:'below'}, renderer: miniShop2.utils.formatDate}
			,editedby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'editedby'}}
			,editedon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime', timePosition:'below'}, renderer: miniShop2.utils.formatDate}
			,deleted: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,deletedon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime', timePosition:'below'}, renderer: miniShop2.utils.formatDate}
			,deletedby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'deletedby'}}
			,published: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,publishedon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime', timePosition:'below'}, renderer: miniShop2.utils.formatDate}
			,publishedby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'publishedby'}}
			,menutitle: {width:100, sortable:true, editor:{xtype:'textfield'}}
			,menuindex: {width:50, sortable:true, editor:{xtype:'numberfield'}}
			,uri: {width:50, sortable:true, editor:{xtype:'textfield'}}

			,new: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,favorite: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,popular: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,uri_override: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,show_in_tree: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,hidemenu: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,richtext: {width:100, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,searchable: {width:100, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,cacheable: {width:100, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}

			,article: {width:50, sortable:true, editor:{xtype:'textfield'}}
			,price: {width:50, sortable:true, editor:{xtype:'numberfield', decimalPrecision: 2}}
			,old_price: {width:50, sortable:true, editor:{xtype:'numberfield', decimalPrecision: 2}}
			,weight: {width:50, sortable:true, editor:{xtype:'numberfield', decimalPrecision: 3}}
			,image: {width:50, sortable:false, renderer: this.renderThumb}
			,thumb: {width:50, sortable:false, renderer: this.renderThumb}
			,vendor: {width:50, sortable:true, renderer: this.renderVendor, editor: {xtype: 'minishop2-combo-vendor'}}
			,made_in: {width:50, sortable:true, editor: {xtype: 'minishop2-combo-autocomplete', name: 'made_in'}}

			,vendor_name: {width:50, sortable:true, header: _('ms2_product_vendor')}
			//,tags: {width:50, sortable:false, editor: {xtype: 'minishop2-combo-options', name: 'tags'}}
			//,color: {width:50, sortable:false, editor: {xtype: 'minishop2-combo-options', name: 'color'}}
			//,size: {width:50, sortable:false, editor: {xtype: 'minishop2-combo-options', name: 'size'}}
		};

		for (i in miniShop2.plugin) {
			if (typeof(miniShop2.plugin[i]['getColumns']) == 'function') {
				var add = miniShop2.plugin[i].getColumns();
				Ext.apply(columns, add);
			}
		}

		var fields = [this.sm];
		for (var i = 0; i < miniShop2.config.grid_fields.length; i++) {
			var field = miniShop2.config.grid_fields[i];
			if (columns[field]) {
				Ext.applyIf(columns[field], {
					header: _('ms2_product_' + field)
					,dataIndex: field
				});
				fields.push(columns[field]);
			}
		}

		return fields;
	}

	,updateRow: function(response) {
		var row = response.object;
		var items = this.store.data.items;

		for (var i = 0; i < items.length; i++) {
			var item = items[i];
			if (item.id == row.id) {
				var pagetitle = item.data.pagetitle;
				item.data = row;
				item.data.pagetitle = pagetitle;
			}
		}
	}

	,renderThumb: function(value) {
		if (value) {
			return '<img src="' + value + '" height="41" style="display:block;margin:auto;"/>';
		}
		else {
			return '';
		}
	}

	,renderVendor: function(value, cell, row) {
		return row.data['vendor_name'];
	}

});
Ext.reg('minishop2-grid-category',miniShop2.grid.Category);