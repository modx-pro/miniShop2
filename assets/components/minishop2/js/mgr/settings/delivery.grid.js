miniShop2.grid.Delivery = function(config) {
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
							action: config.action || 'mgr/settings/delivery/sort'
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
		id: 'minishop2-grid-delivery'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/delivery/getlist'
		}
		,fields: ['id','name','description','price','weight_price','distance_price','logo','active','class','payments','requires']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/delivery/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_id'),dataIndex: 'id',width: 50}
			,{header: _('ms2_name'),dataIndex: 'name',width: 100, editor: {xtype: 'textfield', allowBlank: false}}
			,{header: _('ms2_add_cost'),dataIndex: 'price',width: 50, editor: {xtype: 'textfield'}}
			,{header: _('ms2_weight_price'),dataIndex: 'weight_price',width: 50, editor: {xtype: 'numberfield', decimalPrecision: 2}}
			,{header: _('ms2_distance_price'),dataIndex: 'distance_price',width: 50, editor: {xtype: 'numberfield', decimalPrecision: 2}}
			,{header: _('ms2_logo'),dataIndex: 'logo',width: 75, renderer: this.renderLogo}
			,{header: _('ms2_active'),dataIndex: 'active',width: 50, editor: {xtype: 'combo-boolean', renderer: 'boolean'}}
			,{header: _('ms2_class'),dataIndex: 'class',width: 75, editor: {xtype: 'textfield'}}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createDelivery
			,scope: this
		}]
		,ddGroup: 'dd'
		,enableDragDrop: true
		,listeners: {render: {fn: this.dd, scope: this}}
	});
	miniShop2.grid.Delivery.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Delivery,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateDelivery
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeDelivery
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

	,createDelivery: function(btn,e) {
		var w = Ext.getCmp('minishop2-window-delivery-create');
		if (w) {w.hide().getEl().remove();}
		//if (!this.windows.createDelivery) {
			this.windows.createDelivery = MODx.load({
				xtype: 'minishop2-window-delivery-create'
				,id: 'minishop2-window-delivery-create'
				,fields: this.getDeliveryFields('create', {})
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
					,hide: {fn:function() {this.getEl().remove()}}
				}
			});
		//}
		this.windows.createDelivery.fp.getForm().reset().setValues({
			price: 0
			,weight_price: 0
			,distance_price: 0
		});
		this.windows.createDelivery.show(e.target);
	}
	,updateDelivery: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		var w = Ext.getCmp('minishop2-window-delivery-update');
		if (w) {w.hide().getEl().remove();}
		//if (!this.windows.updateDelivery) {
			this.windows.updateDelivery = MODx.load({
				xtype: 'minishop2-window-delivery-update'
				,id: 'minishop2-window-delivery-update'
				,record: r
				,fields: this.getDeliveryFields('update', r)
				,listeners: {
					success: {fn:function() {this.refresh();},scope:this}
					,hide: {fn:function() {this.getEl().remove()}}
				}
			});
		//}
		this.windows.updateDelivery.fp.getForm().reset();
		this.windows.updateDelivery.fp.getForm().setValues(r);
		this.windows.updateDelivery.show(e.target);
		this.enablePayments(r.payments);
	}

	,removeDelivery: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
			,text: _('ms2_menu_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/delivery/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) {this.refresh();}, scope:this}
			}
		});
	}

	,getDeliveryFields: function(type) {
		var fields = [];
		fields.push({xtype: 'hidden',name: 'id', id: 'minishop2-delivery-id-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'name', allowBlank: false, anchor: '99%', id: 'minishop2-delivery-name-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_add_cost'), name: 'price', description: _('ms2_add_cost_help') ,allowBlank: true, anchor: '50%', id: 'minishop2-delivery-price-'+type}
			,{xtype: 'numberfield',fieldLabel: _('ms2_weight_price'), description: _('ms2_weight_price_help'), name: 'weight_price', decimalPrecision: 2, allowBlank: true, anchor: '50%', id: 'minishop2-delivery-weight_price-'+type}
			,{xtype: 'numberfield',fieldLabel: _('ms2_distance_price'), description: _('ms2_distance_price_help'), name: 'distance_price', decimalPrecision: 2, allowBlank: true, anchor: '50%', id: 'minishop2-delivery-distance_price-'+type}
			,{xtype: 'minishop2-combo-browser',fieldLabel: _('ms2_logo'), name: 'logo', anchor: '99%',  id: 'minishop2-delivery-logo-'+type}
			,{xtype: 'textarea', fieldLabel: _('ms2_description'), name: 'description', anchor: '99%', id: 'minishop2-delivery-description-'+type}
			,{xtype: 'textfield', fieldLabel: _('ms2_order_requires'), description: _('ms2_order_requires_help'), name: 'requires',anchor: '99%', id: 'minishop2-delivery-requires-'+type}
		);
		var payments = this.getAvailablePayments();
		if (payments.length > 0) {
			fields.push(
				{xtype: 'checkboxgroup'
					,fieldLabel: _('ms2_payments')
					,columns: 2
					,items: payments
					,id: 'minishop2-delivery-payments-'+type
				}
			);
		}
		fields.push(
			{xtype: 'textfield',fieldLabel: _('ms2_class'), name: 'class', anchor: '99%', id: 'minishop2-delivery-class-'+type}
			,{xtype: 'xcheckbox', fieldLabel: '', boxLabel: _('ms2_active'), name: 'active', id: 'minishop2-delivery-active-'+type}
		);

		return fields;
	}

	,getAvailablePayments: function() {
		var payments = [];
		var items = miniShop2.PaymentsArray;
		for (i in items) {
			if (items.hasOwnProperty(i)) {
				var id = items[i].id;
				payments.push({
					xtype: 'xcheckbox'
					,boxLabel: items[i].name
					,name: 'payments['+id+']'
					,payment_id: id
				});
			}
		}
		return payments;
	}

	,enablePayments: function(payments) {
		if (payments.length < 1) {return;}
		var checkboxgroup = Ext.getCmp('minishop2-delivery-payments-update');
		Ext.each(checkboxgroup.items.items, function(item) {
			if (payments[item.payment_id] == 1) {
				item.setValue(true);
			}
			else {
				item.setValue(false);
			}
		});
	}

	,beforeDestroy: function() {
		if (this.rendered) {
			this.dropTarget.destroy();
		}
		miniShop2.grid.Delivery.superclass.beforeDestroy.call(this);
	}
});
Ext.reg('minishop2-grid-delivery',miniShop2.grid.Delivery);




miniShop2.window.CreateDelivery = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_create')
		,id: this.ident
		,width: 600
		,autoHeight: true
		,labelAlign: 'left'
		,labelWidth: 180
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/delivery/create'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreateDelivery.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateDelivery,MODx.Window);
Ext.reg('minishop2-window-delivery-create',miniShop2.window.CreateDelivery);


miniShop2.window.UpdateDelivery = function(config) {
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
		,action: 'mgr/settings/delivery/update'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateDelivery.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateDelivery,MODx.Window);
Ext.reg('minishop2-window-delivery-update',miniShop2.window.UpdateDelivery);