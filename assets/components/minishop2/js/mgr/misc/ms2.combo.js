Ext.namespace('miniShop2.combo');

miniShop2.combo.User = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		name: 'user'
		,fieldLabel: _('ms2_product_' + config.name || 'createdby')
		,hiddenName: config.name || 'createdby'
		,displayField: 'username'
		,valueField: 'id'
		,anchor: '99%'
		,fields: ['username','id']
		,pageSize: 20
		,url: MODx.config.connectors_url + 'security/user.php'
		,typeAhead: true
		,editable: true
		,action: 'getList'
		,allowBlank: true
		,baseParams: {
			action: 'getlist'
			,combo: 1
			,limit: 0
		}
	});
	miniShop2.combo.User.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.combo.User,MODx.combo.ComboBox);
Ext.reg('minishop2-combo-user',miniShop2.combo.User);


miniShop2.combo.Category = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-combo-section'
		,fieldLabel: _('ms2_product_parent')
		,description: '<b>[[*parent]]</b><br />'+_('ms2_ms2_product_parent_help')
		,fields: ['id','pagetitle','parents']
		,valueField: 'id'
		,displayField: 'pagetitle'
		,name: 'parent-cmb'
		,hiddenName: 'parent-cmp'
		,allowBlank: false
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/category/getcats'
		}
		,tpl: new Ext.XTemplate(''
		+'<tpl for="."><div class="minishop2-category-list-item">'
			+'<tpl if="parents">'
					+'<span class="parents">'
						+'<tpl for="parents">'
							+'<nobr>{pagetitle} / </nobr>'
						+'</tpl>'
					+'</span>'
			+'</tpl>'
			+'<h3 class="">{pagetitle}</h3>'
			+'</div></tpl>',{
			compiled: true
		})
		,itemSelector: 'div.minishop2-category-list-item'
		,pageSize: 20
		,typeAhead: true
		,editable: true
	});
	miniShop2.combo.Category.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.combo.Category,MODx.combo.ComboBox);
Ext.reg('minishop2-combo-category',miniShop2.combo.Category);



miniShop2.combo.DateTime = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		timePosition:'right'
		,allowBlank: true
		,hiddenFormat:'Y-m-d H:i:s'
		,dateFormat: MODx.config.manager_date_format
		,timeFormat: MODx.config.manager_time_format
		,dateWidth: 120
		,timeWidth: 120
	});
	miniShop2.combo.DateTime.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.combo.DateTime,Ext.ux.form.DateTime, {
/*	renderer: function(field) {
		if (!field) {return '';}
		var date = new Date(field);
		return date.format('HH:NN  DD.MM.YY');
	}
	*/
});
Ext.reg('minishop2-xdatetime',miniShop2.combo.DateTime);

