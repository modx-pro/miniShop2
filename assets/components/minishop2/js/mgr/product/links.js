miniShop2.grid.ProductLinks = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: this.ident
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/product/prodictlink/getlist'
		}
		,fields: ['id','master','slave','master_pagetitle','slave_pagetitle','type']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			,{header: _('ms2_id'),dataIndex: 'id',hidden: true,sortable: true,width: 35}
			,{header: _('ms2_type'),dataIndex: 'gid',hidden: true,sortable: true,width: 35}
			,{header: _('ms2_master'),dataIndex: 'master_pagetitle',hidden: true,sortable: true,width: 35}
			,{header: _('ms2_slave'),dataIndex: 'slave_pagetitle',hidden: true,sortable: true,width: 35}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createProductLink
			,scope: this
		}]
	});
	miniShop2.grid.ProductLinks.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.ProductLinks,MODx.grid.Grid, {

	getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeProductLink
		});
		this.addContextMenuItem(m);
	}

	,createProductLink: function(btn,e) {
		var w = MODx.load({
			xtype: 'minishop-window-orderedgoods'
			,title: _('ms.orderedgoods.add')
			,oid: this.oid
			,newrecord: 1
			,listeners: {
				'success': {fn:function() { this.refresh(); },scope:this}
			}
		});
		w.show(e.target,function() {w.setPosition(null,100)},this);
	}

	,removeProductLink: function(btn,e) {
		if (!this.menu.record) return false;
		MODx.msg.confirm({
			title: _('ms2_menu_remove')
			,text: _('ms2_menu_remove_confirm')
			,url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/product/productlink/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) { this.refresh(); },scope:this}
			}
		});
	}
});

Ext.reg('minishop2-product-links',miniShop2.grid.ProductLinks);