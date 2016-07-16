miniShop2.window.Image = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: config.record['name'],
        width: 700,
        baseParams: {
            action: 'mgr/gallery/update',
        },
        resizable: false,
        maximizable: false,
        minimizable: false,
    });
    miniShop2.window.Image.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.Image, miniShop2.window.Default, {

    getFields: function (config) {
        var src = config.record['type'] == 'image'
            ? config.record['url']
            : config.record['thumbnail'];
        var img = MODx.config['connectors_url'] + 'system/phpthumb.php?src='
            + src
            + '&w=333&h=198&f=jpg&q=90&zc=0&far=1&HTTP_MODAUTH='
            + MODx.siteId + '&wctx=mgr&source='
            + config.record['source'];
        var fields = {
            ms2_gallery_file_source: config.record['source_name'],
            ms2_gallery_file_size: config.record['size'],
            ms2_gallery_file_createdon: config.record['createdon'],
        };
        var details = '';
        for (var i in fields) {
            if (!fields.hasOwnProperty(i)) {
                continue;
            }
            if (fields[i]) {
                details += '<tr><th>' + _(i) + ':</th><td>' + fields[i] + '</td></tr>';
            }
        }

        return [
            {xtype: 'hidden', name: 'id', id: this.ident + '-id'},
            {
                layout: 'column',
                border: false,
                anchor: '100%',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [{
                        xtype: 'displayfield',
                        hideLabel: true,
                        html: '\
                            <a href="' + config.record['url'] + '" target="_blank" class="minishop2-gallery-window-link">\
                                <img src="' + img + '" class="minishop2-gallery-window-thumb"  />\
                            </a>\
                            <table class="minishop2-gallery-window-details">' + details + '</table>'
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: _('ms2_gallery_file_name'),
                        name: 'file',
                        id: this.ident + '-file',
                        anchor: '100%'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('ms2_gallery_file_title'),
                        name: 'name',
                        id: this.ident + '-name',
                        anchor: '100%'
                    }, {
                        xtype: 'textarea',
                        fieldLabel: _('ms2_gallery_file_description'),
                        name: 'description',
                        id: this.ident + '-description',
                        anchor: '100%',
                        height: 100
                    }]
                }]
            }
        ];
    }

});
Ext.reg('minishop2-gallery-image', miniShop2.window.Image);