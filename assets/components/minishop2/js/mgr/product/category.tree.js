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
        }
    });

    Ext.apply(config, {
        listeners: this.getListeners(config)
    });

    miniShop2.tree.Categories.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.tree.Categories, MODx.tree.Tree, {

    getListeners: function () {
        return {
            checkchange: function (node, checked) {
                if (node) {
                    this._handleCheck(node.attributes.pk, checked);
                }
            }
        };
    },

    onRender: function () {
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

    _handleCheck: function (id, checked) {
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
        },{
            text: '<i class="x-menu-item-icon icon icon-level-down"></i> ' + _('expand_tree'),
            handler: function () {
                this.cm.activeNode.expand(true);
            }
        },{
            text: '<i class="x-menu-item-icon icon icon-level-up"></i> ' + _('collapse_tree'),
            handler: function () {
                this.cm.activeNode.collapse(true);
            }
        },{
            text: '<i class="x-menu-item-icon icon icon-check-square-o"></i> ' + _('ms2_menu_select_all'),
            handler: function () {
                var activeNode = this.cm.activeNode;
                var checkchange = this.getListeners().checkchange;

                function massCheck(node)
                {
                    node.getUI().toggleCheck(true);
                    node.expand(false,false,function (node) {
                        node.eachChild(massCheck);
                        if (node == activeNode) {
                            checkchange();
                        }
                    });
                }
                massCheck(activeNode);
            }
        },{
            text: '<i class="x-menu-item-icon icon icon-square-o"></i> ' + _('ms2_menu_clear_all'),
            handler: function () {
                var activeNode = this.cm.activeNode;
                var checkchange = this.getListeners().checkchange;

                function massUncheck(node)
                {
                    node.getUI().toggleCheck(false);
                    node.eachChild(massUncheck);
                    if (node == activeNode) {
                        checkchange();
                    }
                }
                massUncheck(activeNode);
            }
        });
        this.addContextMenuItem(m);
        this.cm.showAt(e.xy);
        e.stopEvent();
    },

});
Ext.reg('minishop2-tree-categories', miniShop2.tree.Categories);
