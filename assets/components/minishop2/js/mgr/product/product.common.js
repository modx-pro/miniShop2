var methods = {

	getMainFields: function(config) {
		config = config || {record:{}};
		return  [{
			layout:'column'
			,border: false
			,anchor: '100%'
			,id: 'modx-resource-main-columns'
			,defaults: {
				labelSeparator: ''
				,labelAlign: 'top'
				,border: false
				,msgTarget: 'under'
			}
			,items:[{
				columnWidth: .70
				,layout: 'form'
				,id: 'modx-resource-main-left'
				,defaults: { msgTarget: 'under' }
				,items: this.getMainLeftFields(config)
			},{
				columnWidth: .30
				,layout: 'form'
				,labelWidth: 0
				,border: false
				,id: 'modx-resource-main-right'
				,style: 'margin-right: 0'
				,defaults: { msgTarget: 'under' }
				,items: this.getMainRightFields(config)
			}]
		},{
			html: MODx.onDocFormRender, border: false
		},{xtype: 'hidden',name: 'id', id: 'modx-resource-id',value: config.record.id, submitValue: true}
			,{xtype: 'hidden',name: 'type',value: 'document'}
			,{xtype: 'hidden',name: 'context_key',id: 'modx-resource-context-key',value: config.record.context_key || 'web'}
			,{xtype: 'hidden',name: 'content_type',id: 'modx-resource-content-type', value: MODx.config.default_content_type || 1}
			,{xtype: 'hidden',name: 'class_key',id: 'modx-resource-class-key',value: 'msProduct'}
			,{xtype: 'hidden',name: 'content',id: 'hiddenContent',value: (config.record.content || config.record.ta) || ''}
			,{xtype: 'hidden',name: 'create-resource-token',id: 'modx-create-resource-token',value: config.record.create_resource_token || ''}
			,{xtype: 'hidden',name: 'reloaded',value: !Ext.isEmpty(MODx.request.reload) ? 1 : 0}
			,{xtype: 'hidden',name: 'parent',value: config.record.parent || 0,id: 'modx-resource-parent-hidden'}
			,{xtype: 'hidden',name: 'isfolder',value: 0,id: 'modx-resource-isfolder-hidden'}
			,{xtype: 'hidden',name: 'parent-original',value: config.record.parent || 0,id: 'modx-resource-parent-old-hidden'}
			,{xtype: 'hidden',name: 'source',value: config.record.source || 1,id: 'modx-resource-source-hidden'}
		];
	}

	,getMainLeftFields: function(config) {
		config = config || {record:{}};
		var fields = [];

		var enabled = ['pagetitle','longtitle','introtext','description'];
		var items = fields.push(this.getProductFields(config, enabled, miniShop2.config.main_fields));

		enabled = miniShop2.config.data_fields;
		var tmp = this.getProductFields(config, enabled, miniShop2.config.main_fields);
		var middle = Math.ceil(tmp.length / 2) - 1;
		if (tmp.length > 0) {
			var array = {
				layout:'column'
				,xtype: 'fieldset'
				,title: _('ms2_product')
				,border: false
				,defaults: {border: false}
				,items: [{
					columnWidth: .5
					,layout: 'form'
					,items: []
				},{
					columnWidth: .5
					,layout: 'form'
					,items: []
				}]
			};

			for (var i=0; i < tmp.length; i++) {
				var field = tmp[i];
				field.anchor = '100%';
				if (i > middle) {
					array.items[1].items.push(field);
				}
				else {
					array.items[0].items.push(field);
				}
			}

			fields.push(array);
		}


		enabled = ['content'];
		fields.push(this.getProductFields(config, enabled, miniShop2.config.main_fields));

		return fields;
	}

	,getMainRightFields: function(config) {
		config = config || {};
		var enabled, items;
		var fields = [];

		enabled = ['createdby','publishedby','deletedby','publishedon','pub_date','unpub_date','createdon','deletedon'];
		items = this.getProductFields(config, enabled, miniShop2.config.main_fields);
		if (items.length > 0) {
			fields.push({
				xtype: 'fieldset'
				//,title: _('ms2_dates')
				,id: 'minishop2-box-dates'
				,items: items
			});
		}

		enabled = ['parent','template','alias','menutitle','menuindex','link_attributes'];
		items = this.getProductFields(config, enabled, miniShop2.config.main_fields);
		if (items.length > 0) {
			fields.push({
				xtype: 'fieldset'
				,id: 'minishop2-box-template'
				,items: items
			});
		}

		enabled = ['searchable','cacheable','richtext','syncsite','uri_override','hidemenu','show_in_tree','new','favorite','popular'];
		items = this.getProductFields(config, enabled, miniShop2.config.main_fields);
		items.push({xtype:'xcheckbox', name:'deleted', inputValue:1, id: 'modx-resource-deleted', boxLabel: _('ms2_product_deleted'), description: '<b>[[*deleted]]</b><br/>' + _('resource_deleted_help'), checked:parseInt(config.record.deleted), hidden: config.mode == 'update' ? 1 : 0});
		items.push({xtype:'xcheckbox', name:'published', inputValue:1, id: 'modx-resource-published', boxLabel: _('ms2_product_published'), description: '<b>[[*published]]</b><br/>' + _('resource_published_help'), checked:parseInt(config.record.published), hidden: config.mode == 'update' ? 1 : 0});
		if (items.length > 0) {
			fields.push({
				xtype: 'checkboxgroup'
				,columns: 2
				,id: 'minishop2-box-options'
				,items: items
			});
			fields.push({xtype:'textfield', name: 'uri', id: 'modx-resource-uri',fieldLabel: _('ms2_product_uri'),value:config.record.uri || '',hidden: !config.record.uri_override,anchor: '100%'});
		}

		/*
		 fields.push({
		 html: '<hr />'
		 ,border: false
		 });
		 */

		return fields;
	}


	,getDataFields: function(config) {
		config = config || {record:{}};
		return  [{
			layout:'column'
			,border: false
			,anchor: '100%'
			,id: 'modx-resource-extra-columns'
			,defaults: {
				labelSeparator: ''
				,labelAlign: 'top'
				,border: false
				,msgTarget: 'under'
			}
			,items:[{
				columnWidth: .5
				,layout: 'form'
				,id: 'minishop2-product-extra-left'
				,items: this.getExtraFields(config)
			},{
				columnWidth: .5
				,title: _('ms2_category_tree')
				,id: 'minishop2-product-extra-right'
				,style: 'margin-top: 12px;'
				,items: [{
					xtype: config.mode == 'create' ? 'displayfield' : 'minishop2-tree-categories'
					,value: _('ms2_disabled_while_creating')
					,record: config.record
				}]
			}]
		}];
	}

	,getExtraFields: function(config) {
		config = config || {record:{}};
		var enabled = miniShop2.config.data_fields;
		var items = this.getProductFields(config, enabled, miniShop2.config.extra_fields);

		if (items.length > 0) {
			return {
				xtype: 'fieldset'
				,items: items
			};
		}
		else {return [];}
	}

	,getProductFields: function(config, enabled, available) {
		var product_fields =  this.getAllProductFields(config);
		var fields = [];
		var tmp, properties;
		for (var i = 0; i < available.length; i++) {
			var field = available[i];
			if ((enabled.length > 0 && enabled.indexOf(field) === -1) || miniShop2.config.active_fields.indexOf(field) !== -1) {continue;}
			if (tmp = product_fields[field]) {
				miniShop2.config.active_fields.push(field);
				properties = {
					description: '<b>[[*'+field+']]</b><br />'+_('resource_'+field+'_help')
					,enableKeyEvents: true
					,listeners: config.listeners
					,name: field
					,id: 'modx-resource-'+field
					,value: config.record[field] || ''
					,msgTarget: 'under'
				};
				if (tmp.allowBlank === false) {tmp.fieldLabel = tmp.fieldLabel + ' <span class="required red">*</span>'}
				switch (tmp.xtype) {
					case 'minishop2-xdatetime':
					case 'minishop2-combo-user':
						properties.anchor = '95%';
						properties.fieldLabel = _('ms2_product_'+field);
						break;
					case 'xcheckbox': properties.boxLabel = _('ms2_product_'+field); break;
					case 'textfield':
						properties.maxLength = 255;
					default:
						properties.fieldLabel = _('ms2_product_'+field);
						properties.anchor = '100%';
				}

				Ext.applyIf(tmp, properties);
				fields.push(tmp);
			}
		}
		return fields;
	}

	,getAllProductFields: function(config) {
		var fields = {
			pagetitle: {xtype: 'textfield',fieldLabel: _('ms2_product_pagetitle'),maxLength: 255,allowBlank: false,listeners: {'keyup': {scope:this,fn:function(f,e) {var title = Ext.util.Format.stripTags(f.getValue());Ext.getCmp('modx-resource-header').getEl().update('<h2>'+title+'</h2>'); MODx.fireResourceFormChange();}}}}
			,longtitle: {xtype: 'textfield'}
			,description: {xtype: 'textarea'}
			,introtext: {xtype: 'textarea', description: '<b>[[*introtext]]</b><br />'+_('resource_summary_help')}
			,content: {xtype: 'textarea',name: 'ta',id: 'ta',description:'', height: 400,grow: false,value: (config.record.content || config.record.ta) || ''}

			,createdby: {xtype: 'minishop2-combo-user',value: config.record.createdby, description: '<b>[[*createdby]]</b><br/>' + _('ms2_product_createdby_help')}
			,publishedby: {xtype: 'minishop2-combo-user',value: config.record.publishedby, description: '<b>[[*publishedby]]</b><br/>' + _('ms2_product_publishedby_help')}
			,deletedby: {xtype: 'minishop2-combo-user',value: config.record.deletedby, description: '<b>[[*deletedby]]</b><br/>' + _('ms2_product_deletedby_help')}
			,editedby: {xtype: 'minishop2-combo-user',value: config.record.deletedby, description: '<b>[[*editedby]]</b><br/>' + _('ms2_product_editedby_help')}

			,publishedon: {xtype: 'minishop2-xdatetime',value: config.record.publishedon, description: '<b>[[*publishedon]]</b><br/>' + _('ms2_product_publishedon_help')}
			,createdon: {xtype: 'minishop2-xdatetime',value: config.record.createdon, description: '<b>[[*createdon]]</b><br/>' + _('ms2_product_createdon_help')}
			,deletedon: {xtype: 'minishop2-xdatetime',value: config.record.deletedon, description: '<b>[[*deletedon]]</b><br/>' + _('ms2_product_deletedon_help')}
			,editedon: {xtype: 'minishop2-xdatetime',value: config.record.editedon, description: '<b>[[*editedon]]</b><br/>' + _('ms2_product_editedon_help')}
			,pub_date: {xtype: MODx.config.publish_document ? 'minishop2-xdatetime' : 'hidden', description: '<b>[[*pub_date]]</b><br />'+_('resource_publishdate_help'),id: 'modx-resource-pub-date', value: config.record.pub_date}
			,unpub_date: {xtype: MODx.config.publish_document ? 'minishop2-xdatetime' : 'hidden', description: '<b>[[*unpub_date]]</b><br />'+_('resource_unpublishdate_help'),id: 'modx-resource-unpub-date',value: config.record.unpub_date}

			,template: {xtype: 'modx-combo-template',editable: false,baseParams: {action: MODx.modx23 ? 'element/template/getlist' : 'getlist', combo: '1'},listeners: {select: {fn: this.templateWarning,scope: this}}}
			,parent: {xtype: 'minishop2-combo-category',value: config.record.parent,listeners: {select: {fn:function(data) {Ext.getCmp('modx-resource-parent-hidden').setValue(data.value);MODx.fireResourceFormChange();}}}}
			,alias: {xtype: 'textfield', value: config.record.alias || ''}
			,menutitle: {xtype: 'textfield', value: config.record.menutitle || ''}
			,menuindex: {xtype: 'numberfield', value: config.record.menuindex || 0, anchor: '50%'}
			,link_attributes: {xtype: 'textfield', value:config.record.link_attributes || '', id:'modx-resource-link-attributes'}

			,searchable: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.searchable)}
			,cacheable: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.cacheable)}
			,richtext: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.richtext)}
			,hidemenu: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.hidemenu), description: '<b>[[*hidemenu]]</b><br/>' + _('resource_hide_from_menus_help')}
			,uri_override: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.uri_override), id: 'modx-resource-uri-override'}
			,syncsite: {xtype:'xcheckbox', inputValue:1, description: _('resource_syncsite_help'), checked: config.record.syncsite !== undefined && config.record.syncsite !== null ? parseInt(config.record.syncsite) : true}
			,show_in_tree: {xtype:'xcheckbox', inputValue:1, description: '<b>[[*show_in_tree]]</b><br/>' + _('ms2_product_show_in_tree_help'), checked:parseInt(config.record.show_in_tree)}


			,article: {xtype: 'textfield', description: '<b>[[+article]]</b><br />'+_('ms2_product_article_help')}
			,price: {xtype: 'numberfield', decimalPrecision: 2, description: '<b>[[+price]]</b><br />'+_('ms2_product_price_help')}
			,old_price: {xtype: 'numberfield', decimalPrecision: 2, description: '<b>[[+old_price]]</b><br />'+_('ms2_product_old_price_help')}
			,weight: {xtype: 'numberfield', decimalPrecision: 3, description: '<b>[[+weight]]</b><br />'+_('ms2_product_weight_help')}
			,remains: {xtype: 'numberfield', description: '<b>[[+remains]]</b><br />'+_('ms2_product_remains_help')}
			,reserved: {xtype: 'numberfield', description: '<b>[[+reserved]]</b><br />'+_('ms2_product_reserved_help')}
			,vendor: {xtype: 'minishop2-combo-vendor', description: '<b>[[+vendor]]</b><br />'+_('ms2_product_vendor_help')}
			,made_in: {xtype: 'minishop2-combo-autocomplete', description: '<b>[[+made_in]]</b><br />'+_('ms2_product_made_in_help')}
			,source: {xtype: config.mode == 'update' ? 'hidden' : 'minishop2-combo-source', name: 'source-cmb', disabled: config.mode == 'update', value:config.record.source || 1, description: '<b>[[+source]]</b><br />'+_('ms2_product_source_help'), listeners: {select: {fn:function(data) {Ext.getCmp('modx-resource-source-hidden').setValue(data.value);MODx.fireResourceFormChange();}}}}

			,new: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.new), description: '<b>[[+new]]</b><br />'+_('ms2_product_new_help')}
			,favorite: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.favorite), description: '<b>[[+favorite]]</b><br />'+_('ms2_product_favorite_help')}
			,popular: {xtype:'xcheckbox', inputValue:1, checked:parseInt(config.record.popular), description: '<b>[[+popular]]</b><br />'+_('ms2_product_popular_help')}

			,tags: {xtype: 'minishop2-combo-options', description: _('ms2_product_tags_help')}
			,color: {xtype: 'minishop2-combo-options', description: _('ms2_product_color_help')}
			,size: {xtype: 'minishop2-combo-options', description: _('ms2_product_size_help')}
		};

		for (var i in miniShop2.plugin) {
			if (typeof(miniShop2.plugin[i]['getFields']) == 'function') {
				var add = miniShop2.plugin[i].getFields(config);
				Ext.apply(fields, add);
			}
		}
		return fields;
	}


	,templateWarning: function() {
		var t = Ext.getCmp('modx-resource-template');
		if (!t) { return false; }
		if(t.getValue() !== t.originalValue) {
			Ext.Msg.confirm(_('warning'), _('resource_change_template_confirm'), function(e) {
				if (e == 'yes') {
					var nt = t.getValue();
					var f = Ext.getCmp('modx-page-update-resource');
					f.config.action = MODx.modx23 ? 'resource/reload' : 'reload';
					MODx.activePage.submitForm({
						success: {fn:function(r) {
							var page = MODx.action ? MODx.action[r.result.object.action] : r.result.object.action;
							MODx.loadPage(page, 'id='+r.result.object.id+'&reload='+r.result.object.reload+'&class_key='+this.config.record.class_key);
						},scope:this}
					},{
						bypassValidCheck: true
					},{
						reloadOnly: true
					});
				} else {
					t.setValue(this.config.record.template);
				}
			},this);
		}
		return true;
	}

	,getGallery: function(config) {
		return {
			xtype: config.mode == 'create' ? 'displayfield' : 'minishop2-product-gallery'
			,value: _('ms2_disabled_while_creating')
			,record: config.record
		};
	}

	,getLinks: function(config) {
		return {
			xtype: config.mode == 'create' ? 'displayfield' : 'minishop2-product-links-grid'
			,value: _('ms2_disabled_while_creating')
			,record: config.record
		};
	}
};


