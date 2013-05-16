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
		,fields: ['id','user_id','customer','num','status','delivery','payment','cost','weight','createdon','updatedon','comment','context']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,sm: this.sm
		,plugins: this.exp
		,columns: [this.sm, this.exp
			,{header: _('ms2_id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ms2_customer'),dataIndex: 'customer',width: 150, sortable: true, renderer: miniShop2.utils.userLink}
			,{header: _('ms2_num'),dataIndex: 'num',width: 50, sortable: true}
			,{header: _('ms2_status'),dataIndex: 'status',width: 50, sortable: true}
			,{header: _('ms2_cost'),dataIndex: 'cost',width: 50, sortable: true}
			,{header: _('ms2_weight'),dataIndex: 'weight',width: 50, sortable: true}
			,{header: _('ms2_delivery'),dataIndex: 'delivery',width: 100, sortable: true}
			,{header: _('ms2_payment'),dataIndex: 'payment',width: 100, sortable: true}
			,{header: _('ms2_createdon'),dataIndex: 'createdon',width: 75, sortable: true, renderer: miniShop2.utils.formatDate}
			,{header: _('ms2_updatedon'),dataIndex: 'updatedon',width: 75, sortable: true, renderer: miniShop2.utils.formatDate}
		]
		,tbar: [{
				text: '<i class="bicon-list"></i> ' + _('ms2_bulk_actions')
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
				,text: '<i class="bicon-remove-sign"></i>'/* + _('ms2_search_clear')*/
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
									if (miniShop2.grid.Orders.changed === true) {
										Ext.getCmp('minishop2-grid-orders').getStore().reload();
										miniShop2.grid.Orders.changed = false;
									}
									this.getEl().remove();
								}}
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
		if (!this.menu.record) return false;

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
/*
	,getOrdersFields: function(type) {
		return [
			{xtype: 'hidden',name: 'id', id: 'minishop2-orders-id-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'name', allowBlank: false, anchor: '99%', id: 'minishop2-orders-name-'+type}
			,{xtype: 'minishop2-combo-browser',fieldLabel: _('ms2_logo'), name: 'logo', anchor: '99%',  id: 'minishop2-orders-logo-'+type}
			,{xtype: 'textarea', fieldLabel: _('ms2_description'), name: 'description', anchor: '99%', id: 'minishop2-orders-description-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_class'), name: 'class', anchor: '99%', id: 'minishop2-orders-class-'+type}
			,{xtype: 'xcheckbox', fieldLabel: '', boxLabel: _('ms2_active'), name: 'active', id: 'minishop2-orders-active-'+type}
		];
	}
*/
});
Ext.reg('minishop2-grid-orders',miniShop2.grid.Orders);



miniShop2.window.UpdateOrder = function(config) {
	config = config || {};

	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_update')
		,id: this.ident
		,width: 750
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
			,items: [{
				title: _('ms2_order')
				,hideMode: 'offsets'
				,bodyStyle: 'padding:5px 0;'
				,defaults: {msgTarget: 'under',border: false}
				,items: this.getOrderFields(config)
			},{
				xtype: 'minishop2-grid-order-products'
				,title: _('ms2_order_products')
				,order_id: config.record.id
			},{
				layout: 'form'
				,title: _('ms2_address')
				,hideMode: 'offsets'
				,bodyStyle: 'padding:5px 0;'
				,defaults: {msgTarget: 'under',border: false}
				,items: this.getAddressFields(config)
			},{
				xtype: 'minishop2-grid-order-logs'
				,title: _('ms2_order_log')
				,order_id: config.record.id
			}]

		}
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateOrder.superclass.constructor.call(this,config);

};
Ext.extend(miniShop2.window.UpdateOrder,MODx.Window, {

	getOrderFields: function(config) {
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
		return [
			{
				layout: 'column'
				,defaults: {msgTarget: 'under',border: false}
				,items: [{
					columnWidth: .7
					,layout: 'form'
					,items: [{xtype: 'textfield', name: 'addr_receiver', fieldLabel: _('ms2_receiver'), anchor: '100%'}]
				},{
					columnWidth: .3
					,layout: 'form'
					,items: [{xtype: 'textfield', name: 'addr_phone', fieldLabel: _('ms2_phone'), anchor: '100%'}]
				}]
			},{
				layout: 'column'
				,defaults: {msgTarget: 'under',border: false}
				,items: [{
					columnWidth: .3
					,layout: 'form'
					,items: [{xtype: 'textfield', name: 'addr_index', fieldLabel: _('ms2_index'), anchor: '100%'}]
				},{
					columnWidth: .7
					,layout: 'form'
					,items: [{xtype: 'textfield', name: 'addr_country', fieldLabel: _('ms2_country'), anchor: '100%'}]
				}]
			},{
				layout: 'column'
				,defaults: {msgTarget: 'under',border: false}
				,items: [{
					columnWidth: .5
					,layout: 'form'
					,items: [
						{xtype: 'textfield', name: 'addr_region', fieldLabel: _('ms2_region'), anchor: '100%'}
						,{xtype: 'textfield', name: 'addr_metro', fieldLabel: _('ms2_metro'), anchor: '100%'}
						,{xtype: 'textfield', name: 'addr_building', fieldLabel: _('ms2_building'), anchor: '100%'}
					]
				},{
					columnWidth: .5
					,layout: 'form'
					,items: [
						{xtype: 'textfield', name: 'addr_city', fieldLabel: _('ms2_city'), anchor: '100%'}
						,{xtype: 'textfield', name: 'addr_street', fieldLabel: _('ms2_street'), anchor: '100%'}
						,{xtype: 'textfield', name: 'addr_room', fieldLabel: _('ms2_room'), anchor: '100%'}
					]
				}]
			}
			,{xtype: 'displayfield', name: 'addr_comment', fieldLabel: _('ms2_comment'), anchor: '100%'}
		];
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
		,fields: ['id','product_id','pagetitle','article','weight','count','price','cost']
		,pageSize: Math.round(MODx.config.default_per_page / 2)
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			{header: _('ms2_id'),dataIndex: 'id', hidden: true, sortable: true, width: 40}
			,{header: _('ms2_product_id'), dataIndex: 'product_id', hidden: true, sortable: true, width: 40}
			,{header: _('ms2_product_pagetitle'),dataIndex: 'pagetitle', width: 100, renderer: miniShop2.utils.productLink}
			,{header: _('ms2_product_article'),dataIndex: 'article', width: 50}
			,{header: _('ms2_product_weight'),dataIndex: 'weight', sortable: true, width: 50}
			,{header: _('ms2_product_price'),dataIndex: 'price', sortable: true, width: 50}
			,{header: _('ms2_count'),dataIndex: 'count', sortable: true, width: 50}
			,{header: _('ms2_cost'),dataIndex: 'cost', width: 50}
		]
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
					console.log(r.object);
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
						{xtype: 'textfield', fieldLabel: _('ms2_product_pagetitle'), name: 'pagetitle', anchor: '100%', disabled: true }
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