miniShop2.page.Orders = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'minishop2-panel-orders'
			,renderTo: 'minishop2-panel-orders-div'
		}]
	});
	miniShop2.page.Orders.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.Orders,MODx.Component);
Ext.reg('minishop2-page-orders',miniShop2.page.Orders);

miniShop2.panel.Orders = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,deferredRender: true
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>'+_('minishop2') + ' :: ' + _('ms2_orders')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,id: 'minishop2-orders-tabs'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,hideMode: 'offsets'
			//,stateful: true
			//,stateId: 'minishop2-orders-tabpanel'
			//,stateEvents: ['tabchange']
			//,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: [{
				title: _('ms2_orders')
				,items: [{
					html: '<p>'+_('ms2_orders_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-orders'
					,id: 'minishop2-grid-orders'
				}]
				,listeners: {/*
					afterrender : function() {
						this.on('show', function() {
							Ext.getCmp('minishop2-grid-orders').refresh();
						});
					}
				*/}
			}]
		}]
	});
	miniShop2.panel.Orders.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Orders,MODx.Panel);
Ext.reg('minishop2-panel-orders',miniShop2.panel.Orders);