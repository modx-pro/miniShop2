miniShop2.tree.FeatureCategories = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		url: miniShop2.config.connector_url
		,id: 'minishop2-feature-categories-tree'
		,title: ''
		,anchor: '100%'
		,rootVisible: false
		,expandFirst: true
		,enableDD: false
		,ddGroup: 'modx-treedrop-dd'
		,remoteToolbar: false
		,action: 'mgr/settings/feature/getcategorynodes'
		,tbarCfg: {id: config.id ? config.id+'-tbar' : 'modx-tree-resource-tbar'}
		,baseParams: {
			action: 'mgr/settings/feature/getcategorynodes'
			,currentResource: MODx.request.id || 0
			,currentAction: MODx.request.a || 0
		}
		//,tbar: []
		,listeners: {
			checkchange: function(node, checked) {
				this.mask.show();
				MODx.Ajax.request({
					url: miniShop2.config.connector_url
					,params: {
						action: 'mgr/settings/feature/applycategory'
						,category_id: node.attributes.pk
						,feature_id: MODx.request.id || 0
					}
					,listeners: {
						success: {fn: function() {this.mask.hide();}, scope:this}
						,failure: {fn: function() {this.mask.hide();}, scope:this}
					}
				});
			}
			,afterrender: function() {
				this.mask = new Ext.LoadMask(this.getEl());
			}
		}
	});
	miniShop2.tree.FeatureCategories.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.tree.FeatureCategories, MODx.tree.Tree,{

	_showContextMenu: function(n,e) {
		n.select();
		this.cm.activeNode = n;
		this.cm.removeAll();
		var m = [];
		m.push({text: _('directory_refresh'),handler: function() {this.refreshNode(this.cm.activeNode.id,true);}});
		this.addContextMenuItem(m);
		this.cm.showAt(e.xy);
		e.stopEvent();
	}

});
Ext.reg('minishop2-tree-feature-categories',miniShop2.tree.FeatureCategories);