miniShop2.panel.ProductGallery = function(config) {
	config = config || {};

	Ext.apply(config,{
		border: false
		,id: 'minishop2-product-gallery'
		,baseCls: 'modx-formpanel'
		,listeners: {
			render: {fn: function(a) {
				this.updateTabImage(config.record.thumb);
			}, scope: this}
		}
		,items: [{
			border: false
			,cls: 'modx-page-header container'
			,html: _('ms2_gallery_introtext')
		},{
			border: false
			,items: [{
				title: ''
				,border: false
				,items: [{
					xtype: 'awesomeuploader'
					,border: false
					,frame: false
					,anchor: '100%'
					,width: '100%'
					,height: 200
					,gridWidth: '99%'
					,gridHeight: 130
					,awesomeUploaderRoot: miniShop2.config.assets_url + 'js/mgr/misc/awesome_uploader/'
					,flashButtonSprite: miniShop2.config.assets_url + 'img/mgr/ms2_gallery_upload.png'
					,flashUploadUrl: miniShop2.config.connector_url + '?action=mgr/gallery/upload&id='+config.record.id+'&HTTP_MODAUTH='+MODx.siteId
					,xhrUploadUrl: miniShop2.config.connector_url + '?action=mgr/gallery/upload&id='+config.record.id+'&HTTP_MODAUTH='+MODx.siteId
					,standardUploadUrl: miniShop2.config.connector_url + '?action=mgr/gallery/upload&id='+config.record.id+'&HTTP_MODAUTH='+MODx.siteId
					,flashButtonWidth:'134'
					,flashButtonHeight:'32'
					,flashSwfUploadFileTypes: '*.png;*.jpg;*.jpeg'
					,tbar: [{
						//swfupload and upload button container
						xtype: 'button'
					},'->',{
						xtype: 'displayfield'
						,html: '<b>' + _('ms2_product_source') + '</b>:&nbsp;&nbsp;'
					},{
						xtype: 'minishop2-combo-source'
						,id: 'minishop2-product-source'
						,description: '<b>[[+source]]</b><br />'+_('ms2_product_source_help')
						,value: config.record.source
						,listeners: {
							select: {fn: this.sourceWarning,scope: this}}
					},{
						xtype: 'tbspacer',
						width: 30
					},{
						xtype: 'button'
						,text: _('ms2_gallery_uploads_clear')
						,handler: function() {
							var store = Ext.getCmp('awesomeuploader-files-grid').getStore();
							store.removeAll();
						}
						,scope: this
					}]
					,listeners: {
						fileupload: {fn: function(uploader,status,response) {
							if (status == true) {
								var grid = Ext.getCmp('minishop2-product-images-panel').view;
								grid.getStore().reload();

								if (response.message != '') {
									Ext.getCmp('minishop2-product-gallery').updateTabImage(response.message);
								}
							}
						},scope: this}
					}
				},{
					xtype: 'minishop2-product-images-panel'
					,id: 'minishop2-product-images-panel'
					,cls: 'modx-pb-view-ct main-wrapper'
					,product_id: config.record.id
					,anchor: '100%'
				}]
			}]
		}]
	});
	miniShop2.panel.ProductGallery.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.ProductGallery,MODx.Panel, {

	sourceWarning: function(combo,option) {
		var source = Ext.getCmp('modx-resource-source-hidden');
		var select = Ext.getCmp('minishop2-product-source');
		var source_id = source.getValue();
		var sel_id = select.getValue();

		if(source_id != sel_id) {
			Ext.Msg.confirm(_('warning'), _('ms2_product_change_source_confirm'), function(e) {
				if (e == 'yes') {
					source.setValue(sel_id);

					var f = Ext.getCmp('modx-page-update-resource');
					//f.config.action = 'update';
					MODx.activePage.submitForm();
				} else {
					select.setValue(source_id);
				}
			},this);
		}
	}

	,updateTabImage: function(thumb) {
		if (thumb === null || thumb == '' || typeof(thumb) == 'undefined') {
			thumb = miniShop2.config.logo_small;
		}
		document.getElementById('minishop2-product-header-image').src = thumb;
	}
});
Ext.reg('minishop2-product-gallery',miniShop2.panel.ProductGallery);



