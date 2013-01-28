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


miniShop2.combo.DateTime = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		timePosition:'below'
		,allowBlank: true
		,dateFormat: MODx.config.manager_date_format
		,timeFormat: MODx.config.manager_time_format
	});
	miniShop2.combo.DateTime.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.combo.DateTime,Ext.ux.form.DateTime, {
	renderer: function(field) {
		if (!field) {return '';}
		var date = new Date(field);
		return date.format('HH:NN  DD.MM.YY');
	}
});
Ext.reg('minishop2-xdatetime',miniShop2.combo.DateTime);

