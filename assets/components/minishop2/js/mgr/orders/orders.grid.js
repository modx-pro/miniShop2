miniShop2.grid.Orders = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{comment}</p>')
		,renderer : function(v, p, record){return record.data.comment != '' && record.data.comment != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});
	this.sm = new Ext.grid.CheckboxSelectionModel();

	Ext.applyIf(config,{
		id: 'minishop2-grid-orders'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/orders/getlist'
		}
		,fields: miniShop2.config.order_grid_fields
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,bodyCssClass: 'grid-with-buttons'
		,sm: this.sm
		,cls: 'minishop2-grid'
		,plugins: this.exp
		,columns: this.getColumns()
		,tbar: [{
				text: '<i class="'+ (MODx.modx23 ? 'icon icon-list' : 'bicon-list') + '"></i> ' + _('ms2_bulk_actions')
				,menu: [
				/*{
					text: _('ms2_product_selected_status')
					,handler: this.changeStatus
					,scope: this
				},'-',*/{
					text: _('ms2_menu_remove_multiple')
					,handler: this.removeSelected
					,scope: this
				}]
			}
			,{xtype: 'spacer',style: 'width:50px;'}
			,{
				xtype: 'minishop2-combo-status'
				,id: 'tbar-minishop2-combo-status'
				,width: 200
				,addall: true
				,listeners: {
					select: {fn: this.filterByStatus, scope:this}
				}
			},'->',{
				xtype: 'textfield'
				,name: 'query'
				,width: 200
				,id: 'minishop2-orders-search'
				,emptyText: _('ms2_search')
				,listeners: {
					render: {fn:function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER,function() {this.FilterByQuery(tf);},this);},scope:this}
				}
			},{
				xtype: 'button'
				,id: 'minishop2-orders-clear'
				,text: '<i class="'+ (MODx.modx23 ? 'icon icon-times' : 'bicon-remove-sign') + '"></i>'/* + _('ms2_search_clear')*/
				,listeners: {
					click: {fn: this.clearFilter, scope: this}
				}
			}
		]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateOrder(grid, e, row);
			}
		}
	});
	miniShop2.grid.Orders.superclass.constructor.call(this,config);
	this.changed = false;
	this._makeTemplates();

	this.on('afterrender', function(grid) {
		var params = miniShop2.utils.Hash.get();
		var order = params['order'] || '';
		if (order) {
			this.updateOrder(grid, Ext.EventObject, {data: {id: order}});
		}
	});
};
Ext.extend(miniShop2.grid.Orders,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateOrder
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeOrder
		});
		this.addContextMenuItem(m);
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
		Ext.getCmp('minishop2-orders-search').setValue('');
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,filterByStatus: function(cb) {
		this.getStore().baseParams['status'] = cb.value;
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,_makeTemplates: function() {
		var userPage = MODx.action ? MODx.action['security/user/update'] : 'security/user/update';
		this.tplCustomer = new Ext.XTemplate(''
			+'<tpl for="."><div class="order-title-column {cls}">'
				+'<h3 class="main-column"><span class="title">' + _('ms2_order') + ' #{num}</span></h3>'
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

	,_renderCustomer:function(v,md,rec) {
		return this.tplCustomer.apply(rec.data);
	}

	,_renderCost:function(v,md,rec) {
		return rec.data.type && rec.data.type == 1
			? '-'+v
			: v;
	}

	,onClick: function(e){
		var t = e.getTarget();
		var elm = t.className.split(' ')[0];
		if(elm == 'controlBtn') {
			var action = t.className.split(' ')[1];
			this.menu.record = this.getSelectionModel().getSelected().data;
			switch (action) {
				case 'update':
					this.updateOrder(this,e);
					break;
				case 'delete':
					this.removeOrder(this,e);
					break;
			}
		}
		this.processEvent('click', e);
	}


	,getColumns: function() {
		var all = {
			id: {width: 35}
			,customer: {width: 100, sortable: true, renderer: miniShop2.utils.userLink}
			,num: {width: 100, sortable: true, renderer: {fn:this._renderCustomer,scope:this}, id: 'main'}
			,receiver: {width: 100, sortable: true}
			,createdon: {width: 75, sortable: true, renderer: miniShop2.utils.formatDate}
			,updatedon: {width: 75, sortable: true, renderer: miniShop2.utils.formatDate}
			,cost: {width: 75, sortable: true, renderer: this._renderCost}
			,cart_cost: {width: 75, sortable: true}
			,delivery_cost: {width: 75, sortable: true}
			,weight: {width: 50, sortable: true}
			,status: {width: 75, sortable: true}
			,delivery: {width: 75, sortable: true}
			,payment: {width: 75, sortable: true}
			//,address: {width: 50, sortable: true}
			,context: {width: 50, sortable: true}
		};

		var columns = [this.sm, this.exp];
		for(var i=0; i < miniShop2.config.order_grid_fields.length; i++) {
			var field = miniShop2.config.order_grid_fields[i];
			if (all[field]) {
				Ext.applyIf(all[field], {
					header: _('ms2_' + field)
					,dataIndex: field
				});
				columns.push(all[field]);
			}
		}

		return columns;
	}

	,updateOrder: function(btn,e,row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		var id = this.menu.record.id;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/orders/get'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					var w = Ext.getCmp('minishop2-window-order-update');
					if (w) {w.hide().getEl().remove();}

					w = MODx.load({
							xtype: 'minishop2-window-order-update'
							,id: 'minishop2-window-order-update'
							,record:r.object
							,listeners: {
								success: {fn:function() {this.refresh();},scope:this}
								,hide: {fn: function() {
									miniShop2.utils.Hash.remove('order');
									if (miniShop2.grid.Orders.changed === true) {
										Ext.getCmp('minishop2-grid-orders').getStore().reload();
										miniShop2.grid.Orders.changed = false;
									}
									var item = this;
									window.setTimeout(function() {
										item.close();
									}, 100);
								}}
								,afterrender: function() {
									miniShop2.utils.Hash.add('order', r.object['id']);
								}
							}
						});
					w.fp.getForm().reset();
					w.fp.getForm().setValues(r.object);
					w.show(e.target,function() {w.setPosition(null,100)},this);
					/* Need to refresh grids with goods and logs */
				},scope:this}
			}
		});
	}

	,removeOrder: function(btn,e) {
		if (!this.menu.record) return;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + ' ' + _('ms2_order') + ' #' + this.menu.record.num
			,text: _('ms2_menu_remove_confirm')
			,url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/orders/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) { this.refresh();}, scope:this}
			}
		});
	}

	,removeSelected: function(btn,e) {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove_multiple')
			,text: _('ms2_menu_remove_multiple_confirm')
			,url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/orders/remove_multiple'
				,ids: cs
			}
			,listeners: {
				success: {fn:function(r) {
					this.getSelectionModel().clearSelections(true);
					this.refresh();
				},scope:this}
			}
		});
		return true;
	}
});
Ext.reg('minishop2-grid-orders',miniShop2.grid.Orders);