// Adapted widgets from https://github.com/splittingred/Gallery

miniShop2.panel.ProductImages = function(config) {
	config = config || {};

	this.view = MODx.load({
		id: 'minishop2-product-images-view'
		,xtype: 'minishop2-product-images-view'
		,onSelect: {fn:function() { }, scope: this}
		,containerScroll: true
		,ident: this.ident
		,cls: 'minishop2-product-images'
		,pageSize: config.pageSize || (Math.round(MODx.config.default_per_page / 2) || 10)
		,product_id: config.product_id
		,inPanel: true
		,style: 'overflow: auto;'
	});

	this.view.pagingBar = new Ext.PagingToolbar({
		pageSize: config.pageSize || (Math.round(MODx.config.default_per_page / 2) || 10)
		,store: this.view.store
		,displayInfo: true
		,autoLoad: true
		,items: ['-'
			,_('per_page')+':'
			,{
				xtype: 'textfield'
				,value: config.pageSize || (Math.round(MODx.config.default_per_page / 2) || 10)
				,width: 40
				,listeners: {
					change: {fn:function(tf,nv,ov) {
						if (Ext.isEmpty(nv)) return false;
						nv = parseInt(nv);
						this.view.pagingBar.pageSize = nv;
						this.view.store.load({params:{start:0,limit: nv	}});
					},scope:this}
					,render: {fn: function(cmp) {
						new Ext.KeyMap(cmp.getEl(), {
							key: Ext.EventObject.ENTER
							,fn: function() {this.fireEvent('change',this.getValue());this.blur();return true;}
							,scope: cmp
						});
					},scope:this}
				}
			}
			,'-'
		]
	});

	var dv = this.view;
	dv.on('render', function() {
		dv.dragZone = new MODx.DataView.dragZone(dv);
		dv.dropZone = new MODx.DataView.dropZone(dv);
	});

	Ext.applyIf(config,{
		id: 'minishop2-product-images'
		,cls: 'browser-win'
		,layout: 'column'
		,minWidth: 500
		,minHeight: 350
		,autoHeight: true
		,modal: false
		,closeAction: 'hide'
		,border: false
		,autoScroll: true
		,items: [{
			id: 'minishop2-product-gallery-list'
			,cls: 'browser-view'
			,region: 'center'
			,width: '50%'
			,minHeight: 450
			,autoScroll: true
			,border: false
			,tbar: [this.view.pagingBar]
			,items: [this.view]
			//,bbar: [this.view.pagingBar]

		},{
			html: ''
			,id: 'minishop2-product-gallery-details'
			,region: 'east'
			,split: true
			,autoScroll: true
			,width: '45%'
			,minWidth: 150
			,maxWidth: 250
			,height: 450
			,border: false
		}]
	});

	miniShop2.panel.ProductImages.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.ProductImages,MODx.Panel,{
	windows: {}
/*
	,doRefresh: function() {
		this.view.getStore().reload();
	}
*/
});
Ext.reg('minishop2-product-images-panel',miniShop2.panel.ProductImages);


