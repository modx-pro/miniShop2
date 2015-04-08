miniShop2.grid.ComboboxOptions = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        fields: ['value']
        ,autoHeight: false
        ,height: 250
        //,hideHeaders: true
       // ,anchor: '100%'
        ,viewConfig: {
            forceFit: true
        }
        ,columns:[{header: _('value'),dataIndex: 'value', editor: { xtype: 'textfield' ,allowBlank: false
            ,listeners:{
                change: {fn:this.prepareProperties, scope: this}
            }
        }}]
        ,data:[new Ext.data.Record({
            value: ''
        })]
        ,bbar: [{
            text: _('ms2_menu_add')
            ,handler: this.addOption
            ,scope: this
        },{xtype: 'hidden', id: 'hidden-properties', name: 'properties'}]
        ,bodyCssClass: 'x-menu'
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

    ,addOption: function(btn,e) {
        this.store.add(new Ext.data.Record({
            value: ''
        }));
        this.prepareProperties();
    }

    ,prepareProperties: function() {
        var properties = {values: this.store.collect('value')};
        properties = Ext.util.JSON.encode(properties);
        Ext.getCmp('hidden-properties').setValue(properties);
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
