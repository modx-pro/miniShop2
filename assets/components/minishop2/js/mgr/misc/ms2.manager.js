Ext.override(MODx.window.QuickCreateResource, {
    listeners: {
        show: function () {
            var classKey = this.config.record.class_key,
                isProduct = classKey == 'msProduct',
                class_key = isProduct ? 'ms2_product' : 'ms2_category',
                windowTitle = _(class_key + '_create_here');

            if (['msProduct', 'msCategory'].includes(classKey)) {
                switch (this.action) {
                    case 'resource/create':
                        var form = this.fp.getForm(),
                            templateCmb = form.findField('template'),
                            templateCmbStore = templateCmb.getStore(),
                            templateSettingKey = isProduct ? 'ms2_template_product_default' : 'ms2_template_category_default',
                            templateVal = MODx.config[templateSettingKey] || MODx.config.default_template,
                            contentSettingKey = 'ms2_category_content_default',
                            contentCmb = form.findField('content'),
                            contentVal = MODx.config[contentSettingKey];

                        MODx.Ajax.request({
                            url: MODx.config.connector_url,
                            params: {
                                action: 'context/setting/getlist',
                                context_key: this.config.record.context_key
                            },
                            listeners: {fn: function () {}, scope: this},
                            callback: function (options, success, response) {
                                var r = JSON.parse(response.responseText),
                                    settings = r.results;

                                if (success && settings) {
                                    for (var prop in settings) {
                                        if (!settings.hasOwnProperty(prop)) {
                                            continue;
                                        }
                                        switch (settings[prop].key) {
                                            case templateSettingKey:
                                                templateVal = settings[prop].value;
                                                continue;
                                            case contentSettingKey:
                                                contentVal = settings[prop].value;
                                                continue;
                                        }
                                    }
                                }

                                if (templateCmb && templateCmbStore) {
                                    templateCmbStore.on('load', function () {
                                        if (templateCmb.getValue() != templateVal) {
                                            templateCmb.setValue(templateVal);
                                        }
                                    });
                                    templateCmbStore.load();
                                }

                                if (contentCmb && !isProduct && contentCmb.getValue() != contentVal) {
                                    contentCmb.setValue(contentVal);
                                }
                            }
                        });

                        this._setTitle(_('quick_create'), windowTitle);
                        break;
                    case 'resource/update':
                        this._setTitle(_('quick_update'), windowTitle);
                        break;
                }
            }
        }
    },
    _setTitle: function (prefix, title) {
        if (title != 'undefined') {
            this.setTitle(prefix + ' ' + title.toLowerCase());
        }
    }
});
