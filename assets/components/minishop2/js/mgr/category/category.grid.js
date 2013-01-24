miniShop2.grid.Category = function(config) {
	config = config || {};
	this.exp = new Ext.grid.RowExpander({
		tpl : new Ext.Template(
			'<p class="desc">{content}</p>'
		)
	});
	Ext.applyIf(config,{
		id: 'minishop2-grid-category'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'mgr/product/getlist'
			,parent: config.resource
		}
		,fields: ['id','pagetitle',
			'publishedon','publishedon_date','publishedon_time',
			'uri','uri_override','preview_url',
			'createdby','createdby_username',
			'actions','action_edit','content','comments']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,bodyCssClass: 'grid-with-buttons'
		,cls: 'minishop2-grid'
		,plugins: [this.exp]
		,columns: [this.exp
			,{header: _('ms2_product_publishedon'),dataIndex: 'publishedon',width: 50,sortable: true,renderer: {fn:this._renderPublished,scope:this}}
			,{header: _('ms2_product_pagetitle'),dataIndex: 'pagetitle',id: 'main',width: 200,sortable: true,renderer: {fn:this._renderPageTitle,scope:this}}
			,{header: _('ms2_product_author'),dataIndex: 'createdby_username',width: 150,sortable: true,renderer: {fn:this._renderAuthor,scope:this}}
			,{header: '<!--<img src="'+miniShop2.config.assets_url+'img/comments-icon-w.png" alt="" class="minishop2-comments-col-header" />-->',dataIndex: 'comments',width: 50,sortable: true,renderer: {fn:this._renderComments,scope:this}}
		]
		,tbar: [{
			text: _('ms2_product_create')
			,handler: this.createProduct
			,scope: this
		},
			'->'
			,{
				xtype: 'textfield'
				,name: 'query'
				,width: 200
				,id: 'minishop2-product-search'
				,emptyText: _('search')
				,listeners: {render: {fn: function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER, function() {this.search(tf);}, this);},scope: this}}
			},{
				xtype: 'button'
				,id: 'modx-filter-minishop2-clear'
				,text: _('ms2_product_clear')
				,listeners: {
					'click': {fn: this.clearFilter, scope: this}
				}
			}]
	});
	miniShop2.grid.Category.superclass.constructor.call(this,config);
	this._makeTemplates();
	this.on('rowclick',MODx.fireResourceFormChange);
	this.on('click', this.onClick, this);
};
Ext.extend(miniShop2.grid.Category,MODx.grid.Grid,{

	_makeTemplates: function() {
		this.tplPublished = new Ext.XTemplate('<tpl for=".">'
			+'<div class="minishop2-grid-date">{publishedon_date}<span class="minishop2-grid-time">{publishedon_time}</span></div>'
			+'</tpl>',{
			compiled: true
		});
		this.tplComments = new Ext.XTemplate('<tpl for=".">'
			+'<div class="minishop2-grid-comments"><span>{comments}</span></div>'
			+'</tpl>',{
			compiled: true
		});
		this.tplPageTitle = new Ext.XTemplate('<tpl for="."><div class="product-title-column">'
			+'<h3 class="main-column"><a href="{action_edit}" title="Edit {pagetitle}">{pagetitle}</a><span class="product-id">({id})</span></h3>'
			+'<tpl if="actions">'
			+'<ul class="actions">'
			+'<tpl for="actions">'
			+'<li><a href="#" class="controlBtn {className}">{text}</a></li>'
			+'</tpl>'
			+'</ul>'
			+'</tpl>'
			+'</div></tpl>',{
			compiled: true
		});
	}
	,_renderPublished:function(v,md,rec) {
		return this.tplPublished.apply(rec.data);
	}
	,_renderPageTitle:function(v,md,rec) {
		return this.tplPageTitle.apply(rec.data);
	}
	,_renderComments:function(v,md,rec) {
		return this.tplComments.apply(rec.data);
	}
	,onClick: function(e){
		var t = e.getTarget();
		var elm = t.className.split(' ')[0];
		if(elm == 'controlBtn') {
			var action = t.className.split(' ')[1];
			var record = this.getSelectionModel().getSelected();
			this.menu.record = record;
			switch (action) {
				case 'delete':
					this.deleteTicket();
					break;
				case 'undelete':
					this.undeleteTicket();
					break;
				case 'edit':
					this.editTicket();
					break;
				case 'publish':
					this.publishTicket();
					break;
				case 'unpublish':
					this.unpublishTicket();
					break;
				case 'view':
					this.viewTicket();
					break;
				default:
					window.location = record.data.edit_action;
					break;
			}
		}
	}

	,search: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function() {
		var s = this.getStore();
		s.baseParams.query = '';
		Ext.getCmp('minishop2-product-search').reset();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,editTicket: function(btn,e) {
		location.href = 'index.php?a='+MODx.request.a+'&id='+this.menu.record.id;
	}

	,createProduct: function(btn,e) {
		var createPage = MODx.action ? MODx.action['resource/create'] : 'resource/create';
		MODx.loadPage(createPage, 'class_key=msProduct&parent='+MODx.request.id+'&context_key='+MODx.ctx+'&template=' + miniShop2.template_product_default);
		//location.href = 'index.php?a='+createPage+'&class_key=Ticket&parent='+MODx.request.id+'&context_key='+MODx.ctx+'&template=' + miniShop2.template_product_default;
	}

	,viewTicket: function(btn,e) {
		window.open(this.menu.record.data.preview_url);
		return false;
	}

	,deleteTicket: function(btn,e) {
		MODx.msg.confirm({
			title: _('ms2_product_delete')
			,text: _('ms2_product_delete_text')
			,url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'delete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,undeleteTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'undelete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,publishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'publish'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,unpublishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'unpublish'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

});
Ext.reg('minishop2-grid-category',miniShop2.grid.Category);