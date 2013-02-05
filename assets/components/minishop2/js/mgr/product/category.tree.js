miniShop2.tree.Categories = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		url: miniShop2.config.connector_url
		,title: ''
		,rootVisible: false
		,expandFirst: true
		,enableDD: false
		,ddGroup: 'modx-treedrop-dd'
		,remoteToolbar: false
		,action: 'mgr/category/getnodes'
		,tbarCfg: {
			id: config.id ? config.id+'-tbar' : 'modx-tree-resource-tbar'
		}
		,baseParams: {
			action: 'mgr/category/getnodes'
			,currentResource: MODx.request.id || 0
			,currentAction: MODx.request.a || 0
		}
		/*
		,buttons: [{
			text: 'Get Completed Tasks'
			,handler: function(){
				var msg = '', selNodes = tree.getChecked();
				Ext.each(selNodes, function(node){
					if(msg.length > 0){
						msg += ', ';
					}
					msg += node.text;
				});
				Ext.Msg.show({
					title: 'Completed Tasks',
					msg: msg.length > 0 ? msg : 'None',
					icon: Ext.Msg.INFO,
					minWidth: 200,
					buttons: Ext.Msg.OK
				});
			}
		}]
		*/
	});
	miniShop2.tree.Categories.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.tree.Categories, MODx.tree.Tree);
Ext.reg('minishop2-tree-categories',miniShop2.tree.Categories);