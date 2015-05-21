miniShop2.grid.ComboboxOptions = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        fields: ['dd','value','remove']
        ,autoHeight: false
       // ,autoScroll: true
        ,height: 260
        ,hideHeaders: true
        ,anchor: '100%'
        ,viewConfig: {
            forceFit: true
        }
        ,layout: 'anchor'
        ,columns:[{
            dataIndex: 'dd' ,width:10, renderer: function(){return '<div class="controlBtn sort icon icon-sort" style="color:#ccc;cursor:move;" title="'+_('sort')+'"></div>'}
        },{
            header: _('value'),dataIndex: 'value' ,editor: { xtype: 'textfield' ,allowBlank: false
            ,listeners:{
                change: {fn:this.prepareProperties, scope: this}
            }
        }},{
            header: _('remove'), dataIndex: 'remove', width: 10, align: 'center', renderer: function(){return '<a class="controlBtn delete icon icon-remove" style="color:#ff0000;cursor:pointer" title="'+_('remove')+'"></a>'}
        }]
        ,plugins: [new Ext.ux.dd.GridDragDropRowOrder({
            copy: false
            ,scrollable: true
            ,targetCfg: {}
            ,listeners: {
                'afterrowmove': {fn:this.prepareProperties,scope:this}
            }
        })]
        ,bbar: [{
            text: _('ms2_menu_add')
            ,handler: this.addOption
            ,scope: this
        },{xtype: 'hidden', id: 'hidden-properties', name: 'properties'}]
        ,bodyCssClass: 'x-menu'
        ,listeners: {
            viewready: {fn: this.prepareValues, scope: this}
            ,afteredit: {fn: this.prepareProperties, scope: this}
        }
    });
    miniShop2.grid.ComboboxOptions.superclass.constructor.call(this,config);
};

Ext.extend(miniShop2.grid.ComboboxOptions,MODx.grid.LocalGrid,{
    windows: {}

    ,prepareValues: function() {
        if (!this.record) {
            this.store.add(new Ext.data.Record({
                value: ''
            }));
            this.focusValueCell(0);
            return;
        }

        Ext.each(this.record.properties.values, function(item){
            this.store.add(new Ext.data.Record({
                value: item
            }));
        }, this);
        this.prepareProperties();
    }

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
        if (this.store.collect('value').length == this.store.data.length) {
            this.store.add(new Ext.data.Record({
                value: ''
            }));
            this.focusValueCell(this.store.data.length-1);
        } else {
            Ext.Msg.alert(_('error'), 'Вы не ввели значение или ввели повтор.', function(){
                this.focusValueCell(this.store.data.length-1);
            }, this);
        }

        this.prepareProperties();
    }

    ,prepareProperties: function(field) {
        var properties = {values: this.store.collect('value')};
        properties = Ext.util.JSON.encode(properties);
        Ext.getCmp('hidden-properties').setValue(properties);
    }

    ,onClick: function(e){
        var t = e.getTarget();
        var elm = t.className.split(' ')[0];
        if(elm == 'controlBtn') {
            var action = t.className.split(' ')[1];
            this.menu.record = this.getSelectionModel().getSelected();
            switch (action) {
                case 'delete':
                    this.removeOption(this.store, this.menu.record);
                    break;
            }
        }
        this.processEvent('click', e);
    }

    ,removeOption: function(store, record) {
        if (!store || !record) return false;

        if (store.data.length == 1) {
            store.getAt(0).set('value','');
            this.focusValueCell(0);
        } else {
            store.remove(record);
        }
        this.prepareProperties();
    }

    ,focusValueCell: function(row) {
        this.startEditing(row,1);
    }

});
Ext.reg('minishop2-grid-combobox-options',miniShop2.grid.ComboboxOptions);
