miniShop2.grid.Payment = function(config) {
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
							action: config.action || 'mgr/settings/payment/sort'
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
		id: 'minishop2-grid-payment'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/settings/payment/getlist'
		}
		,fields: ['id','name','description','price','logo','rank','active','class']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,save_action: 'mgr/settings/payment/updatefromgrid'
		,autosave: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_id'),dataIndex: 'id',width: 50}
			,{header: _('ms2_name'),dataIndex: 'name',width: 100, editor: {xtype: 'textfield', allowBlank: false}}
			,{header: _('ms2_add_cost'),dataIndex: 'price',width: 50, editor: {xtype: 'textfield'}}
			,{header: _('ms2_logo'),dataIndex: 'logo',width: 75, renderer: this.renderLogo}
			,{header: _('ms2_active'),dataIndex: 'active',width: 50, editor: {xtype: 'combo-boolean', renderer: 'boolean'}}
			,{header: _('ms2_class'),dataIndex: 'class',width: 75, editor: {xtype: 'textfield'}}
		]
		,tbar: [{
			text: _('ms2_btn_create')
			,handler: this.createPayment
			,scope: this
		}]
		,ddGroup: 'dd'
		,enableDragDrop: true
		,listeners: {render: {fn: this.dd, scope: this}}
	});
	miniShop2.grid.Payment.superclass.constructor.call(this,config);

	this.store.on('load', function(store) {
		miniShop2.PaymentsArray = [];
		var items = store.data.items;
		for (i in items) {
			if (items.hasOwnProperty(i) ) {
				miniShop2.PaymentsArray.push({id: items[i].id, name: items[i].data.name})
			}
		}
	})
};
Ext.extend(miniShop2.grid.Payment,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_update')
			,handler: this.updatePayment
		});
		m.push('-');
		m.push({
			text: _('ms2_menu_remove')
			,handler: this.removePayment
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

	,createPayment: function(btn,e) {
		if (!this.windows.createPayment) {
			this.windows.createPayment = MODx.load({
				xtype: 'minishop2-window-payment-create'
				,fields: this.getPaymentFields('create')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.createPayment.fp.getForm().reset();
		this.windows.createPayment.show(e.target);
	}
	,updatePayment: function(btn,e) {
		if (!this.menu.record || !this.menu.record.id) return false;
		var r = this.menu.record;

		if (!this.windows.updatePayment) {
			this.windows.updatePayment = MODx.load({
				xtype: 'minishop2-window-payment-update'
				,record: r
				,fields: this.getPaymentFields('update')
				,listeners: {
					success: {fn:function() { this.refresh(); },scope:this}
				}
			});
		}
		this.windows.updatePayment.fp.getForm().reset();
		this.windows.updatePayment.fp.getForm().setValues(r);
		this.windows.updatePayment.show(e.target);
	}

	,removePayment: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
			,text: _('ms2_menu_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/settings/payment/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) {this.refresh();}, scope:this}
			}
		});
	}

	,getPaymentFields: function(type) {
		return [
			{xtype: 'hidden',name: 'id', id: 'minishop2-payment-id-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_name'), name: 'name', allowBlank: false, anchor: '99%', id: 'minishop2-payment-name-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_add_cost'), name: 'price', description: _('ms2_add_cost_help') ,allowBlank: true, anchor: '50%', id: 'minishop2-payment-price-'+type}
			,{xtype: 'minishop2-combo-browser',fieldLabel: _('ms2_logo'), name: 'logo', anchor: '99%',  id: 'minishop2-payment-logo-'+type}
			,{xtype: 'textarea', fieldLabel: _('ms2_description'), name: 'description', anchor: '99%', id: 'minishop2-payment-description-'+type}
			,{xtype: 'textfield',fieldLabel: _('ms2_class'), name: 'class', anchor: '99%', id: 'minishop2-payment-class-'+type}
			,{xtype: 'xcheckbox', fieldLabel: '', boxLabel: _('ms2_active'), name: 'active', id: 'minishop2-payment-active-'+type}
		];
	}

	,beforeDestroy: function() {
		if (this.rendered) {
			this.dropTarget.destroy();
		}
		miniShop2.grid.Payment.superclass.beforeDestroy.call(this);
	}
});
Ext.reg('minishop2-grid-payment',miniShop2.grid.Payment);




miniShop2.window.CreatePayment = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_create')
		,width: 600
		,autoHeight: true
		,labelAlign: 'left'
		,labelWidth: 180
		,url: miniShop2.config.connector_url
		,action: 'mgr/settings/payment/create'
		,fields: [
			{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'minishop2-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'minishop2-'+this.ident+'-description',width: 300}
		]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.CreatePayment.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.CreatePayment,MODx.Window);
Ext.reg('minishop2-window-payment-create',miniShop2.window.CreatePayment);


miniShop2.window.UpdatePayment = function(config) {
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
		,action: 'mgr/settings/payment/update'
		,fields: [
			{xtype: 'hidden',name: 'id',id: 'minishop2-'+this.ident+'-id'}
			,{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'minishop2-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'minishop2-'+this.ident+'-description',width: 300}
		]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	miniShop2.window.UpdatePayment.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.window.UpdatePayment,MODx.Window);
Ext.reg('minishop2-window-payment-update',miniShop2.window.UpdatePayment);