miniShop2.panel.ProductSettings = function(config) {
	config = config || {};
	config.listeners = {
		change:{fn:MODx.fireResourceFormChange}
		,select:{fn:MODx.fireResourceFormChange}
		,keydown:{fn:MODx.fireResourceFormChange}
		,check:{fn:MODx.fireResourceFormChange}
		,uncheck:{fn:MODx.fireResourceFormChange}
	};

	miniShop2.config.active_fields = [];

	var items = [{
		title: _('ms2_product_tab_main')
		,hideMode: 'offsets'
		,anchor: '100%'
		,items: this.getMainFields(config)
		,listeners: config.listeners
	}];

	if (miniShop2.config.product_tab_extra) {
		items.push({
			title: _('ms2_product_tab_extra')
			,hideMode: 'offsets'
			,anchor: '100%'
			,items: this.getDataFields(config)
			,listeners: config.listeners
		});
	}
	if (miniShop2.config.product_tab_gallery) {
		items.push({
			title: _('ms2_product_tab_gallery')
			,hideMode: 'offsets'
			,anchor: '100%'
			,items: this.getGallery(config)
		});
	}
	if (miniShop2.config.product_tab_links) {
		items.push({
			title: _('ms2_product_tab_links')
			,hideMode: 'offsets'
			,anchor: '100%'
			,items: this.getLinks(config)
		});
	}

	Ext.applyIf(config,{
		id: 'minishop2-product-settings-panel'
		,border: false
		,deferredRender: false
		,forceLayout: true
		,anchor: '97%'
		,stateful: MODx.config.ms2_product_remember_tabs == true
		,stateEvents: ['tabchange']
		,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
		,headerCfg: {
			tag: 'div'
			,cls: 'x-tab-panel-header vertical-tabs-header'
			,id: 'modx-resource-vtabs-header'
			,html: '<img src="' + miniShop2.config.logo_small + '" width="120" height="90" id="minishop2-product-header-image" />'
		}
		,items: items
	});
	miniShop2.panel.ProductSettings.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.ProductSettings, MODx.VerticalTabs, methods);
