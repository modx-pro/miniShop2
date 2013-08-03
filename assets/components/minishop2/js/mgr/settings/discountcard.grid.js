miniShop2.grid.DiscountCard = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{description}</p>')
		,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});
// Ext.Ajax.request({
// 	url: 'foo.php',
// 	success: function (){alert('All good!');},
// 	failure: function (){alert('Fail...');},
// 	headers: {
// 		'my-header': 'foo'
// 	},
// 	params: { foo: 'bar' }
// });
	Ext.applyIf(config,{
		id: 'minishop2-grid-discountcard'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/discountcard/getlist'
		}
		,fields: ['id','uid','discount_id','public','amount','amount_used','owner']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/discountcard/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_id'),dataIndex: 'id',width: 30}
			,{header: _('ms2_discountcard_uid'),dataIndex: 'uid',width: 130, editor: {xtype: 'numberfield', allowBlank: false}}
			,{header: _('ms2_discount_length'),dataIndex: 'discount_id',width: 130, editor: {xtype: 'minishop2-combo-discount', allowBlank: false}}
			,{header: _('ms2_discountcard_public'),dataIndex: 'public',width: 105, editor: {xtype: 'combo-boolean', renderer: 'boolean'}}
			,{header: _('ms2_discountcard_amount'),dataIndex: 'amount',width: 130, editor: {xtype: 'numberfield', allowBlank: false}}
			,{header: _('ms2_discountcard_amount_used'),dataIndex: 'amount_used',width: 130, editor: {xtype: 'numberfield', allowBlank: false}}
			,{header: _('ms2_discountcard_owner'),dataIndex: 'owner',width: 190, editor: {xtype: 'minishop2-combo-user'}}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createDiscountCard
			,scope: this
		}]
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
		// this.enablePayments(r.payments);
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
			,{xtype: 'numberfield',fieldLabel: _('ms2_discountcard_uid'), name: 'uid', allowBlank: false, anchor: '99%', id: 'minishop2-discountcard-uid-'+type}
			,{xtype: 'minishop2-combo-discount',fieldLabel: _('ms2_discount_length'), name: 'discount_id', allowBlank: false, decimalPrecision: 3, anchor: '50%', id: 'minishop2-discountcard-discount_id-'+type}
			,{xtype: 'combo-boolean',fieldLabel: _('ms2_discountcard_public'),  name: 'public', allowBlank: true, anchor: '50%', id: 'minishop2-discountcard-public-'+type}
			,{xtype: 'numberfield',fieldLabel: _('ms2_discountcard_amount'), name: 'amount', decimalPrecision: 10, allowBlank: true, anchor: '50%', id: 'minishop2-discountcard-amount-'+type}
			,{xtype: 'numberfield',fieldLabel: _('ms2_discountcard_amount_used'), name: 'amount_used', decimalPrecision: 10, allowBlank: true, anchor: '50%', id: 'minishop2-discountcard-amount_used-'+type}
			,{xtype: 'minishop2-combo-user',fieldLabel: _('ms2_discountcard_owner'), name: 'owner', anchor: '99%',  id: 'minishop2-discountcard-owner-'+type}
			,{xtype: 'minishop2-combo-users',fieldLabel: _('ms2_discountcard_coowners'), name: 'coowners', anchor: '99%',  id: 'minishop2-discountcard-coowners-'+type}
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