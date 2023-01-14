miniShop2.grid.ComboboxColors = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-combobox-colors';
    }

    Ext.applyIf(config, {
        autoHeight: false,
        height: 200,
        style: 'padding-top: 5px;',
        hideHeaders: true,
        anchor: '100%',
        layout: 'anchor',
        viewConfig: {
            forceFit: true
        },
        fields: ['dd', 'value', 'name', 'remove'],
        columns: this.getColumns(config),
        plugins: this.getPlugins(config),
        listeners: this.getListeners(config),
        bbar: this.getBottomBar(config),
        bodyCssClass: 'x-menu',
        cls: 'minishop2-grid',
    });
    miniShop2.grid.ComboboxColors.superclass.constructor.call(this, config);
};
var nameCell = '';
var existcolors = [
    {"name": "Черный", "value": "#000000"},
    {"name": "Белый", "value": "#FFFFFF"},
    {"name": "Красный", "value": "#FF0000"},
    {"name": "Оранжевый", "value": "#ffa500"},
    {"name": "Желтый", "value": "#FFFF00"},
    {"name": "Зеленый", "value": "#008000"},
    {"name": "Голубой", "value": "#42aaff"},
    {"name": "Синий", "value": "#0000FF"},
    {"name": "Фиолетовый", "value": "#800080"},
    {"name": "Коричневый", "value": "#964B00"},
    {"name": "Бордовый", "value": "#800000"},
    {"name": "Малиновый", "value": "#dc143c"},
    {"name": "Пурпурный", "value": "#FF00FF"},
    {"name": "Розовый", "value": "#FFC0CB"},
    {"name": "Циан", "value": "#00FFFF"},
    {"name": "Бирюзовый", "value": "#30d5c8"},
    {"name": "Оливковое", "value": "#808000"},
    {"name": "Хаки", "value": "#806b2a"},
    {"name": "Лайм", "value": "#00FF00"},
    {"name": "Серебряный", "value": "#C0C0C0"},
    {"name": "Серый", "value": "#808080"}
];

Ext.extend(miniShop2.grid.ComboboxColors, MODx.grid.LocalGrid, {

    getColumns: function () {
        return [{
            header: _('sort'),
            dataIndex: 'dd',
            width: 10,
            align: 'center',
            renderer: function () {
                return String.format(
                    '<div class="sort icon icon-sort" style="cursor:move;" title="{0}"></div>',
                    _('move')
                );
            }
        }, {
            header: _('name'),
            dataIndex: 'name',
            width: 30,

            editor: {
                xtype: 'textfield',
                listeners: {
                    change: {fn: this.prepareProperties, scope: this},
                    focus: function () {
                        nameCell = 'name';
                    },

                },
            }
        }, {
            header: _('value'),
            dataIndex: 'value',
            width: 30,
            editor: {
                xtype: 'textfield',
                listeners: {
                    change: {fn: this.prepareProperties, scope: this},
                    focus: function () {
                        nameCell = 'value';
                    },
                }
            },
            renderer: function (value, cell, row) {
                if(value === undefined) {
                    return value;
                }
                var color, text = '#ffffff';
                var r = g = b = 0;
                if ((value.length < 4 || value.length > 7) || (value.length > 4 && value.length < 7)) {
                    return value;
                }
                if (value.length == 7) {
                    r = '0x' + value[1] + value[2];
                    g = '0x' + value[3] + value[4];
                    b = '0x' + value[5] + value[6];
                } else {
                    r = '0x' + value[1] + value[1];
                    g = '0x' + value[2] + value[2];
                    b = '0x' + value[3] + value[3];
                }
                if ((r > 0xbb && g > 0xbb && b > 0xbb) || r > 0xbb && g > 0xbb || g > 0xdd) {
                    text = '#000000';
                }
                color = value;
                return String.format('<span class="minishop2-row-badge" style="background-color:{0};color:{1}">{0}</span>', color, text);
            }
        }, {
            header: _('remove'),
            dataIndex: 'remove',
            width: 10,
            id: 'actions',
            align: 'center',
            renderer: function () {
                return String.format(
                    '\
                    <ul class="minishop2-row-actions">\
                        <li>\
                            <button class="btn btn-default icon icon-remove action-red" title="{0}" action="removeColor"></button>\
                        </li>\
                    </ul>',
                    _('remove')
                );
            }
        }];
    },

    getBottomBar: function (config) {
        return [{
            xtype: 'hidden',
            id: config.id + '-properties',
            name: 'properties'
        }];
    },

    getPlugins: function () {
        return [new Ext.ux.dd.GridDragDropRowOrder({
            copy: false,
            scrollable: true,
            targetCfg: {},
            listeners: {
                afterrowmove: {fn: this.prepareProperties, scope: this}
            }
        })]
    },

    getListeners: function () {
        return {
            viewready: {fn: this.prepareValues, scope: this},
            afteredit: {
                fn: function () {
                    this.prepareProperties();
                    this.addColor(this);
                }, scope: this
            }
        };
    },

    prepareValues: function () {
        if (this.record?.properties?.values.length == false || this.record?.properties?.values.length == undefined) {
            this.record.properties = {};
            this.record.properties['values'] = existcolors;
        }
        if (this.record.properties && this.record.properties['values']) {
            Ext.each(this.record.properties['values'], function (item) {
                this.store.add(new Ext.data.Record(item));
            }, this);
            this.store.add(new Ext.data.Record({
                name: '',
                value: ''
            }));
        } else {
            this.store.add(new Ext.data.Record({
                name: '',
                value: ''
            }));
            this.focusValueCell(0);
        }
        this.prepareProperties();
    },

    prepareProperties: function (ini) {
        var newdata = [];
        this.store.each(function (el) {
            if (el.data['value'] || el.data['name']) {
                newdata.push(el.data);
            }
        });

        var properties = {
            values: newdata,
        };
        properties = Ext.util.JSON.encode(properties);
        Ext.getCmp(this.config.id + '-properties').setValue(properties);
    },

    addColor: function () {
        var newdata = [];
        this.store.each(function (el) {
            if (el.data['value'] || el.data['name']) {
                newdata.push(el.data);
            }
        });
        //var rowi = this.getSelectionModel().getSelected().getChanges();
        var rowi = this.getSelectionModel().last;

        if ((newdata.length == this.store.data.length) && newdata.length <= (rowi + 1)) {
            this.store.add(new Ext.data.Record({
                name: '',
                value: ''
            }));
        }
        if (nameCell == 'name') {
            this.focusValueCell(rowi);
        } else {
            this.focusValueCell2(rowi);
        }
        this.prepareProperties();
    },

    removeColor: function () {
        var record = this.getSelectionModel().getSelected();
        if (!record) {
            return false;
        }
        var newdata = [];
        this.store.each(function (el) {
            newdata.push(el.data);
        });
        if (this.store.data.length == 1) {
            this.store.getAt(0).set('value', '');
            this.store.getAt(0).set('name', '');
            this.focusValueCell(0);
        } else if ((newdata.length != this.store.data.length) && record.data['value'] == '') {
            this.focusValueCell(this.store.data.length - 1);
        } else {
            this.store.remove(record);
        }
        this.prepareProperties();
    },

    focusValueCell: function (row) {
        this.startEditing(row, 1);
    },
    focusValueCell2: function (row) {
        this.startEditing(row, 2);
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof (row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this);
                }
            }
        }
        return this.processEvent('click', e);
    },

});
Ext.reg('minishop2-grid-combobox-colors', miniShop2.grid.ComboboxColors);
