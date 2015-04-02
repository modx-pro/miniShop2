miniShop2.tree.OptionCategories = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		url: miniShop2.config.connector_url
		,id: 'minishop2-option-categories-tree'
		,title: ''
		,anchor: '100%'
		,rootVisible: false
		,expandFirst: true
		,enableDD: false
		,ddGroup: 'modx-treedrop-dd'
		,remoteToolbar: false
		,action: 'mgr/settings/option/getcategorynodes'
		,tbarCfg: {id: config.id ? config.id+'-tbar' : 'modx-tree-resource-tbar'}
		,baseParams: {
			action: 'mgr/settings/option/getcategorynodes'
			,currentResource: MODx.request.id || 0
			,currentAction: MODx.request.a || 0
		}
		//,tbar: []
		,listeners: {
			checkchange: function(node, checked) {
                if (typeof this.optionGrid == 'undefined') return;
                var grid = Ext.getCmp(this.optionGrid);
                if (!grid) return;

                var checkedNodes = this.getChecked();
                var categories = [];
                for (var i = 0; i < checkedNodes.length; i++) {
                    categories.push(checkedNodes[i].attributes.pk);
                }

                var s = grid.getStore();
                s.baseParams.categories = Ext.util.JSON.encode(categories);
                grid.getBottomToolbar().changePage(1);
			}
            ,load: function(node) { // refresh grid on tree update
                this.fireEvent('checkchange', node);
            }
			,afterrender: function() {
				this.mask = new Ext.LoadMask(this.getEl());
			}
		}
	});
	miniShop2.tree.OptionCategories.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.tree.OptionCategories, MODx.tree.Tree,{

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
Ext.reg('minishop2-tree-option-categories',miniShop2.tree.OptionCategories);