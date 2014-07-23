miniShop2.grid.Link = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{description}</p>')
		,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});

	Ext.applyIf(config,{
		id: 'minishop2-grid-link'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/link/getlist'
		}
		,fields: ['id','type','name','description']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/link/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ms2_name'),dataIndex: 'name',width: 150, editor: {xtype: 'textfield', allowBlank: false}, sortable: true}
			,{header: _('ms2_type'),dataIndex: 'type',width: 100, renderer: this.renderType}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createLink
			,scope: this
		}]
	});
	miniShop2.grid.Link.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Link,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updateLink
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removeLink
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

	,renderType: function(value) {
		return _('ms2_link_'+value);
	}

	,createLink: function(btn,e) {
		if (!this.windows.createLink) {
			this.windows.createLink = MODx.load({
				xtype: 'minishop2-window-link-create'
				,fields: this.getLinkFields('create')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.createLink.fp.getForm().reset();
		this.windows.createLink.show(e.target);
		Ext.getCmp('minishop2-link-type_desc-create').getEl().dom.innerText = '';
	}

	,updateLink: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		if (!this.windows.updateLink) {
			this.windows.updateLink = MODx.load({
				xtype: 'minishop2-window-link-update'
				,record: r
				,fields: this.getLinkFields('update')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.updateLink.fp.getForm().reset();
		this.windows.updateLink.fp.getForm().setValues(r);
		this.windows.updateLink.show(e.target);
		Ext.getCmp('minishop2-link-type_desc-update').getEl().dom.innerText = r.type ? _('ms2_link_'+r.type+'_desc') : '';
	}

	,removeLink: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
			,text: _('ms2_menu_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/link/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) {this.refresh();}, scope:this}
			}
		});
	}

	,getLinkFields: function(type) {
		return [
			{xtype: 'hidden',name: 'id', id: 'minishop2-link-id-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'name', allowBlank: false, anchor: '99%', id: 'minishop2-link-name-'+type}
			,{xtype: 'minishop2-combo-link-type',fieldLabel: _('ms2_type'), name: 'type', allowBlank: false, anchor: '99%', id: 'minishop2-link-type-'+type
				,listeners: {
					select: function(combo,row,value) {
						Ext.getCmp('minishop2-link-type_desc-'+type).getEl().dom.innerText = row.data.description;
					}
				}
				,disabled: type == 'update' ? 1 : 0
			}
			,{html: '',id: 'minishop2-link-type_desc-'+type,
				style: 'font-style: italic; padding: 10px; color: #555555;'
			}
			,{xtype: 'textarea', fieldLabel: _('ms2_description'), name: 'description', anchor: '99%', id: 'minishop2-link-description-'+type}
		];
	}

});
Ext.reg('minishop2-grid-link',miniShop2.grid.Link);




miniShop2.window.CreateLink = function(config) {
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
		,action: 'mgr/settings/link/create'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreateLink.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreateLink,MODx.Window);
Ext.reg('minishop2-window-link-create',miniShop2.window.CreateLink);


miniShop2.window.UpdateLink = function(config) {
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
		,action: 'mgr/settings/link/update'
		,fields: config.fields
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdateLink.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdateLink,MODx.Window);
Ext.reg('minishop2-window-link-update',miniShop2.window.UpdateLink);