miniShop2.grid.Payment = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'minishop2-grid-payment'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/payment/getlist'
		}
		,fields: ['id','name','description']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			{header: _('id'),dataIndex: 'id',width: 70}
			,{header: _('name'),dataIndex: 'name',width: 200}
			,{header: _('description'),dataIndex: 'description',width: 250}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createItem
			,scope: this
		}]
	});
	miniShop2.grid.Payment.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Payment,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('modextra.item_update')
			,handler: this.updateItem
		});
		m.push('-');
		m.push({
			text: _('modextra.item_remove')
			,handler: this.removeItem
		});
		this.addContextMenuItem(m);
	}

	,createItem: function(btn,e) {
		if (!this.windows.createItem) {
			this.windows.createItem = MODx.load({
				xtype: 'minishop2-window-payment-create'
				,listeners: {
					'success': {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.createItem.fp.getForm().reset();
		this.windows.createItem.show(e.target);
	}
	,updateItem: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		if (!this.windows.updateItem) {
			this.windows.updateItem = MODx.load({
				xtype: 'minishop2-window-payment-update'
				,record: r
				,listeners: {
					'success': {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.updateItem.fp.getForm().reset();
		this.windows.updateItem.fp.getForm().setValues(r);
		this.windows.updateItem.show(e.target);
	}

	,removeItem: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('modextra.item_remove')
			,text: _('modextra.item_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/payment/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) { this.refresh(); },scope:this}
			}
		});
	}
});
Ext.reg('minishop2-grid-payment',miniShop2.grid.Payment);




miniShop2.window.CreateItem = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('modextra.item_create')
		,id: this.ident
		,height: 150
		,width: 475
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/payment/create'
		,fields: [
			{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'minishop2-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'minishop2-'+this.ident+'-description',width: 300}
		]
	});
	miniShop2.window.CreateItem.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateItem,MODx.Window);
Ext.reg('minishop2-window-payment-create',miniShop2.window.CreateItem);


miniShop2.window.UpdateItem = function(config) {
	config = config || {};
	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('modextra.item_update')
		,id: this.ident
		,height: 150
		,width: 475
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/payment/update'
		,fields: [
			{xtype: 'hidden',name: 'id',id: 'minishop2-'+this.ident+'-id'}
			,{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'minishop2-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'minishop2-'+this.ident+'-description',width: 300}
		]
	});
	miniShop2.window.UpdateItem.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateItem,MODx.Window);
Ext.reg('minishop2-window-payment-update',miniShop2.window.UpdateItem);