miniShop2.window.UpdateOrder = function(config) {
	config = config || {};

	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_update')
		,id: this.ident
		,width: 750
		,autoHeight: true
		,labelAlign: 'top'
		,url: miniShop2.config.connector_url
		,action: 'mgr/orders/update'
		,fields: {
			xtype: 'modx-tabs'
			//,border: true
			,activeTab: config.activeTab || 0
			,bodyStyle: { background: 'transparent'}
			,deferredRender: false
			,autoHeight: true
			,stateful: true
			,stateId: 'minishop2-window-order-update'
			,stateEvents: ['tabchange']
			,getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: this.getTabs(config)
		}
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateOrder.superclass.constructor.call(this,config);

};
Ext.extend(miniShop2.window.UpdateOrder,MODx.Window, {

	getTabs: function(config) {
		var tabs = [{
			title: _('ms2_order')
			,hideMode: 'offsets'
			,bodyStyle: 'padding:5px 0;'
			,defaults: {msgTarget: 'under',border: false}
			,items: this.getOrderFields(config)
		},{
			xtype: 'minishop2-grid-order-products'
			,title: _('ms2_order_products')
			,order_id: config.record.id
		}];

		var address = this.getAddressFields(config);
		if (address.length > 0) {
			tabs.push({
				layout: 'form'
				,title: _('ms2_address')
				,hideMode: 'offsets'
				,bodyStyle: 'padding:5px 0;'
				,defaults: {msgTarget: 'under',border: false}
				,items: address
			});
		}

		tabs.push({
			xtype: 'minishop2-grid-order-logs'
			,title: _('ms2_order_log')
			,order_id: config.record.id
		});

		return tabs;
	}

	,getOrderFields: function(config) {
		return [{
				xtype: 'hidden'
				,name: 'id'
			},{
				layout: 'column'
				,defaults: {msgTarget: 'under',border: false}
				,style: 'padding:15px 5px;text-align:center;'
				,items: [{
					columnWidth: .5
					,layout: 'form'
					,items: [{xtype: 'displayfield', name: 'fullname', fieldLabel: _('ms2_user'), anchor: '100%', style: 'font-size:1.1em;'}]
				},{
					columnWidth: .5
					,layout: 'form'
					,items: [{xtype: 'displayfield', name: 'cost', fieldLabel: _('ms2_order_cost'), anchor: '100%', style: 'font-size:1.1em;'}]
				}]
			},{
				xtype: 'fieldset'
				,layout: 'column'
				,style: 'padding:15px 5px;text-align:center;'
				,defaults: {msgTarget: 'under',border: false}
				,items: [{
					columnWidth: .33
					,layout: 'form'
					,items: [
						{xtype: 'displayfield', name: 'num', fieldLabel: _('ms2_num'), anchor: '100%', style: 'font-size:1.1em;'}
						,{xtype: 'displayfield', name: 'cart_cost', fieldLabel: _('ms2_cart_cost'), anchor: '100%'}
					]
				},{
					columnWidth: .33
					,layout: 'form'
					,items: [
						{xtype: 'displayfield', name: 'createdon', fieldLabel: _('ms2_createdon'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'delivery_cost', fieldLabel: _('ms2_delivery_cost'), anchor: '100%'}
					]
				},{
					columnWidth: .33
					,layout: 'form'
					,items: [
						{xtype: 'displayfield', name: 'updatedon', fieldLabel: _('ms2_updatedon'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'weight', fieldLabel: _('ms2_weight'), anchor: '100%'}
					]
				}]
			},{
				layout: 'column'
				,defaults: {msgTarget: 'under',border: false}
				,anchor: '100%'
				,items: [{
					columnWidth: .48
					,layout: 'form'
					,items: [
						{xtype: 'minishop2-combo-status', name: 'status', fieldLabel: _('ms2_status'), anchor: '100%', order_id: config.record.id}
						,{xtype: 'minishop2-combo-delivery', name: 'delivery', fieldLabel: _('ms2_delivery'), anchor: '100%'}
						,{xtype: 'minishop2-combo-payment', name: 'payment', fieldLabel: _('ms2_payment'), anchor: '100%', delivery_id: config.record.delivery}
					]
				},{
					columnWidth: .5
					,layout: 'form'
					,items: [
						{xtype: 'textarea', name: 'comment', fieldLabel: _('ms2_comment'), anchor: '100%', height: 155}
					]
				}]
			}
		];
	}

	,getAddressFields: function(config) {
		var all = {receiver:{},phone:{},index:{},country:{},region:{},metro:{},building:{},city:{},street:{},room:{}};
		var fields = [], tmp = [];
		for (var i=0; i < miniShop2.config.order_address_fields.length; i++) {
			var field = miniShop2.config.order_address_fields[i];
			if (all[field]) {
				Ext.applyIf(all[field], {
					xtype: 'textfield'
					,name: 'addr_' + field
					,fieldLabel: _('ms2_' + field)
				});
				all[field].anchor = '100%';
				tmp.push(all[field]);
			}
		}

		var addx = function(w1, w2) {
			if (!w1) {w1 = .5;}
			if (!w2) {w2 = .5;}
			return {
				layout:'column'
				,defaults: {msgTarget: 'under',border: false}
				,items: [
					{columnWidth: w1, layout: 'form', items: []}
					,{columnWidth: w2, layout: 'form', items: []}
				]
			};
		}

		var n;
		if (tmp.length > 0) {
			for (i = 0; i < tmp.length; i++) {
				if (i == 0) fields.push(addx(.7,.3));
				else if (i == 2) fields.push(addx(.3,.7));
				else if (i % 2 == 0) fields.push(addx());

				if (i <= 1) {n = 0;}
				else {n = Math.floor(i / 2);}
				fields[n].items[i % 2].items.push(tmp[i]);
			}
			if (miniShop2.config.order_address_fields.in_array('comment')) {
				fields.push(
					{xtype: 'displayfield', name: 'addr_comment', fieldLabel: _('ms2_comment'), anchor: '98%',style: 'min-height: 50px;border:1px solid #efefef;width:95%;'}
				);
			}
		}

		return fields;
	}
});
Ext.reg('minishop2-window-order-update',miniShop2.window.UpdateOrder);




/*------------------------------------*/
miniShop2.grid.Logs = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: this.ident
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/orders/getlog'
			,order_id: config.order_id
			,type: 'status'
		}
		,fields: ['id','user_id','username','fullname','timestamp','action','entry']
		,pageSize: Math.round(MODx.config.default_per_page / 2)
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			{header: _('ms2_id'),dataIndex: 'id', hidden: true, sortable: true, width: 50}
			,{header: _('ms2_username'),dataIndex: 'username', width: 75, renderer: miniShop2.utils.userLink}
			,{header: _('ms2_fullname'),dataIndex: 'fullname', width: 100}
			,{header: _('ms2_timestamp'),dataIndex: 'timestamp', sortable: true, renderer: miniShop2.utils.formatDate, width: 75}
			,{header: _('ms2_action'),dataIndex: 'action', width: 50}
			,{header: _('ms2_entry'),dataIndex: 'entry', width: 50}
		]
	});
	miniShop2.grid.Logs.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Logs,MODx.grid.Grid);