Ext.reg('minishop2-product-settings',miniShop2.panel.ProductSettings);


miniShop2.panel.ProductSettingsHorizontal = function(config) {
	config = config || {};
	config.listeners = {
		change:{fn:MODx.fireResourceFormChange}
		,select:{fn:MODx.fireResourceFormChange}
		,keydown:{fn:MODx.fireResourceFormChange}
		,check:{fn:MODx.fireResourceFormChange}
		,uncheck:{fn:MODx.fireResourceFormChange}
	};

	miniShop2.config.active_fields = [];

	var items = [{
		title: _('ms2_product_tab_main')
		,hideMode: 'offsets'
		,anchor: '100%'
		,items: this.getMainFields(config)
		,listeners: config.listeners
	}];

	if (miniShop2.config.product_tab_extra) {
		items.push({
			title: _('ms2_product_tab_extra')
			,hideMode: 'offsets'
			,anchor: '100%'
			,items: this.getDataFields(config)
			,listeners: config.listeners
		});
	}
	if (miniShop2.config.product_tab_gallery) {
		items.push({
			title: _('ms2_product_tab_gallery')
			,hideMode: 'offsets'
			,anchor: '100%'
			,items: this.getGallery(config)
		});
	}
	if (miniShop2.config.product_tab_links) {
		items.push({
			title: _('ms2_product_tab_links')
			,hideMode: 'offsets'
			,anchor: '100%'
			,items: this.getLinks(config)
		});
	}

	Ext.applyIf(config,{
		id: 'minishop2-product-settings-panel-horizontal'
		,border: false
		,footerCfg: {
			tag: 'div'
			,cls: 'x-tab-panel-footer tabs-footer'
			,id: 'modx-resource-htabs-footer'
			,html: '<img src="' + miniShop2.config.logo_small + '" width="120" height="90" id="minishop2-product-header-image" />'
		}
		,items: [{
			html: ''
			,border: false
			,bodyCssClass: 'panel-desc'
			,bodyStyle: 'margin-bottom: 10px'
		},{
			style: 'padding: 5px;'
			,xtype: 'modx-tabs'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,bodyCssClass: 'tab-panel-wrapper main-wrapper'
			,deferredRender: false
			,forceLayout: true
			,anchor: '97%'
			,stateful: MODx.config.ms2_product_remember_tabs == true
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: items
		}]
	});
	miniShop2.panel.ProductSettingsHorizontal.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.ProductSettingsHorizontal, MODx.Panel, methods);
