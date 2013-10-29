miniShop2.panel.CategoryTemplateSettings = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'minishop2-panel-container-template-settings'
		,layout: 'column'
		,border: false
		,anchor: '100%'
		,defaults: {
			layout: 'form'
			,labelAlign: 'top'
			,anchor: '100%'
			,border: false
			,labelSeparator: ''
		}
		,items: this.getItems(config)
	});
	miniShop2.panel.CategoryTemplateSettings.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.CategoryTemplateSettings,MODx.Panel,{
	getItems: function(config) {
		return [{
			columnWidth: .7
			,items: this.getContentField(config)
		},{
			columnWidth: .3
			,items: this.getSettingRightFields(config)
		}];
	}

	,getContentField: function(config) {
		if (config.mode == "create" && MODx.config.ms2_category_content_default) {
			config.record.content = MODx.config.ms2_category_content_default;
		}

		return [{
			xtype: 'textarea'
			,name: 'ta'
			,id: 'ta'
			,fieldLabel: _('content')
			,anchor: '100%'
			,height: 300
			,grow: false
			,value: config.record.content
		},{
			id: 'modx-content-below'
			,border: false
		}];
	}

	,getSettingRightFields: function(config) {
		var oc = {
			'select':{fn:MODx.fireResourceFormChange}
			,'change':{fn:MODx.fireResourceFormChange}
		};
		return [{
			xtype: 'xdatetime'
			,fieldLabel: _('resource_publishedon')
			,description: '<b>[[*publishedon]]</b><br />'+_('resource_publishedon_help')
			,name: 'publishedon'
			,id: 'modx-resource-publishedon'
			,allowBlank: true
			,dateFormat: MODx.config.manager_date_format
			,timeFormat: MODx.config.manager_time_format
			,startDay: parseInt(MODx.config.manager_week_start)
			,dateWidth: 120
			,timeWidth: 120
			,value: config.record.publishedon
			,listeners: oc
		},{
			xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden'
			,fieldLabel: _('resource_publishdate')
			,description: '<b>[[*pub_date]]</b><br />'+_('resource_publishdate_help')
			,name: 'pub_date'
			,id: 'modx-resource-pub-date'
			,allowBlank: true
			,dateFormat: MODx.config.manager_date_format
			,timeFormat: MODx.config.manager_time_format
			,startDay: parseInt(MODx.config.manager_week_start)
			,dateWidth: 120
			,timeWidth: 120
			,value: config.record.pub_date
			,listeners: oc
		},{
			xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden'
			,fieldLabel: _('resource_unpublishdate')
			,description: '<b>[[*unpub_date]]</b><br />'+_('resource_unpublishdate_help')
			,name: 'unpub_date'
			,id: 'modx-resource-unpub-date'
			,allowBlank: true
			,dateFormat: MODx.config.manager_date_format
			,timeFormat: MODx.config.manager_time_format
			,startDay: parseInt(MODx.config.manager_week_start)
			,dateWidth: 120
			,timeWidth: 120
			,value: config.record.unpub_date
			,listeners: oc
		},{
			xtype: 'fieldset'
			,items: this.getSettingRightFieldset(config)
		},{
			xtype: 'hidden',name: 'content_type',id: 'modx-resource-content-type', value: MODx.config.default_content_type || 1
		}
		];
	}

	,getSettingRightFieldset: function(config) {
		return [{
			layout: 'column'
			,id: 'modx-page-settings-box-columns'
			,border: false
			,anchor: '100%'
			,defaults: {
				labelSeparator: ''
				,labelAlign: 'top'
				,border: false
				,layout: 'form'
				,msgTarget: 'under'
			}
			,items: [{
				columnWidth: .5
				,id: 'modx-page-settings-right-box-left'
				,defaults: { msgTarget: 'under' }
				,items: this.getSettingRightFieldsetLeft(config)
			},{
				columnWidth: .5
				,id: 'modx-page-settings-right-box-right'
				,defaults: { msgTarget: 'under' }
				,items: this.getSettingRightFieldsetRight(config)
			}]
		},{
			xtype: 'hidden'
			,name: 'class_key'
			,id: 'modx-resource-class-key'
			,value: 'msCategory'
		},{
			xtype: 'xcheckbox'
			,boxLabel: _('resource_uri_override')
			,description: _('resource_uri_override_help')
			,hideLabel: true
			,name: 'uri_override'
			,value: 1
			,checked: parseInt(config.record.uri_override) ? true : false
			,id: 'modx-resource-uri-override'
			,listeners: {
				'check':{fn:MODx.fireResourceFormChange}
			}
		},{
			xtype: 'textfield'
			,fieldLabel: _('resource_uri')
			,description: '<b>[[*uri]]</b><br />'+_('resource_uri_help')
			,name: 'uri'
			,id: 'modx-resource-uri'
			,maxLength: 255
			,anchor: '70%'
			,value: config.record.uri || ''
			,hidden: !config.record.uri_override
		}];
	}


	,getSettingRightFieldsetLeft: function(config) {
		var oc = {
			'check':{fn:MODx.fireResourceFormChange}
		};
		return [{
			xtype: 'xcheckbox'
			,boxLabel: _('resource_folder')
			,description: '<b>[[*isfolder]]</b><br />'+_('resource_folder_help')
			,hideLabel: true
			,name: 'isfolder'
			,id: 'modx-resource-isfolder'
			,inputValue: 1
			,value: 1
			,checked: 1
			,disabled: true
		},{
			xtype: 'xcheckbox'
			,boxLabel: _('resource_searchable')
			,description: '<b>[[*searchable]]</b><br />'+_('resource_searchable_help')
			,hideLabel: true
			,name: 'searchable'
			,id: 'modx-resource-searchable'
			,inputValue: 1
			,checked: parseInt(config.record.searchable)
			,listeners: oc
		},{
			xtype: 'xcheckbox'
			,boxLabel: _('resource_richtext')
			,description: '<b>[[*richtext]]</b><br />'+_('resource_richtext_help')
			,hideLabel: true
			,name: 'richtext'
			,id: 'modx-resource-richtext'
			,inputValue: 1
			,checked: parseInt(config.record.richtext)
			,listeners: oc
		}];
	}

	,getSettingRightFieldsetRight: function(config) {
		var oc = {
			'check':{fn:MODx.fireResourceFormChange}
		};
		return [{
			xtype: 'xcheckbox'
			,boxLabel: _('resource_cacheable')
			,description: '<b>[[*cacheable]]</b><br />'+_('resource_cacheable_help')
			,hideLabel: true
			,name: 'cacheable'
			,id: 'modx-resource-cacheable'
			,inputValue: 1
			,checked: parseInt(config.record.cacheable)
			,listeners: oc
		},{
			xtype: 'xcheckbox'
			,boxLabel: _('resource_syncsite')
			,description: _('resource_syncsite_help')
			,hideLabel: true
			,name: 'syncsite'
			,id: 'modx-resource-syncsite'
			,inputValue: 1
			,checked: config.record.syncsite !== undefined && config.record.syncsite !== null ? parseInt(config.record.syncsite) : true
			,listeners: oc
		},{
			xtype: 'xcheckbox'
			,boxLabel: _('deleted')
			,description: '<b>[[*deleted]]</b>'
			,hideLabel: true
			,name: 'deleted'
			,id: 'modx-resource-deleted'
			,inputValue: 1
			,checked: parseInt(config.record.deleted) || false
			,listeners: oc
		}];
	}



});
Ext.reg('minishop2-tab-template-settings',miniShop2.panel.CategoryTemplateSettings);