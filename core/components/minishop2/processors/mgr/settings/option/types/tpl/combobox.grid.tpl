<div id="type-combobox-grid"></div>
{literal}<script type="text/javascript">
miniShop2.grid.ComboboxOptions = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        fields: ['value']
        ,autoHeight: true
        ,paging: true
        ,hideHeaders: true
        ,columns:[{header: _('value'),dataIndex: 'value',width: 50}]
        ,bbar: [{
            text: _('ms2_btn_add')
            ,handler: this.addOption
            ,scope: this
        }]
    });
    miniShop2.grid.ComboboxOptions.superclass.constructor.call(this,config);
};

Ext.extend(miniShop2.grid.ComboboxOptions,MODx.grid.LocalGrid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('ms2_menu_update')
            ,handler: this.updateOption
        });
        m.push('-');
        m.push({
            text: _('ms2_menu_remove')
            ,handler: this.removeOption
        });
        this.addContextMenuItem(m);
    }

    ,createOption: function(btn,e) {
        if (!this.windows.createOption) {
            this.windows.createOption = MODx.load({
                xtype: 'minishop2-window-option-create'
                ,fields: this.getOptionFields('create')
                ,listeners: {
                    success: {fn:function() { this.refresh(); },scope:this}
                }
            });
        } else {
            var tree = Ext.getCmp('minishop2-tree-modal-categories-window-create');
            tree.getLoader().load(tree.root);
        }

        var f = this.windows.createOption.fp.getForm();
        f.reset();
        f.setValues({type: 'textfield'});
        this.windows.createOption.show(e.target);
    }

    ,removeOption: function(btn,e) {
        if (!this.menu.record) return false;

        MODx.msg.confirm({
            title: _('ms2_menu_remove') + '"' + this.menu.record.name + '"'
            ,text: _('ms2_menu_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/settings/option/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                success: {fn:function(r) {this.refresh();}, scope:this}
            }
        });
    }

});
Ext.reg('minishop2-grid-combobox-options',miniShop2.grid.ComboboxOptions);
console.log(MODx);
MODx.load({
    xtype: 'minishop2-grid-combobox-options'
    , renderTo: 'type-combobox-grid'
});
</script>
{/literal}