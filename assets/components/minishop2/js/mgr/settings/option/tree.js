miniShop2.tree.OptionCategories = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-option-categories-tree';
    }

    Ext.applyIf(config, {
        url: miniShop2.config.connector_url,
        title: '',
        anchor: '100%',
        rootVisible: false,
        expandFirst: true,
        enableDD: false,
        remoteToolbar: false,
        action: 'mgr/settings/option/getnodes',
        baseParams: {
            categories: config['categories'] || '',
            options: config['options'] || '',
        },
        stateful: false,
        listeners: this.getListeners(config)
    });
    miniShop2.tree.OptionCategories.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.tree.OptionCategories, MODx.tree.Tree, {

    getListeners: function () {
        return {
            checkchange: function () {
                var grid = Ext.getCmp(this.optionGrid);
                if (grid) {
                    var checkedNodes = this.getChecked();
                    var categories = [];
                    for (var i = 0; i < checkedNodes.length; i++) {
                        categories.push(checkedNodes[i].attributes.pk);
                    }

                    var s = grid.getStore();
                    s.baseParams.categories = Ext.util.JSON.encode(categories);
                    grid.getBottomToolbar().changePage(1);
                }
            }
        };
    },

    _showContextMenu: function (n, e) {
        n.select();
        this.cm.activeNode = n;
        this.cm.removeAll();
        var m = [];
        m.push({
            text: '<i class="x-menu-item-icon icon icon-refresh"></i> ' + _('directory_refresh'),
            handler: function () {
                this.refreshNode(this.cm.activeNode.id, true);
            }
        });
        this.addContextMenuItem(m);
        this.cm.showAt(e.xy);
        e.stopEvent();
    },

    remove: function() {

    },

});
Ext.reg('minishop2-tree-option-categories', miniShop2.tree.OptionCategories);

/*
 miniShop2.tree.ModalCategories = function (config) {
 config = config || {};
 if (!config.id) {
 config.id = 'minishop2-modal-categories-tree';
 }

 //Ext.applyIf(config, {});
 miniShop2.tree.ModalCategories.superclass.constructor.call(this, config);
 };
 Ext.extend(miniShop2.tree.ModalCategories, miniShop2.tree.OptionCategories, {

 getListeners: function () {
 return {
 checkchange: function () {
 var checkedNodes = this.getChecked();
 var categories = [];
 for (var i = 0; i < checkedNodes.length; i++) {
 categories.push(checkedNodes[i].attributes.pk);
 }

 var catField = Ext.getCmp(this.categories);
 if (!catField) {
 return false;
 }
 catField.setValue(Ext.util.JSON.encode(categories));
 },
 afterrender: function () {
 this.mask = new Ext.LoadMask(this.getEl());
 }
 };
 }

 });
 Ext.reg('minishop2-tree-modal-categories', miniShop2.tree.ModalCategories);
 */