miniShop2.grid.DiscountCard = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{description}</p>')
		,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});
	// this.dd = function(grid) {
	// 	new Ext.dd.DropTarget(grid.container, {
	// 		ddGroup : 'dd',
	// 		copy:false,
	// 		notifyDrop : function(dd, e, data) {
	// 			var store = grid.store.data.items;
	// 			var target = store[dd.getDragData(e).rowIndex].id;
	// 			var source = store[data.rowIndex].id;
	// 			if (target != source) {
	// 				dd.el.mask(_('loading'),'x-mask-loading');
	// 				MODx.Ajax.request({
	// 					url: miniShop2.config.connector_url
	// 					,params: {
	// 						action: config.action || 'mgr/settings/discountcard/sort'
	// 						,source: source
	// 						,target: target
	// 					}
	// 					,listeners: {
	// 						success: {fn:function(r) {dd.el.unmask();grid.refresh();},scope:grid}
	// 						,failure: {fn:function(r) {dd.el.unmask();},scope:grid}
	// 					}
	// 				});
	// 			}
	// 		}
	// 	});
	// };

	Ext.applyIf(config,{
		id: 'minishop2-grid-discountcard'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/discountcard/getlist'
		}
		,fields: ['id','uid','public','owner','coowners_count']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/discountcard/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_id'),dataIndex: 'id',width: 30}
			,{header: _('ms2_discountcard_uid'),dataIndex: 'uid',width: 130, editor: {xtype: 'textfield', allowBlank: false}}
			,{header: _('ms2_discountcard_public'),dataIndex: 'public',width: 105, editor: {xtype: 'combo-boolean', renderer: 'boolean'}}
			,{header: _('ms2_discountcard_owner'),dataIndex: 'owner',width: 190, editor: {xtype: 'minishop2-combo-user'}}
			,{header: _('ms2_discountcard_coowners_count'),dataIndex: 'coowners_count',width: 185}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createDiscountCard
			,scope: this
		}]
		// ,ddGroup: 'dd'
		// ,enableDragDrop: true
		// ,listeners: {render: {fn: this.dd, scope: this}}
	});
	miniShop2.grid.DiscountCard.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.DiscountCard,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateDiscountCard
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeDiscountCard
		});
		this.addContextMenuItem(m);
	}

	// ,renderCrosstick: function (value) {
	// 	return (parseInt(value, 10)) 
	// 		? '<span class="glyphicon glyphicon-ok"></span>'
	// 		: '<span class="glyphicon glyphicon-remove"></span>';
	// }
	
	,renderDiscountCardType: function (value) {
		return _('ms2_discountcard_type_'+value);
	}

	,createDiscountCard: function(btn,e) {
		var w = Ext.getCmp('minishop2-window-discountcard-create');
		if (w) {w.hide().getEl().remove();}
		//if (!this.windows.createDiscountCard) {
			this.windows.createDiscountCard = MODx.load({
				xtype: 'minishop2-window-discountcard-create'
				,id: 'minishop2-window-discountcard-create'
				,fields: this.getDiscountCardFields('create', {})
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
					,hide: {fn:function() {this.getEl().remove()}}
				}
			});
		//}
		this.windows.createDiscountCard.fp.getForm().reset();
		this.windows.createDiscountCard.show(e.target);
	}
	,updateDiscountCard: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		var w = Ext.getCmp('minishop2-window-discountcard-update');
		if (w) {w.hide().getEl().remove();}
		//if (!this.windows.updateDiscountCard) {
			this.windows.updateDiscountCard = MODx.load({
				xtype: 'minishop2-window-discountcard-update'
				,id: 'minishop2-window-discountcard-update'
				,record: r
				,fields: this.getDiscountCardFields('update', r)
				,listeners: {
					success: {fn:function() {this.refresh();},scope:this}
					,hide: {fn:function() {this.getEl().remove()}}
				}
			});
		//}
		this.windows.updateDiscountCard.fp.getForm().reset();
		this.windows.updateDiscountCard.fp.getForm().setValues(r);
		this.windows.updateDiscountCard.show(e.target);
		this.enablePayments(r.payments);
	}

	,removeDiscountCard: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
			,text: _('ms2_menu_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/discountcard/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) {this.refresh();}, scope:this}
			}
		});
	}

	,getDiscountCardFields: function(type) {
		var fields = [];
		fields.push({xtype: 'hidden',name: 'id', id: 'minishop2-discountcard-id-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'name', allowBlank: false, anchor: '99%', id: 'minishop2-discountcard-name-'+type}
			,{xtype: 'numberfield',fieldLabel: _('ms2_price'), name: 'price', description: _('ms2_price_help') ,allowBlank: false, decimalPrecision: 2, anchor: '50%', id: 'minishop2-discountcard-price-'+type}
			,{xtype: 'numberfield',fieldLabel: _('ms2_weight_price'), description: _('ms2_weight_price_help'), name: 'weight_price', decimalPrecision: 2, allowBlank: true, anchor: '50%', id: 'minishop2-discountcard-weight_price-'+type}
			,{xtype: 'numberfield',fieldLabel: _('ms2_distance_price'), description: _('ms2_distance_price_help'), name: 'distance_price', decimalPrecision: 2, allowBlank: true, anchor: '50%', id: 'minishop2-discountcard-distance_price-'+type}
			,{xtype: 'minishop2-combo-browser',fieldLabel: _('ms2_logo'), name: 'logo', anchor: '99%',  id: 'minishop2-discountcard-logo-'+type}
			,{xtype: 'textarea', fieldLabel: _('ms2_description'), name: 'description', anchor: '99%', id: 'minishop2-discountcard-description-'+type}
			,{xtype: 'textfield', fieldLabel: _('ms2_order_requires'), description: _('ms2_order_requires_help'), name: 'requires',anchor: '99%', id: 'minishop2-discountcard-requires-'+type}
		);
		var payments = this.getAvailablePayments();
		if (payments.length > 0) {
			fields.push(
				{xtype: 'checkboxgroup'
					,fieldLabel: _('ms2_payments')
					,columns: 2
					,items: payments
					,id: 'minishop2-discountcard-payments-'+type
				}
			);
		}
		fields.push(
			{xtype: 'textfield',fieldLabel: _('ms2_class'), name: 'class', anchor: '99%', id: 'minishop2-discountcard-class-'+type}
			,{xtype: 'xcheckbox', fieldLabel: '', boxLabel: _('ms2_active'), name: 'active', id: 'minishop2-discountcard-active-'+type}
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
		var checkboxgroup = Ext.getCmp('minishop2-discountcard-payments-update');
		Ext.each(checkboxgroup.items.items, function(item) {
			if (payments[item.payment_id] == 1) {
				item.setValue(true);
			}
			else {
				item.setValue(false);
			}
		});
	}
});
Ext.reg('minishop2-grid-discountcard',miniShop2.grid.DiscountCard);




miniShop2.window.CreateDiscountCard = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_create')
		,id: this.ident
		,width: 600
		,labelAlign: 'left'
		,labelWidth: 180
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/discountcard/create'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreateDiscountCard.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateDiscountCard,MODx.Window);
Ext.reg('minishop2-window-discountcard-create',miniShop2.window.CreateDiscountCard);


miniShop2.window.UpdateDiscountCard = function(config) {
	config = config || {};
	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_update')
		,id: this.ident
		,width: 600
		,labelAlign: 'left'
		,labelWidth: 180
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/discountcard/update'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateDiscountCard.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateDiscountCard,MODx.Window);
Ext.reg('minishop2-window-discountcard-update',miniShop2.window.UpdateDiscountCard);