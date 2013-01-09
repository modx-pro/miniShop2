miniShop2.grid.Items = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'minishop2-grid-items'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/item/getlist'
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
			text: _('minishop2.item_create')
			,handler: this.createItem
			,scope: this
		}]
	});
	miniShop2.grid.Items.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Items,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('minishop2.item_update')
			,handler: this.updateItem
		});
		m.push('-');
		m.push({
			text: _('minishop2.item_remove')
			,handler: this.removeItem
		});
		this.addContextMenuItem(m);
	}
	
	,createItem: function(btn,e) {
		if (!this.windows.createItem) {
			this.windows.createItem = MODx.load({
				xtype: 'minishop2-window-item-create'
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
				xtype: 'minishop2-window-item-update'
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
			title: _('minishop2.item_remove')
			,text: _('minishop2.item_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/item/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) { this.refresh(); },scope:this}
			}
		});
	}
});
Ext.reg('minishop2-grid-items',miniShop2.grid.Items);




miniShop2.window.CreateItem = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('minishop2.item_create')
		,id: this.ident
		,height: 150
		,width: 475
		,url: miniShop2.config.connector_url
		,action: 'mgr/item/create'
		,fields: [
			{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'minishop2-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'minishop2-'+this.ident+'-description',width: 300}
		]
	});
	miniShop2.window.CreateItem.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateItem,MODx.Window);
Ext.reg('minishop2-window-item-create',miniShop2.window.CreateItem);


miniShop2.window.UpdateItem = function(config) {
	config = config || {};
	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('minishop2.item_update')
		,id: this.ident
		,height: 150
		,width: 475
		,url: miniShop2.config.connector_url
		,action: 'mgr/item/update'
		,fields: [
			{xtype: 'hidden',name: 'id',id: 'minishop2-'+this.ident+'-id'}
			,{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'minishop2-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'minishop2-'+this.ident+'-description',width: 300}
		]
	});
	miniShop2.window.UpdateItem.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateItem,MODx.Window);
Ext.reg('minishop2-window-item-update',miniShop2.window.UpdateItem);