Ext.reg('minishop2-product-settings-horizontal',miniShop2.panel.ProductSettingsHorizontal);

miniShop2.panel.ProductSettingsSimple = function(config) {
	config = config || {};
	config.listeners = {
		change:{fn:MODx.fireResourceFormChange}
		,select:{fn:MODx.fireResourceFormChange}
		,keydown:{fn:MODx.fireResourceFormChange}
		,check:{fn:MODx.fireResourceFormChange}
		,uncheck:{fn:MODx.fireResourceFormChange}
	};

	miniShop2.config.active_fields = [];

	Ext.applyIf(config,{
		id: 'minishop2-product-settings-panel-simple'
		,border: false
		,items: [{
			hideMode: 'offsets'
			,border: false
			,cls: 'modx-resource-tab'
			,layout: 'form'
			,labelAlign: 'top'
			,labelSeparator: ''
			,bodyCssClass: 'tab-panel-wrapper main-wrapper'
			,autoHeight: true
			,defaults: {
				border: false
				,msgTarget: 'under'
			}
			,items: this.getMainFields(config)
			,listeners: config.listeners
		}]
	});
	miniShop2.panel.ProductSettingsHorizontal.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.ProductSettingsSimple, MODx.Panel, methods);
Ext.reg('minishop2-product-settings-simple',miniShop2.panel.ProductSettingsSimple);