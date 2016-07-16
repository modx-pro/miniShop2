miniShop2.tree.Categories = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-categories-tree';
    }

    Ext.applyIf(config, {
        url: miniShop2.config.connector_url,
        title: '',
        name: 'categories',
        anchor: '100%',
        rootVisible: false,
        expandFirst: true,
        enableDD: false,
        remoteToolbar: false,
        action: 'mgr/category/getnodes',
        baseParams: {
            parent: config.parent || 0,
            resource: config.resource || 0,
        },
    });

    Ext.apply(config, {
        listeners: {
            checkchange: function (node, checked) {
                this._handleCheck(node.attributes.pk, checked);
            },
        }
    });
    miniShop2.tree.Categories.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.tree.Categories, MODx.tree.Tree, {

    onRender: function() {
        MODx.tree.Tree.superclass.onRender.apply(this, arguments);
        this.wrap = this.el.wrap({
            id: this.id + '-wrap'
        });

        this.input = this.wrap.createChild({
            tag: 'input',
            type: 'hidden',
            name: this.name,
            value: '{}',
            id: this.id + '-categories'
        });
    },

    _handleCheck: function(id, checked) {
        var value = Ext.util.JSON.decode(this.input.getAttribute('value'));
        value[id] = Number(checked);

        this.input.set({
            'value': Ext.util.JSON.encode(value)
        });
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

});
Ext.reg('minishop2-tree-categories', miniShop2.tree.Categories);