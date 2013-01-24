Ext.onReady(function() {
	MODx.load({ xtype: 'minishop2-page-home'});
});

miniShop2.page.Home = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'minishop2-panel-home'
			,renderTo: 'minishop2-panel-home-div'
		}]
	}); 
	miniShop2.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.Home,MODx.Component);
Ext.reg('minishop2-page-home',miniShop2.page.Home);