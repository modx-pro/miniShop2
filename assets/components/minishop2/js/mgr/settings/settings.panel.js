Ext.onReady(function() {
	MODx.load({ xtype: 'minishop2-panel-settings'});
});

miniShop2.page.Settings = function(config) {
	config = config || {};
	Ext.apply(config,{
		renderTo: 'minishop2-panel-settings-div'
		,border: false
		,deferredRender: true
		,baseCls: 'modx-formpanel'
		,cls: 'container'
		,items: [{
			html: '<h2>'+_('ms2_settings')+'</h2>'
			,border: false
			,cls: 'modx-page-header'
		},{
			xtype: 'modx-tabs'
			,bodyStyle: 'padding: 5px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,hideMode: 'offsets'
			,stateful: true
			,stateId: 'minishop2-settings-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: [{
				title: _('ms2_deliveries')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_deliveries_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-delivery'
				}]
			},{
				title: _('ms2_payments')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_payments_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-payment'
				}]
			},{
				title: _('ms2_vendors')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_vendors_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-vendor'
				}]
			},{
				title: _('ms2_statuses')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_statuses_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-status'
				}]
			}]
		}]
	});
	miniShop2.page.Settings.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.Settings,MODx.Panel);
Ext.reg('minishop2-panel-settings',miniShop2.page.Settings);