miniShop2.grid.Vendor = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{description}</p>')
		,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});

	Ext.applyIf(config,{
		id: 'minishop2-grid-vendor'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/vendor/getlist'
		}
		,fields: ['id','name','resource','country','email','logo','address','phone','fax','description']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/vendor/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ms2_name'),dataIndex: 'name',width: 100, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_resource'),dataIndex: 'resource',width: 100, editor: {xtype: 'minishop2-combo-resource'}, sortable: true, hidden: true}
			,{header: _('ms2_country'),dataIndex: 'country',width: 75, editor: {xtype: 'textfield'}, sortable: true}
			,{header: _('ms2_email'),dataIndex: 'email',width: 100, editor: {xtype: 'textfield'}, sortable: true}
			,{header: _('ms2_logo'),dataIndex: 'logo',width: 75, renderer: this.renderLogo}
			,{header: _('ms2_address'),dataIndex: 'address',width: 100, editor: {xtype: 'textarea'}}
			,{header: _('ms2_phone'),dataIndex: 'phone',width: 75, editor: {xtype: 'textfield'}}
			,{header: _('ms2_fax'),dataIndex: 'fax',width: 75, editor: {xtype: 'textfield'}}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createVendor
			,scope: this
		}]
	});
	miniShop2.grid.Vendor.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Vendor,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateVendor
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeVendor
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

	,createVendor: function(btn,e) {
		if (!this.windows.createVendor) {
			this.windows.createVendor = MODx.load({
				xtype: 'minishop2-window-vendor-create'
				,fields: this.getVendorFields('create')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.createVendor.fp.getForm().reset();
		this.windows.createVendor.show(e.target);
	}

	,updateVendor: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		if (!this.windows.updateVendor) {
			this.windows.updateVendor = MODx.load({
				xtype: 'minishop2-window-vendor-update'
				,record: r
				,fields: this.getVendorFields('update')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.updateVendor.fp.getForm().reset();
		this.windows.updateVendor.fp.getForm().setValues(r);
		this.windows.updateVendor.show(e.target);
	}

	,removeVendor: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
			,text: _('ms2_menu_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/vendor/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) {this.refresh();}, scope:this}
			}
		});
	}

	,getVendorFields: function(type) {
		return [
			{xtype: 'hidden',name: 'id', id: 'minishop2-vendor-id-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'name', allowBlank: false, anchor: '99%', id: 'minishop2-vendor-name-'+type}
			,{xtype: 'minishop2-combo-resource',fieldLabel: _('ms2_resource'), name: 'resource', anchor: '99%', id: 'minishop2-vendor-resource-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_country'), name: 'country', anchor: '99%', id: 'minishop2-vendor-country-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_email'), name: 'email', anchor: '99%', id: 'minishop2-vendor-email-'+type}
			,{xtype: 'minishop2-combo-browser',fieldLabel: _('ms2_logo'), name: 'logo', anchor: '99%',  id: 'minishop2-vendor-logo-'+type}
			,{xtype: 'textarea',fieldLabel: _('ms2_address'), name: 'address', anchor: '99%', id: 'minishop2-vendor-address-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_phone'), name: 'phone', anchor: '99%', id: 'minishop2-vendor-phone-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_fax'), name: 'fax', anchor: '99%', id: 'minishop2-vendor-fax-'+type}
			,{xtype: 'textarea', fieldLabel: _('ms2_description'), name: 'description', anchor: '99%', id: 'minishop2-vendor-description-'+type}
		];
	}

});
Ext.reg('minishop2-grid-vendor',miniShop2.grid.Vendor);




miniShop2.window.CreateVendor = function(config) {
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
		,action: 'mgr/settings/vendor/create'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreateVendor.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateVendor,MODx.Window);
Ext.reg('minishop2-window-vendor-create',miniShop2.window.CreateVendor);


miniShop2.window.UpdateVendor = function(config) {
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
		,action: 'mgr/settings/vendor/update'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateVendor.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateVendor,MODx.Window);
Ext.reg('minishop2-window-vendor-update',miniShop2.window.UpdateVendor);