Ext.reg('minishop2-grid-order-logs',miniShop2.grid.Logs);


miniShop2.grid.Products = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: this.ident
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/orders/product/getlist'
			,order_id: config.order_id
			,type: 'status'
		}
		,fields: miniShop2.config.order_product_fields
			//['id','product_id','pagetitle','article','weight','count','price','cost']
		,pageSize: Math.round(MODx.config.default_per_page / 2)
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: this.getColumns()
		/*[
			{header: _('ms2_id'),dataIndex: 'id', hidden: true, sortable: true, width: 40}
			,{header: _('ms2_product_id'), dataIndex: 'product_id', hidden: true, sortable: true, width: 40}
			,{header: _('ms2_product_pagetitle'),dataIndex: 'pagetitle', width: 100, renderer: miniShop2.utils.productLink}
			,{header: _('ms2_product_article'),dataIndex: 'article', width: 50}
			,{header: _('ms2_product_weight'),dataIndex: 'weight', sortable: true, width: 50}
			,{header: _('ms2_product_price'),dataIndex: 'price', sortable: true, width: 50}
			,{header: _('ms2_count'),dataIndex: 'count', sortable: true, width: 50}
			,{header: _('ms2_cost'),dataIndex: 'cost', width: 50}
		]*/
		,tbar: [{
			xtype: 'minishop2-combo-product'
			,allowBlank: true
			,width: '50%'
			,listeners: {
				select: {fn: this.addOrderProduct, scope: this}
			}
		}]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateOrderProduct(grid, e, row);
			}
		}
	});
	miniShop2.grid.Products.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Products,MODx.grid.Grid, {

	getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateOrderProduct
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeOrderProduct
		});
		this.addContextMenuItem(m);
	}

	,getColumns: function() {
		var fields = {
			id: {hidden: true, sortable: true, width: 40}
			,product_id: {hidden: true, sortable: true, width: 40}
			,name: {header: _('ms2_name'), width: 100, renderer: miniShop2.utils.productLink}
			,product_weight: {header: _('ms2_product_weight'), width: 50}
			,product_price: {header: _('ms2_product_price'), width: 50}
			,product_article: {width: 50}
			,weight: {sortable: true, width: 50}
			,price: {sortable: true, header: _('ms2_product_price'), width: 50}
			,count: {sortable: true, width: 50}
			,cost: {width: 50}
			,options: {width: 100}
		};

		var columns = [];
		for(var i=0; i < miniShop2.config.order_product_fields.length; i++) {
			var field = miniShop2.config.order_product_fields[i];
			if (fields[field]) {
				Ext.applyIf(fields[field], {
					header: _('ms2_' + field)
					,dataIndex: field
				});
				columns.push(fields[field]);
			}
			else if (/^option_/.test(field)) {
				columns.push(
					{header: _(field.replace(/^option_/, 'ms2_')), dataIndex: field, width: 50}
				);
			}
			else if (/^product_/.test(field)) {
				columns.push(
					{header: _(field.replace(/^product_/, 'ms2_')), dataIndex: field, width: 75}
				);
			}
		}

		return columns;
	}

	,addOrderProduct: function(combo, row, e) {
		var id = row.id;
		combo.reset();

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/get'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					 var w = Ext.getCmp('minishop2-window-orderproduct-update');
					 if (w) {w.hide().getEl().remove();}

					r.object.order_id = this.config.order_id;
					r.object.count = 1;
					 w = MODx.load({
						 xtype: 'minishop2-window-orderproduct-update'
						 ,id: 'minishop2-window-orderproduct-update'
						 ,record:r.object
						 ,action: 'mgr/orders/product/create'
						 ,listeners: {
							 success: {fn:function() {
								 miniShop2.grid.Orders.changed = true;
								 this.refresh();
							 },scope:this}
							 ,hide: {fn: function() {this.getEl().remove();}}
						 }
					 });
					 w.fp.getForm().reset();
					 w.fp.getForm().setValues(r.object);
					 w.show(e.target,function() {w.setPosition(null,100)},this);
				},scope:this}
			}
		});
	}

	,updateOrderProduct: function(btn,e,row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		var id = this.menu.record.id;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/orders/product/get'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					var w = Ext.getCmp('minishop2-window-orderproduct-update');
					if (w) {w.hide().getEl().remove();}

					r.object.order_id = this.config.order_id;
					w = MODx.load({
						xtype: 'minishop2-window-orderproduct-update'
						,id: 'minishop2-window-orderproduct-update'
						,record:r.object
						,action: 'mgr/orders/product/update'
						,listeners: {
							success: {fn:function() {
								miniShop2.grid.Orders.changed = true;
								this.refresh();
							},scope:this}
							,hide: {fn: function() {this.getEl().remove();}}
						}
					});
					w.fp.getForm().reset();
					w.fp.getForm().setValues(r.object);
					w.show(e.target,function() {w.setPosition(null,100)},this);
				},scope:this}
			}
		});
	}

	,removeOrderProduct: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove')
			,text: _('ms2_menu_remove_confirm')
			,url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/orders/product/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) { this.refresh(); },scope:this}
			}
		});
		return true;
	}
});
Ext.reg('minishop2-grid-order-products',miniShop2.grid.Products);


