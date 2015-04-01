miniShop2.page.Settings = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'minishop2-panel-settings'
			,renderTo: 'minishop2-panel-settings-div'
		}]
	});
	miniShop2.page.Settings.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.page.Settings,MODx.Component);
Ext.reg('minishop2-page-settings',miniShop2.page.Settings);

miniShop2.panel.Settings = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,deferredRender: true
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>'+_('minishop2') + ' :: ' + _('ms2_settings')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,bodyStyle: 'padding: 5px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,hideMode: 'offsets'
			,stateful: true
			,stateId: 'minishop2-settings-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: [{
				title: _('ms2_deliveries')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_deliveries_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-delivery'
				}]
			},{
				title: _('ms2_payments')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_payments_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-payment'
				}]
			},{
				title: _('ms2_statuses')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_statuses_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-status'
				}]
			},{
				title: _('ms2_vendors')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_vendors_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-vendor'
				}]
			},{
				title: _('ms2_links')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_links_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'minishop2-grid-link'
				}]
			},{
				title: _('ms2_options')
				,deferredRender: true
				,items: [{
					html: '<p>'+_('ms2_options_intro')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				}, {
					layout:'column',
					items: [{
						xtype: 'minishop2-tree-option-categories',
						id: 'minishop2-tree-categories-panel',
                        optionGrid: 'minishop2-grid-option',
						columnWidth: .20
					},{
						xtype: 'minishop2-grid-option',
						columnWidth: .80
					}]
				}]
			}]
		}]
	});
	miniShop2.panel.Settings.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Settings,MODx.Panel);
Ext.reg('minishop2-panel-settings',miniShop2.panel.Settings);