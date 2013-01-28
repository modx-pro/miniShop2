miniShop2.grid.Category = function(config) {
	config = config || {};

	this.sm = new Ext.grid.CheckboxSelectionModel();
	Ext.applyIf(config,{
		id: 'minishop2-grid-category'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/product/getlist'
			,parent: config.resource
		}
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
		,tbar: [{
			text: '<i class="bicon-list"></i> ' + _('ms2_bulk_actions')
			,menu: [{
				text: _('ms2_product_selected_publish')
				,handler: this.publishSelected
				,scope: this
			},{
				text: _('ms2_product_selected_unpublish')
				,handler: this.unpublishSelected
				,scope: this
			},'-',{
				text: _('ms2_product_selected_delete')
				,handler: this.deleteSelected
				,scope: this
			},{
				text: _('ms2_product_selected_undelete')
				,handler: this.undeleteSelected
				,scope: this
			}]
		},'-',{
			text: '<i class="bicon-plus-sign"></i> ' + _('ms2_product_create')
			,handler: this.createProduct
			,scope: this
		},
			'->'
			,{
				xtype: 'textfield'
				,name: 'query'
				,width: 200
				,id: 'minishop2-product-search'
				,emptyText: _('ms2_search')
				,listeners: {render: {fn: function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER, function() {this.search(tf);}, this);},scope: this}}
			},{
				xtype: 'button'
				,id: 'modx-filter-minishop2-clear'
				,text: '<i class="bicon-remove-sign"></i>'/* + _('ms2_search_clear')*/
				,listeners: {
					'click': {fn: this.clearFilter, scope: this}
				}
			}]
	});
	miniShop2.grid.Category.superclass.constructor.call(this,config);
	this._makeTemplates();
	this.on('rowclick',MODx.fireResourceFormChange);
	this.on('click', this.onClick, this);
};
Ext.extend(miniShop2.grid.Category,MODx.grid.Grid,{

	_makeTemplates: function() {
		this.tplPageTitle = new Ext.XTemplate(''
			+'<tpl for="."><div class="product-title-column">'
				+'<h3 class="main-column"><span class="product-id">{id}</span> <a href="{action_edit}">{pagetitle}</a></h3>'
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
			var record = this.getSelectionModel().getSelected();
			this.menu.record = record;
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
				default:
					window.location = record.data.edit_action;
					break;
			}
		}
	}

	,search: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function() {
		var s = this.getStore();
		s.baseParams.query = '';
		Ext.getCmp('minishop2-product-search').reset();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,viewProduct: function(btn,e) {
		window.open(this.menu.record.data.preview_url);
		return false;
	}

	,createProduct: function(btn,e) {
		var createPage = MODx.action ? MODx.action['resource/create'] : 'resource/create';
		MODx.loadPage(createPage, 'class_key=msProduct&parent='+MODx.request.id+'&context_key='+MODx.ctx+'&template=' + MODx.config.ms2_template_product_default || MODx.config.default_template);
	}

	,editProduct: function(btn,e) {
		location.href = 'index.php?a='+MODx.request.a+'&id='+this.menu.record.id;
	}

	,deleteProduct: function(btn,e) {
		MODx.msg.confirm({
			title: _('ms2_product_delete') + ' ' + this.menu.record.data.pagetitle
			,text: _('ms2_product_delete_desc')
			,url: miniShop2.config.connector_url
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
			//id: {width:25, sortable:true}
			pagetitle: {width:150, sortable:true, renderer: {fn:this._renderPageTitle,scope:this}, id: 'main'}
			,longtitle: {width:50, sortable:true}
			,description: {width:100, sortable:true}
			,alias: {width:50, sortable:true}
			,introtext: {width:100, sortable:true}
			,content: {width:100, sortable:true}
			,template: {width:100, sortable:true, editor:{xtype:'modx-combo-template'}}
			,richtext: {width:100, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,searchable: {width:100, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,cacheable: {width:100, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}

			,createdby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'createdby'}}
			,createdon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime', renderer: this.formatDate}}
			,editedby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'editedby'}}
			,editedon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime', renderer: this.formatDate}}
			,deleted: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,deletedon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime', renderer: this.formatDate}}
			,deletedby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'deletedby'}}
			,published: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,publishedon: {width:50, sortable:true, editor:{xtype:'minishop2-xdatetime'}, renderer: this.formatDate}
			,publishedby: {width:100, sortable:true, editor:{xtype:'minishop2-combo-user', name:'publishedby'}}

			,menutitle: {width:100, sortable:true}
			,hidemenu: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,uri: {width:50, sortable:true}
			,uri_override: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}
			,show_in_tree: {width:50, sortable:true, editor:{xtype:'combo-boolean', renderer:'boolean'}}

			,article: {width:50, sortable:true, editor:{xtype:'textfield'}}
			,price: {width:50, sortable:true, editor:{xtype:'numberfield'}}
			,new_price: {width:50, sortable:true, editor:{xtype:'numberfield'}}
			,weight: {width:50, sortable:true, editor:{xtype:'numberfield'}}
			,color: {width:50, sortable:true}
			,remains: {width:25, sortable:true, editor:{xtype:'numberfield'}}
			,reserved: {width:25, sortable:true, editor:{xtype:'numberfield'}}
			,image: {width:50, sortable:true}
			,vendor: {width:50, sortable:true}
			,made_in: {width:50, sortable:true}
		};

		var fields = [this.sm];
		for (var i = 0; i < miniShop2.config.grid_fields.length; i++) {
			var field = miniShop2.config.grid_fields[i];
			if (columns[field]) {
				columns[field].header = _('ms2_product_' + field);
				columns[field].dataIndex = field;
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


	,formatDate: function(value) {
		if (value) {
			var date = new Date(value);
			return date.format('DD.MM.YY&nbsp;&nbsp;<span class="gray">HH:NN</span>');
		}
		else {
			return '';
		}
	}
});
Ext.reg('minishop2-grid-category',miniShop2.grid.Category);