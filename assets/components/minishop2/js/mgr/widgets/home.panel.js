miniShop2.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>'+_('minishop2')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,activeItem: 0
			,hideMode: 'offsets'
			,items: [{
				title: _('minishop2.items')
				,items: [{
					html: _('minishop2.intro_msg')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-items'
					,preventRender: true
				}]
			}]
		}]
	});
	miniShop2.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Home,MODx.Panel);
Ext.reg('minishop2-panel-home',miniShop2.panel.Home);