miniShop2.view.ProductImages = function(config) {
	config = config || {};

	this._initTemplates();

	Ext.applyIf(config,{
		url: miniShop2.config.connector_url
		,fields: ['id','product_id','name','description','url','createdon','createdby','file','thumbnail','filesort','source','menu']
		,id: 'minishop2-product-images-view'
		,baseParams: {
			action: 'mgr/gallery/getlist'
			,product_id: config.product_id
			,parent: 0
			,type: 'image'
			,limit: config.pageSize || 10
		}
		,loadingText: _('loading')
		,tpl: this.templates.thumb
		,enableDD: true
		,multiSelect: true
		,listeners: {}
		,prepareData: this.formatData.createDelegate(this)
	});
	miniShop2.view.ProductImages.superclass.constructor.call(this,config);

	this.on('selectionchange',this.showDetails,this,{buffer: 100});
	this.addEvents('sort','select');
	this.on('sort',this.onSort,this);
	this.on('dblclick',this.onDblClick,this);
};
Ext.extend(miniShop2.view.ProductImages,MODx.DataView,{
	templates: {}
	,windows: {}

	,onSort: function(o) {
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/gallery/sort'
				,product_id: this.config.product_id
				,source: o.source.id
				,target: o.target.id
			}
			,listeners: {
				success: {fn:function(response) {
					Ext.getCmp('minishop2-product-gallery').updateTabImage(response.message);
				},scope: this}
			}
		});
	}

	,onDblClick: function(d,idx,n) {
		var node = this.getSelectedNodes()[0];
		if (!node) return false;

		if (this.config.inPanel) {
			this.cm.activeNode = node;
			this.updateImage(node,n);
		} else {
			var data = this.lookup[node.id];
			this.fireEvent('select',data);
		}
	}

	,updateImage: function(btn,e) {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) return false;

		this.windows.updateImage = MODx.load({
			xtype: 'minishop2-gallery-image-update'
			,record: data
			,listeners: {
				success: {fn:function() {
					this.store.reload();
				},scope:this}
			}
		});
		this.windows.updateImage.setValues(data);
		this.windows.updateImage.show(e.target);
	}

	,deleteImage: function(btn,e) {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) return false;

		MODx.msg.confirm({
			text: _('ms2_gallery_image_delete_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/gallery/remove'
				,id: data.id
			}
			,listeners: {
				success: {fn:function(response) {
					Ext.getCmp('minishop2-product-gallery').updateTabImage(response.message);
					this.store.reload();
				},scope:this}
			}
		});
	}

	,deleteMultiple: function(btn,e) {
		var recs = this.getSelectedRecords();
		if (!recs) return false;

		var ids = '';
		for (var i=0;i<recs.length;i++) {
			ids += ','+recs[i].id;
		}

		MODx.msg.confirm({
			text: _('ms2_gallery_image_delete_multiple_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/gallery/remove_multiple'
				,ids: ids.substr(1)
				,product_id: this.config.product_id
			}
			,listeners: {
				'success': {fn:function(response) {
					Ext.getCmp('minishop2-product-gallery').updateTabImage(response.message);
					this.store.reload();
				},scope:this}
			}
		});
		return true;
	}

	,generateThumbs: function() {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) return false;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/gallery/generate'
				,id: data.id
			}
			,listeners: {
				success: {fn:function(response) {
					this.store.reload()
				},scope: this}
			}
		});
	}

	,generateThumbsMultiple: function() {
		var recs = this.getSelectedRecords();
		if (!recs) return false;

		var ids = '';
		for (var i=0;i<recs.length;i++) {
			ids += ','+recs[i].id;
		}
		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'mgr/gallery/generate_multiple'
				,ids: ids.substr(1)
			}
			,listeners: {
				success: {fn:function(response) {
					this.store.reload()
				},scope: this}
			}
		});
	}


	,run: function(p) {
		p = p || {};
		var v = {};
		Ext.apply(v,this.store.baseParams);
		Ext.apply(v,p);
		this.pagingBar.changePage(1);
		this.store.baseParams = v;
		this.store.load();
	}

	,showDetails : function(){
		var selNode = this.getSelectedNodes();
		var detailEl = Ext.getCmp('minishop2-product-gallery-details').body;
		if(selNode && selNode.length > 0){
			selNode = selNode[0];
			var data = this.lookup[selNode.id];
			if (data) {
				//detailEl.hide();
				this.templates.details.overwrite(detailEl, data);
				//detailEl.slideIn('l', {stopFx:true,duration:'0.1'});
				//detailEl.show();
			}
		}else{
			detailEl.update('');
		}
	}

	,formatData: function(data) {
		var formatSize = function(data){
			if(data.size < 1024) {
				return data.size + 'B';
			} else {
				return (Math.round(((data.size*10) / 1024))/10) + " KB";
			}
		};
		data.shortName = Ext.util.Format.ellipsis(data.name, 16);
		data.createdon = new Date(data.createdon).format(MODx.config.manager_date_format + ' ' + MODx.config.manager_time_format);
		this.lookup['ms2-product-image-'+data.id] = data;
		return data;
	}

	,_initTemplates: function() {
		this.templates.thumb = new Ext.XTemplate(
			'<tpl for=".">'
				,'<div class="modx-pb-thumb-wrap" id="ms2-product-image-{id}">'
					,'<div class="gal-item-thumb">'
						,'<img src="{thumbnail}" title="{name}" />'
					,'</div>'
					,'<span>{shortName}</span>'
				,'</div>'
			,'</tpl>'
		);
		this.templates.thumb.compile();

		this.templates.details = new Ext.XTemplate(
			'<div class="details">'
				,'<tpl for=".">'
					,'<div class="modx-pb-detail-thumb"><img src="{url}" width="300" alt="{name}" onclick="window.open(\'{url}\')" /></div>'
						,'<div class="modx-pb-details-info">'
						,_('ms2_gallery_name') + ': <strong>{name}</strong><br/><br/>'
						,_('ms2_gallery_createdon') + ': <strong>{createdon}</strong><br/><br/>'
						,_('ms2_product_source') + ': <strong>{source}</strong><br/><br/>'
						,_('ms2_gallery_url') + ': <a href="{url}" target="_blank" class="link">{url}</a>'
						,'<tpl if="description"><br/><hr/><p>{description}</p></tpl>'
					,'</div>'
				,'</tpl>'
			,'</div>'
		);
		this.templates.details.compile();
	}



	,_showContextMenu: function(v,i,n,e) {
		e.preventDefault();
		var data = this.lookup[n.id];
		var m = this.cm;
		m.removeAll();
		var ct = this.getSelectionCount();
		if (ct == 1) {
			m.add({
				text: _('ms2_gallery_image_update')
				,handler: this.updateImage
				,scope: this
			});
			m.add({
				text: _('ms2_gallery_image_generate_thumbs')
				,handler: this.generateThumbs
				,scope: this
			});
			m.add('-');
			m.add({
				text: _('ms2_gallery_image_delete')
				,handler: this.deleteImage
				,scope: this
			});
			m.show(n,'tl-c?');
		} else if (ct > 1) {
			m.add({
				text: _('ms2_gallery_image_generate_thumbs')
				,handler: this.generateThumbsMultiple
				,scope: this
			});
			m.add('-');
			m.add({
				text: _('ms2_gallery_image_delete_multiple')
				,handler: this.deleteMultiple
				,scope: this
			});
			m.show(n,'tl-c?');
		}

		m.activeNode = n;
	}

});
Ext.reg('minishop2-product-images-view',miniShop2.view.ProductImages);