miniShop2.window.OrderProduct = function(config) {
	config = config || {};
	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_update')
		,autoHeight: true
		,width: 600
		,autoHeight: true
		,url: miniShop2.config.connector_url
		,action: config.action || 'mgr/orders/product/update'
		,fields: [
			{xtype: 'hidden',name: 'id'}
			,{xtype: 'hidden',name: 'order_id'}
			,{
				layout:'column'
				,border: false
				,anchor: '100%'
				,items: [
					{columnWidth: .3,layout: 'form',defaults: { msgTarget: 'under' }, border:false, items: [
						{xtype: 'numberfield', fieldLabel: _('ms2_product_count'), name: 'count', anchor: '100%', allowNegative: false, allowBlank: false}
					]}
					,{columnWidth: .7,layout: 'form',defaults: { msgTarget: 'under' }, border:false, items: [
						{xtype: 'textfield', fieldLabel: _('ms2_name'), name: 'name', anchor: '100%' }
					]}
				]
			}
			,{
				layout:'column'
				,border: false
				,anchor: '100%'
				,items: [
					{columnWidth: .5,layout: 'form',defaults: { msgTarget: 'under' }, border:false, items: [
						{xtype: 'numberfield', decimalPrecision: 2, fieldLabel: _('ms2_product_price'), name: 'price', anchor: '100%'}
					]}
					,{columnWidth: .5,layout: 'form',defaults: { msgTarget: 'under' }, border:false, items: [
						{xtype: 'numberfield', decimalPrecision: 3, fieldLabel: _('ms2_product_weight'), name: 'weight', anchor: '100%'}
					]}
				]
			}
			,{xtype: 'textarea',fieldLabel: _('ms2_product_options'), name: 'options', height: 100, anchor: '100%'}
		]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.OrderProduct.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.OrderProduct,MODx.Window);
Ext.reg('minishop2-window-orderproduct-update',miniShop2.window.OrderProduct);