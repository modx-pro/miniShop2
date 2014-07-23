miniShop2.grid.ProductLinks = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{description}</p>')
		,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});

	Ext.applyIf(config,{
		id: 'minishop2-grid-product-link'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/product/productlink/getlist'
			,master: config.record.id
		}
		,fields: ['id','link','type','name','master','slave','master_pagetitle','slave_pagetitle','description']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_link_name'), dataIndex: 'name',width: 75, sortable: true}
			,{header: _('ms2_type'), dataIndex: 'type',width: 75, sortable: true, renderer: this.renderType}
			,{header: _('ms2_link_master'), dataIndex: 'master_pagetitle',width: 125,  sortable: true, renderer: this.renderMaster}
			,{header: _('ms2_link_slave'), dataIndex: 'slave_pagetitle',width: 125,  sortable: true, renderer: this.renderSlave}
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

	,renderType: function(value) {
		return _('ms2_link_'+value);
	}

	,renderMaster: function(value, cell, row) {
		if (row.data.master == MODx.request.id) {
			return value;
		}
		else {
			row.data['product_id'] = row.data.master;
			return miniShop2.utils.productLink(value, cell, row);
		}
	}

	,renderSlave: function(value, cell, row) {
		if (row.data.slave == MODx.request.id) {
			return value;
		}
		else {
			row.data['product_id'] = row.data.slave;
			return miniShop2.utils.productLink(value, cell, row);
		}
	}

	,createProductLink: function(btn,e) {
		if (!this.windows.createProductLink) {
			this.windows.createProductLink = MODx.load({
				xtype: 'minishop2-window-product-link-create'
				,title: _('ms2_btn_create')
				,fields: this.getProductLinkFields('create')
				,baseParams: {
					action: 'mgr/product/productlink/create'
					,master: btn.scope.record.id
				}
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		//this.windows.createProductLink.fp.getForm().reset();
		Ext.getCmp('minishop2-link-slave-create').reset();
		this.windows.createProductLink.show(e.target);
	}

	,removeProductLink: function(btn,e) {
		if (this.menu.record) {
			MODx.msg.confirm({
				title: _('ms2_menu_remove')
				,text: _('ms2_menu_remove_confirm')
				,url: miniShop2.config.connector_url
				,params: {
					action: 'mgr/product/productlink/remove'
					,link: this.menu.record.link
					,master: this.menu.record.master
					,slave: this.menu.record.slave
				}
				,listeners: {
					success: {fn:function(r) { this.refresh(); },scope:this}
				}
			});
		}
	}

	,getProductLinkFields: function(type) {
		return [
			{xtype: 'minishop2-combo-link',fieldLabel: _('ms2_link'), name: 'link', allowBlank: false, anchor: '99%', id: 'minishop2-link-name-'+type}
			,{xtype: 'minishop2-combo-product',fieldLabel: _('ms2_product'), name: 'slave', hiddenName: 'slave', allowBlank: false, anchor: '99%', id: 'minishop2-link-slave-'+type}
		];
	}

});
Ext.reg('minishop2-product-links-grid',miniShop2.grid.ProductLinks);


miniShop2.window.CreateProductLink = function(config) {
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
		,action: 'mgr/product/productlink/create'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreateProductLink.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateProductLink,MODx.Window);
Ext.reg('minishop2-window-product-link-create',miniShop2.window.CreateProductLink);