miniShop2.window.UpdateImage = function(config) {
	config = config || {};
	this.ident = config.ident || 'gupdit'+Ext.id();
	Ext.applyIf(config,{
		title: config.record.shortName || _('ms2_gallery_image_update')
		,id: this.ident
		,closeAction: 'close'
		,width: 450
		,height: 350
		,url: miniShop2.config.connector_url
		,action: 'mgr/gallery/update'
		,layout: 'anchor'
		,autoHeight: false
		,fields: [{
				xtype: 'hidden'
				,name: 'id'
				,id: this.ident+'-id'
			},{
				xtype: 'textfield'
				,fieldLabel: _('name')
				,name: 'name'
				,id: this.ident+'-name'
				,anchor: '100%'
			},{
				xtype: 'textarea'
				,fieldLabel: _('description')
				,name: 'description'
				,id: this.ident+'-description'
				,anchor: '100% -60'
		}]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: this.submit,scope: this}]
	});
	miniShop2.window.UpdateImage.superclass.constructor.call(this,config);
	/*
	this.on('activate',function(w,e) {
		if (typeof Tiny != 'undefined') { MODx.loadRTE(this.ident + '-description'); }
		var d = this.fp.getForm().getValues();
		if (d && d.image) {
			var p = Ext.getCmp(this.ident+'-preview');
			var u = d.image+'&h=200&w=200&zc=1&q=100&f=png';
			p.update('<div class="gal-item-update-preview"><img src="'+u+'" alt="" onclick="Ext.getCmp(\'gal-album-items-view\').showScreenshot(\''+d.id+'\'); return false;" /></div>');
		}
	},this);
	*/
};
Ext.extend(miniShop2.window.UpdateImage,MODx.Window);
Ext.reg('minishop2-gallery-image-update',miniShop2.window.UpdateImage);