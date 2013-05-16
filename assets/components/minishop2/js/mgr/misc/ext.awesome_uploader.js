/*
 Awesome Uploader
 AwesomeUploader JavaScript Class

 Copyright (c) 2010, Andrew Rymarczyk
 All rights reserved.

 Redistribution and use in source and minified, compiled or otherwise obfuscated
 form, with or without modification, are permitted provided that the following
 conditions are met:

 * Redistributions of source code must retain the above copyright notice,
 this list of conditions and the following disclaimer.
 * Redistributions in minified, compiled or otherwise obfuscated form must
 reproduce the above copyright notice, this list of conditions and the
 following disclaimer in the documentation and/or other materials
 provided with the distribution.

 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*
 if(SWFUpload !== undefined){
 SWFUpload.UPLOAD_ERROR_DESC = {
 '-200': 'HTTP ERROR'
 ,'-210': 'MISSING UPLOAD URL'
 ,'-220': 'IO ERROR'
 ,'-230': 'SECURITY ERROR'
 ,'-240': 'UPLOAD LIMIT EXCEEDED'
 ,'-250': 'UPLOAD FAILED'
 ,'-260': 'SPECIFIED FILE ID NOT FOUND'
 ,'-270': 'FILE VALIDATION FAILED'
 ,'-280': 'FILE CANCELLED'
 ,'-290': 'UPLOAD STOPPED'
 };
 SWFUpload.QUEUE_ERROR_DESC = {
 '-100': 'QUEUE LIMIT EXCEEDED'
 ,'-110': 'FILE EXCEEDS SIZE LIMIT'
 ,'-120': 'ZERO BYTE FILE'
 ,'-130': 'INVALID FILETYPE'
 };
 }
 */

AwesomeUploader = Ext.extend(Ext.Panel, {
	jsonUrl:'/test/router/fileMan/'
	,jsonUrlUpload:'upload'
	,swfUploadItems:[]
	,doLayout:function(){
		AwesomeUploader.superclass.doLayout.apply(this, arguments);
		this.fileGrid.getView().refresh();
	}
	,initComponent:function(){

		this.addEvents(
			'fileupload'
			// fireEvent('fileupload', Obj thisUploader, Bool uploadSuccessful, Obj serverResponse);
			//server response object will at minimum have a property "error" describing the error.
			,'fileselectionerror'
			// fireEvent('fileselectionerror', String message)
			//fired by drag and drop and swfuploader if a file that is too large is selected.
			//Swfupload also fires this even if a 0-byte file is selected or the file type does not match the "flashSwfUploadFileTypes" mask
		);

		var fields = ['id', 'name', 'size', 'status', 'progress'];
		this.fileRecord = Ext.data.Record.create(fields);

		this.initialConfig = this.initialConfig || {};
		this.initialConfig.awesomeUploaderRoot = this.initialConfig.awesomeUploaderRoot || '';

		Ext.apply(this, this.initialConfig, {
			flashButtonSprite:this.initialConfig.awesomeUploaderRoot+'swfupload_browse_button_trans_56x22.PNG'
			,flashButtonWidth:'56'
			,flashButtonHeight:'22'
			,flashUploadFilePostName:'Filedata'
			,disableFlash:false
			,flashSwfUploadPath:this.initialConfig.awesomeUploaderRoot+'swfupload.swf'
			//,flashSwfUploadFileSizeLimit:'3 MB' //deprecated
			,flashSwfUploadFileTypes:'*.*'
			,flashSwfUploadFileTypesDescription:'All Files'
			,flashUploadUrl:this.initialConfig.awesomeUploaderRoot+'upload.php'
			,xhrUploadUrl:this.initialConfig.awesomeUploaderRoot+'xhrupload.php'
			,xhrFileNameHeader:'X-File-Name'
			,xhrExtraPostDataPrefix:'extraPostData_'
			,xhrFilePostName:'Filedata'
			,xhrSendMultiPartFormData:false
			,maxFileSizeBytes: 3145728 // 3 * 1024 * 1024 = 3 MiB
			,standardUploadFilePostName:'Filedata'
			,standardUploadUrl: this.initialConfig.awesomeUploaderRoot+'upload.php'
			,iconStatusPending: '<img src="' + this.initialConfig.awesomeUploaderRoot + 'hourglass.png" height="16" width="16" />'
			,iconStatusSending: '<img src="' + this.initialConfig.awesomeUploaderRoot + 'loading.gif" height="16" width="16" />'
			,iconStatusAborted: '<img src="' + this.initialConfig.awesomeUploaderRoot + 'cross.png" height="16" width="16" />'
			,iconStatusError: '<img src="' + this.initialConfig.awesomeUploaderRoot + 'cross.png" height="16" width="16" />'
			,iconStatusDone: '<img src="' + this.initialConfig.awesomeUploaderRoot + 'tick.png" height="16" width="16" />'
			,supressPopups:false
			,extraPostData:{}
			,width:440
			,height:250
			,autoScroll: true
			,border:true
			,frame:true
			,layout:'absolute'
			,fileId:0
			,tbar: this.initialConfig.tbar || [{
				//swfupload and upload button container
				xtype: 'button'
			},'->',{
				xtype: 'button'
				,text: _('ms2_gallery_uploads_clear')
				,handler: function() {
					var store = this.fileGrid.getStore();
					store.removeAll();
				}
				,scope: this
			}]
			,items:[
				{
				//swfupload and upload button container
			},{
				xtype:'grid'
				,id: 'awesomeuploader-files-grid'
				,width:this.initialConfig.gridWidth || 420
				,height:this.initialConfig.gridHeight || 200
				,enableHdMenu:false
				,store:new Ext.data.ArrayStore({
					fields: fields
					,reader: new Ext.data.ArrayReader({idIndex: 0}, this.fileRecord)
				})
				,autoExpandColumn: 'awesomeuploader-column-filename'
				,viewConfig: {
					forceFit: true
					,enableRowBody: true
					,autoFill: true
					,showPreview: true
					,scrollOffset: 0
					,emptyText: _('ms2_gallery_emptymsg')
				}
				,columns:[
					{header: _('ms2_gallery_filename'), dataIndex:'name', width:250, id: 'awesomeuploader-column-filename'}
					,{header: _('ms2_gallery_size'), dataIndex:'size', width:100, renderer:Ext.util.Format.fileSize}
					//,{header: '&nbsp;',dataIndex:'status', width: 50, scope:this, renderer:this.statusIconRenderer}
					,{header: _('ms2_gallery_status'), dataIndex:'status', width: 50}
					,{header: _('ms2_gallery_progress'), dataIndex:'progress', width: 200, scope:this, renderer:this.progressBarColumnRenderer}
				]
				,listeners:{
					render:{
						scope:this
						,fn:function(){
							this.fileGrid = this.items.items[1];
							this.initFlashUploader();
							this.initDnDUploader();
						}
					}
				}
			}]
		});

		AwesomeUploader.superclass.initComponent.apply(this, arguments);
	}
	,fileAlert:function(text){
		if(this.supressPopups){
			return true;
		}
		if(this.fileAlertMsg === undefined || !this.fileAlertMsg.isVisible()){
			this.fileAlertMsgText = text;
			this.fileAlertMsg = Ext.MessageBox.show({
				title:'Upload Error'
				,msg: this.fileAlertMsgText
				,buttons: Ext.Msg.OK
				,modal:false
				,minWidth: 600
				//icon: Ext.MessageBox.ERROR
			});
		}else{
			this.fileAlertMsgText += '<br>' + text;
			this.fileAlertMsg.updateText(this.fileAlertMsgText);
			this.fileAlertMsg.getDialog().focus();
		}

	}
	/*
	,statusIconRenderer:function(value){
		var res;
		switch(value) {
			case 'Pending':res = this.iconStatusPending; break;
			case 'Sending':res = this.iconStatusSending; break;
			case 'Aborted':res = this.iconStatusAborted; break;
			case 'Error':res = this.iconStatusError; break;
			case 'Done': res = this.iconStatusDone; break;
			default: res = value;
		}
		return '<div style="text-align:center">' + res +  '</div>';
	}
	*/

	,progressBarColumnTemplate: new Ext.XTemplate(
		'<div class="ux-progress-cell-inner ux-progress-cell-inner-center ux-progress-cell-foreground">',
		'<div>{value} %</div>',
		'</div>',
		'<div class="ux-progress-cell-inner ux-progress-cell-inner-center ux-progress-cell-background" style="left:{value}%">',
		'<div style="left:-{value}%">{value} %</div>',
		'</div>'
	)

	,progressBarColumnRenderer:function(value, meta, record, rowIndex, colIndex, store){
		meta.css += ' x-grid3-td-progress-cell';
		return this.progressBarColumnTemplate.apply({
			value: value
		});
	}

	,addFile:function(file){
		var fileRec = new this.fileRecord(
			Ext.apply(file,{
				id: ++this.fileId
				,status: 'Pending'
				,progress: '0'
				,complete: '0'
			})
		);
		this.fileGrid.store.add(fileRec);

		return fileRec;
	}

	,updateFile:function(fileRec, key, value){
		fileRec.set(key, value);
		fileRec.commit();
	}

	,initStdUpload:function(param){
		if(this.uploader){
			this.uploader.fileInput = null; //remove reference to file field. necessary to prevent destroying file field during upload.
			Ext.destroy(this.uploader);
		}else{
			Ext.destroy(this.items.items[0]);
		}
		this.uploader = new Ext.ux.form.FileUploadField({
			renderTo:this.body
			,buttonText: _('ms2_browse_files')
			,buttonOnly: true
			,x:0
			,y:0
			,style:'position:absolute;'
			,name:this.standardUploadFilePostName
			,listeners:{
				scope:this
				,fileselected:this.stdUploadFileSelected
			}
		});

	}

	,initFlashUploader:function(){
		if(this.disableFlash){
			this.initStdUpload();
			return true;
		}
		var settings = {
			flash_url: this.flashSwfUploadPath
			,upload_url: this.flashUploadUrl
			,file_size_limit: this.maxFileSizeBytes + ' B'
			,file_types: this.flashSwfUploadFileTypes
			,file_types_description: this.flashSwfUploadFileTypesDescription
			,file_upload_limit: 100
			,file_queue_limit: 0
			,debug: false
			,post_params: this.extraPostData
			,button_image_url: this.flashButtonSprite
			,button_width: this.flashButtonWidth
			,button_height: this.flashButtonHeight
			,button_window_mode: 'opaque'
			,file_post_name: this.flashUploadFilePostName
			//,button_placeholder: this.items.items[0].body.dom
			,button_placeholder: this.tbar.dom.children[0].children[0].children[0].children[0].children[0]
			,file_queued_handler: this.swfUploadfileQueued.createDelegate(this)
			,file_dialog_complete_handler: this.swfUploadFileDialogComplete.createDelegate(this)
			,upload_start_handler: this.swfUploadUploadStart.createDelegate(this)
			,upload_error_handler: this.swfUploadUploadError.createDelegate(this)
			,upload_progress_handler: this.swfUploadUploadProgress.createDelegate(this)
			,upload_success_handler: this.swfUploadSuccess.createDelegate(this)
			,upload_complete_handler: this.swfUploadComplete.createDelegate(this)
			,file_queue_error_handler: this.swfUploadFileQueError.createDelegate(this)
			,minimum_flash_version: '9.0.28'
			,swfupload_load_failed_handler: this.initStdUpload.createDelegate(this)
			,button_text: '<span class="buttonText">' + _('ms2_gallery_button_upload') + '</span>'
			,button_text_style: '.buttonText {color:#53595f;font-size:11px;font-weight:bold;font-family:tahoma,verdana,helvetica,sans-serif;text-shadow:0 1px 0 #fcfcfc;display:inline-block;width:134px;text-align:center;}'
			,button_cursor: SWFUpload.CURSOR.HAND
			//,button_text_left_padding : 14
			,button_text_top_padding : 7
		};
		this.swfUploader = new SWFUpload(settings);
	}

	,initDnDUploader:function(){
		//==================
		// Attach drag and drop listeners to document body
		// this prevents incorrect drops, reloading the page with the dropped item
		// This may or may not be helpful
		if(!document.body.BodyDragSinker){
			document.body.BodyDragSinker = true;

			var body = Ext.fly(document.body);
			body.on({
				dragenter:function(event){
					return true;
				}
				,dragleave:function(event){
					return true;
				}
				,dragover:function(event){
					event.stopEvent();
					return true;
				}
				,drop:function(event){
					event.stopEvent();
					return true;
				}
			});
		}
		// end body events
		//==================

		this.el.on({
			dragenter:function(event){
				event.browserEvent.dataTransfer.dropEffect = 'move';
				return true;
			}
			,dragover:function(event){
				event.browserEvent.dataTransfer.dropEffect = 'move';
				event.stopEvent();
				return true;
			}
			,drop:{
				scope:this
				,fn:function(event){
					event.stopEvent();
					var files = event.browserEvent.dataTransfer.files;

					if(files === undefined){
						return true;
					}
					var len = files.length;
					while(--len >= 0){
						this.processDnDFileUpload(files[len]);
					}
				}
			}
		});

	}

	,processDnDFileUpload:function(file){

		var fileRec = this.addFile({
			name: file.name
			,size: file.size
		});

		if(file.size > this.maxFileSizeBytes){
			this.updateFile(fileRec, 'status', 'Error');
			this.fileAlert(file.name+'<br/><i>File size exceeds allowed limit.</i><br/>');
			this.fireEvent('fileselectionerror', 'File size exceeds allowed limit.');
			return true;
		}

		var upload = new Ext.ux.XHRUpload({
			url:this.xhrUploadUrl
			,filePostName:this.xhrFilePostName
			,fileNameHeader:this.xhrFileNameHeader
			,extraPostData:this.extraPostData
			,sendMultiPartFormData:this.xhrSendMultiPartFormData
			,file:file
			,listeners:{
				scope:this
				,uploadloadstart:function(event){
					this.updateFile(fileRec, 'status', _('ms2_gallery_status_sending'));
				}
				,uploadprogress:function(event){
					this.updateFile(fileRec, 'progress', Math.round((event.loaded / event.total)*100));
				}
				// XHR Events
				,loadstart:function(event){
					this.updateFile(fileRec, 'status', _('ms2_gallery_status_sending'));
				}
				,progress:function(event){
					fileRec.set('progress', Math.round((event.loaded / event.total)*100) );
					fileRec.commit();
				}
				,abort:function(event){
					this.updateFile(fileRec, 'status', _('ms2_gallery_status_aborted'));
					this.fireEvent('fileupload', this, false, {error:'XHR upload aborted'});
				}
				,error:function(event){
					this.updateFile(fileRec, 'status', _('ms2_gallery_status_error'));
					this.fireEvent('fileupload', this, false, {error:'XHR upload error'});
				}
				,load:function(event){

					try{
						var result = Ext.util.JSON.decode(upload.xhr.responseText);//throws a SyntaxError.
					}catch(e){
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK
							//,icon: Ext.MessageBox.ERROR
							,modal:false
							,minWidth: 600
							,title:'Upload Error!'
							,msg:'Invalid JSON Data Returned: ' + upload.xhr.responseText
						});
						this.updateFile(fileRec, 'status', _('ms2_gallery_status_error'));
						this.fireEvent('fileupload', this, false, {error:'Invalid JSON returned'});
						return true;
					}
					if( result.success ){
						fileRec.set('progress', 100 );
						fileRec.set('status', _('ms2_gallery_status_done'));
						fileRec.commit();
						this.fireEvent('fileupload', this, true, result);
					}else{
						this.fileAlert(file.name+'<br/><i>'+result.message+'</i><br/>');
						this.updateFile(fileRec, 'status', _('ms2_gallery_status_error'));
						this.fireEvent('fileupload', this, false, result);
					}
				}
			}
		});
		upload.send();
	}

	,swfUploadUploadProgress:function(file, bytesComplete, bytesTotal){
		this.updateFile(this.swfUploadItems[file.index], 'progress', Math.round((bytesComplete / bytesTotal)*100));
	}

	,swfUploadFileDialogComplete:function(){
		this.swfUploader.startUpload();
	}

	,swfUploadUploadStart:function(file){
		this.swfUploader.setPostParams(this.extraPostData); //sync post data with flash
		this.updateFile(this.swfUploadItems[file.index], 'status', _('ms2_gallery_status_sending'));
	}

	,swfUploadComplete:function(file){ //called if the file is errored out or on success
		this.swfUploader.startUpload(); //as per the swfupload docs, start the next upload!
	}

	,swfUploadUploadError:function(file, errorCode, message){
		this.fileAlert(file.name+'<br/><i>'+message+'</i><br/>');//SWFUpload.UPLOAD_ERROR_DESC[errorCode.toString()]

		this.updateFile(this.swfUploadItems[file.index], 'status', _('ms2_gallery_status_error'));
		this.fireEvent('fileupload', this, false, {error:message});
	}

	,swfUploadSuccess:function(file, serverData){ //called when the file is done
		try{
			var result = Ext.util.JSON.decode(serverData);//throws a SyntaxError.
		}catch(e){
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK
				//,icon: Ext.MessageBox.ERROR
				,modal:false
				,minWidth: 600
				,title:'Upload Error!'
				,msg:'Invalid JSON Data Returned: ' + serverData
			});
			this.updateFile(this.swfUploadItems[file.index], 'status', _('ms2_gallery_status_error'));
			this.fireEvent('fileupload', this, false, {error:'Invalid JSON returned'});
			return true;
		}
		if( result.success ){
			this.swfUploadItems[file.index].set('progress',100);
			this.swfUploadItems[file.index].set('status', _('ms2_gallery_status_done'));
			this.swfUploadItems[file.index].commit();
			this.fireEvent('fileupload', this, true, result);
		}else{
			this.fileAlert(file.name+'<br/><i>'+result.message+'</i><br/>');
			this.updateFile(this.swfUploadItems[file.index], 'status', _('ms2_gallery_status_error'));
			this.fireEvent('fileupload', this, false, result);
		}
	}

	,swfUploadfileQueued:function(file){
		this.swfUploadItems[file.index] = this.addFile({
			name: file.name
			,size: file.size
		});
		return true;
	}

	,swfUploadFileQueError:function(file, error, message){
		this.swfUploadItems[file.index] = this.addFile({
			name: file.name
			,size: file.size
		});
		this.updateFile(this.swfUploadItems[file.index], 'status', _('ms2_gallery_status_error'));
		this.fileAlert(file.name+'<br/><i>'+message+'</i><br/>');
		this.fireEvent('fileselectionerror', message);
	}

	,stdUploadSuccess:function(form, action){
		form.el.fileRec.set('progress',100);
		form.el.fileRec.set('status', _('ms2_gallery_status_done'));
		form.el.fileRec.commit();
		this.fireEvent('fileupload', this, true, action.result);
	}

	,stdUploadFail:function(form, action){
		this.updateFile(form.el.fileRec, 'status', _('ms2_gallery_status_error'));
		this.fireEvent('fileupload', this, false, action.result);
		this.fileAlert(form.el.fileRec.get('name')+'<br/><i>'+action.result.message+'</i><br/>');
	}

	,stdUploadFileSelected:function(fileBrowser, fileName){
		var lastSlash = fileName.lastIndexOf('/'); //check for *nix full file path
		if( lastSlash < 0 ){
			lastSlash = fileName.lastIndexOf('\\'); //check for win full file path
		}
		if(lastSlash > 0){
			fileName = fileName.substr(lastSlash+1);
		}
		var file = {
			name:fileName
			,size:'0'
		};

		if(Ext.isDefined(fileBrowser.fileInput.dom.files) ){
			file.size = fileBrowser.fileInput.dom.files[0].size;
		};

		var fileRec = this.addFile(file);

		if( file.size > this.maxFileSizeBytes){
			this.updateFile(fileRec, 'status', _('ms2_gallery_status_error'));
			this.fileAlert(file.name+'<br/><i>File size exceeds allowed limit.</i><br/>');
			this.fireEvent('fileselectionerror', 'File size exceeds allowed limit.');
			return true;
		}

		var formEl = document.createElement('form'),
			extraPost;
		for( attr in this.extraPostData){
			extraPost = document.createElement('input'),
				extraPost.type = 'hidden';
			extraPost.name = attr;
			extraPost.value = this.extraPostData[attr];
			formEl.appendChild(extraPost);
		}
		formEl = this.el.appendChild(formEl);
		formEl.fileRec = fileRec;
		fileBrowser.fileInput.addClass('au-hidden');
		formEl.appendChild(fileBrowser.fileInput);
		formEl.addClass('au-hidden');
		var formSubmit = new Ext.form.BasicForm(formEl,{
			method:'POST'
			,fileUpload:true
		});

		formSubmit.submit({
			url:this.standardUploadUrl
			,scope:this
			,success:this.stdUploadSuccess
			,failure:this.stdUploadFail
		});
		this.updateFile(fileRec, 'status', _('ms2_gallery_status_sending'));
		this.initStdUpload(); //re-init uploader for multiple simultaneous uploads
	}

});

Ext.reg('awesomeuploader', AwesomeUploader);



/*
 Awesome Uploader
 Ext.ux.XHRUpload JavaScript Class

 Copyright (c) 2010, Andrew Rymarczyk
 All rights reserved.

 Redistribution and use in source and minified, compiled or otherwise obfuscated
 form, with or without modification, are permitted provided that the following
 conditions are met:

 * Redistributions of source code must retain the above copyright notice,
 this list of conditions and the following disclaimer.
 * Redistributions in minified, compiled or otherwise obfuscated form must
 reproduce the above copyright notice, this list of conditions and the
 following disclaimer in the documentation and/or other materials
 provided with the distribution.

 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

// API Specs:
// http://www.w3.org/TR/XMLHttpRequest/
// http://www.w3.org/TR/XMLHttpRequest2/
// http://www.w3.org/TR/progress-events/

// Browser Implementation Details:
// FROM: https://developer.mozilla.org/en/DOM/File
// https://developer.mozilla.org/en/Using_files_from_web_applications
// https://developer.mozilla.org/En/DragDrop/DataTransfer
// https://developer.mozilla.org/en/DOM/FileList
// "NOTE: The File object as implemented by Gecko offers several non-standard methods for reading the contents of the file. These should not be used, as they will prevent your web application from being used in other browsers, as well as in future versions of Gecko, which will likely remove these methods."
// NOTE: fileObj.getAsBinary() is deprecated according to the mozilla docs!

// Can optionally follow RFC2388
// RFC2388 - Returning Values from Forms: multipart/form-data
// http://www.faqs.org/rfcs/rfc2388.html
// This allows additional POST params to be sent with file upload, and also simplifies the backend upload handler becuase a single script can be used for drag and drop, flash, and standard uploads
// NOTE: This is currently only supported by Firefox 1.6, Chrome 6 should be released soon and will also be supported.

Ext.ns('Ext.ux');

Ext.ux.XHRUpload = function(config){
	Ext.apply(this, config, {
		method: 'POST'
		,fileNameHeader: 'X-File-Name'
		,filePostName:'fileName'
		,contentTypeHeader: 'text/plain; charset=x-user-defined-binary'
		,extraPostData:{}
		,xhrExtraPostDataPrefix:'extraPostData_'
		,sendMultiPartFormData:false
	});
	this.addEvents( //extend the xhr's progress events to here
		'loadstart',
		'progress',
		'abort',
		'error',
		'load',
		'loadend'
	);
	Ext.ux.XHRUpload.superclass.constructor.call(this);
};

Ext.extend(Ext.ux.XHRUpload, Ext.util.Observable,{
	send:function(config){
		Ext.apply(this, config);

		this.xhr = new XMLHttpRequest();
		this.xhr.addEventListener('loadstart', this.relayXHREvent.createDelegate(this), false);
		this.xhr.addEventListener('progress', this.relayXHREvent.createDelegate(this), false);
		this.xhr.addEventListener('progressabort', this.relayXHREvent.createDelegate(this), false);
		this.xhr.addEventListener('error', this.relayXHREvent.createDelegate(this), false);
		this.xhr.addEventListener('load', this.relayXHREvent.createDelegate(this), false);
		this.xhr.addEventListener('loadend', this.relayXHREvent.createDelegate(this), false);

		this.xhr.upload.addEventListener('loadstart', this.relayUploadEvent.createDelegate(this), false);
		this.xhr.upload.addEventListener('progress', this.relayUploadEvent.createDelegate(this), false);
		this.xhr.upload.addEventListener('progressabort', this.relayUploadEvent.createDelegate(this), false);
		this.xhr.upload.addEventListener('error', this.relayUploadEvent.createDelegate(this), false);
		this.xhr.upload.addEventListener('load', this.relayUploadEvent.createDelegate(this), false);
		this.xhr.upload.addEventListener('loadend', this.relayUploadEvent.createDelegate(this), false);

		this.xhr.open(this.method, this.url, true);

		if(typeof(FileReader) !== 'undefined' && this.sendMultiPartFormData ){
			//currently this is firefox only, chrome 6 will support this in the future
			this.reader = new FileReader();
			this.reader.addEventListener('load', this.sendFileUpload.createDelegate(this), false);
			this.reader.readAsBinaryString(this.file);
			return true;
		}
		//This will work in both Firefox 1.6 and Chrome 5
		this.xhr.overrideMimeType(this.contentTypeHeader);
		this.xhr.setRequestHeader(this.fileNameHeader, this.file.name);
		for(attr in this.extraPostData){
			this.xhr.setRequestHeader(this.xhrExtraPostDataPrefix + attr, this.extraPostData[attr]);
		}
		//xhr.setRequestHeader('X-File-Size', files.size); //this may be useful
		this.xhr.send(this.file);
		return true;

	}
	,sendFileUpload:function(){

		var boundary = (1000000000000+Math.floor(Math.random()*8999999999998)).toString(),
			data = '';

		for(attr in this.extraPostData){
			data += '--'+boundary + '\r\nContent-Disposition: form-data; name="' + attr + '"\r\ncontent-type: text/plain;\r\n\r\n'+this.extraPostData[attr]+'\r\n';
		}

		//window.btoa(binaryData)
		//Creates a base-64 encoded ASCII string from a string of binary data.
		//https://developer.mozilla.org/en/DOM/window.btoa
		//Firefox and Chrome only!!

		data += '--'+boundary + '\r\nContent-Disposition: form-data; name="' + this.filePostName + '"; filename="' + this.file.name + '"\r\nContent-Type: '+this.file.type+'\r\nContent-Transfer-Encoding: base64\r\n\r\n' + window.btoa(this.reader.result) + '\r\n'+'--'+boundary+'--\r\n\r\n';

		this.xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary='+boundary);
		this.xhr.send(data);
	}
	,relayUploadEvent:function(event){
		this.fireEvent('upload'+event.type, event);
	}
	,relayXHREvent:function(event){
		this.fireEvent(event.type, event);
	}
});



/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ns('Ext.ux.form');

/**
 * @class Ext.ux.form.FileUploadField
 * @extends Ext.form.TextField
 * Creates a file upload field.
 * @xtype fileuploadfield
 */
Ext.ux.form.FileUploadField = Ext.extend(Ext.form.TextField,  {
	/**
	 * @cfg {String} buttonText The button text to display on the upload button (defaults to
	 * 'Browse...').  Note that if you supply a value for {@link #buttonCfg}, the buttonCfg.text
	 * value will be used instead if available.
	 */
	buttonText: 'Browse...',
	/**
	 * @cfg {Boolean} buttonOnly True to display the file upload field as a button with no visible
	 * text field (defaults to false).  If true, all inherited TextField members will still be available.
	 */
	buttonOnly: false,
	/**
	 * @cfg {Number} buttonOffset The number of pixels of space reserved between the button and the text field
	 * (defaults to 3).  Note that this only applies if {@link #buttonOnly} = false.
	 */
	buttonOffset: 3,
	/**
	 * @cfg {Object} buttonCfg A standard {@link Ext.Button} config object.
	 */

	// private
	readOnly: true,

	/**
	 * @hide
	 * @method autoSize
	 */
	autoSize: Ext.emptyFn,

	// private
	initComponent: function(){
		Ext.ux.form.FileUploadField.superclass.initComponent.call(this);

		this.addEvents(
			/**
			 * @event fileselected
			 * Fires when the underlying file input field's value has changed from the user
			 * selecting a new file from the system file selection dialog.
			 * @param {Ext.ux.form.FileUploadField} this
			 * @param {String} value The file value returned by the underlying file input field
			 */
			'fileselected'
		);
	},

	// private
	onRender : function(ct, position){
		Ext.ux.form.FileUploadField.superclass.onRender.call(this, ct, position);

		this.wrap = this.el.wrap({cls:'x-form-field-wrap x-form-file-wrap'});
		this.el.addClass('x-form-file-text');
		this.el.dom.removeAttribute('name');
		this.createFileInput();

		var btnCfg = Ext.applyIf(this.buttonCfg || {}, {
			text: this.buttonText
		});
		this.button = new Ext.Button(Ext.apply(btnCfg, {
			renderTo: this.wrap,
			cls: 'x-form-file-btn' + (btnCfg.iconCls ? ' x-btn-icon' : '')
		}));

		if(this.buttonOnly){
			this.el.hide();
			this.wrap.setWidth(this.button.getEl().getWidth());
		}

		this.bindListeners();
		this.resizeEl = this.positionEl = this.wrap;
	},

	bindListeners: function(){
		this.fileInput.on({
			scope: this,
			mouseenter: function() {
				this.button.addClass(['x-btn-over','x-btn-focus'])
			},
			mouseleave: function(){
				this.button.removeClass(['x-btn-over','x-btn-focus','x-btn-click'])
			},
			mousedown: function(){
				this.button.addClass('x-btn-click')
			},
			mouseup: function(){
				this.button.removeClass(['x-btn-over','x-btn-focus','x-btn-click'])
			},
			change: function(){
				var v = this.fileInput.dom.value;
				this.setValue(v);
				this.fireEvent('fileselected', this, v);
			}
		});
	},

	createFileInput : function() {
		this.fileInput = this.wrap.createChild({
			id: this.getFileInputId(),
			name: this.name||this.getId(),
			cls: 'x-form-file',
			tag: 'input',
			type: 'file',
			size: 1
		});
	},

	reset : function(){
		this.fileInput.remove();
		this.createFileInput();
		this.bindListeners();
		Ext.ux.form.FileUploadField.superclass.reset.call(this);
	},

	// private
	getFileInputId: function(){
		return this.id + '-file';
	},

	// private
	onResize : function(w, h){
		Ext.ux.form.FileUploadField.superclass.onResize.call(this, w, h);

		this.wrap.setWidth(w);

		if(!this.buttonOnly){
			var w = this.wrap.getWidth() - this.button.getEl().getWidth() - this.buttonOffset;
			this.el.setWidth(w);
		}
	},

	// private
	onDestroy: function(){
		Ext.ux.form.FileUploadField.superclass.onDestroy.call(this);
		Ext.destroy(this.fileInput, this.button, this.wrap);
	},

	onDisable: function(){
		Ext.ux.form.FileUploadField.superclass.onDisable.call(this);
		this.doDisable(true);
	},

	onEnable: function(){
		Ext.ux.form.FileUploadField.superclass.onEnable.call(this);
		this.doDisable(false);

	},

	// private
	doDisable: function(disabled){
		this.fileInput.dom.disabled = disabled;
		this.button.setDisabled(disabled);
	},


	// private
	preFocus : Ext.emptyFn,

	// private
	alignErrorIcon : function(){
		this.errorIcon.alignTo(this.wrap, 'tl-tr', [2, 0]);
	}

});

Ext.reg('fileuploadfield', Ext.ux.form.FileUploadField);

// backwards compat
Ext.form.FileUploadField = Ext.ux.form.FileUploadField;



/**
 * SWFUpload: http://www.swfupload.org, http://swfupload.googlecode.com
 *
 * mmSWFUpload 1.0: Flash upload dialog - http://profandesign.se/swfupload/,  http://www.vinterwebb.se/
 *
 * SWFUpload is (c) 2006-2007 Lars Huring, Olov Nilzйn and Mammon Media and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * SWFUpload 2 is (c) 2007-2008 Jake Roberts and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */


/* ******************* */
/* Constructor & Init  */
/* ******************* */
var SWFUpload;

if (SWFUpload == undefined) {
	SWFUpload = function (settings) {
		this.initSWFUpload(settings);
	};
}

SWFUpload.prototype.initSWFUpload = function (settings) {
	try {
		this.customSettings = {};	// A container where developers can place their own settings associated with this instance.
		this.settings = settings;
		this.eventQueue = [];
		this.movieName = "SWFUpload_" + SWFUpload.movieCount++;
		this.movieElement = null;


		// Setup global control tracking
		SWFUpload.instances[this.movieName] = this;

		// Load the settings.  Load the Flash movie.
		this.initSettings();
		this.loadFlash();
		this.displayDebugInfo();
	} catch (ex) {
//		console.log('Exception!!');
		delete SWFUpload.instances[this.movieName];
		throw ex;
	}
};

/* *************** */
/* Static Members  */
/* *************** */
SWFUpload.instances = {};
SWFUpload.movieCount = 0;
SWFUpload.version = "2.2.0 2009-03-25";
SWFUpload.QUEUE_ERROR = {
	QUEUE_LIMIT_EXCEEDED	  		: -100,
	FILE_EXCEEDS_SIZE_LIMIT  		: -110,
	ZERO_BYTE_FILE			  		: -120,
	INVALID_FILETYPE		  		: -130
};
SWFUpload.UPLOAD_ERROR = {
	HTTP_ERROR				  		: -200,
	MISSING_UPLOAD_URL	      		: -210,
	IO_ERROR				  		: -220,
	SECURITY_ERROR			  		: -230,
	UPLOAD_LIMIT_EXCEEDED	  		: -240,
	UPLOAD_FAILED			  		: -250,
	SPECIFIED_FILE_ID_NOT_FOUND		: -260,
	FILE_VALIDATION_FAILED	  		: -270,
	FILE_CANCELLED			  		: -280,
	UPLOAD_STOPPED					: -290
};
SWFUpload.FILE_STATUS = {
	QUEUED		 : -1,
	IN_PROGRESS	 : -2,
	ERROR		 : -3,
	COMPLETE	 : -4,
	CANCELLED	 : -5
};
SWFUpload.BUTTON_ACTION = {
	SELECT_FILE  : -100,
	SELECT_FILES : -110,
	START_UPLOAD : -120
};
SWFUpload.CURSOR = {
	ARROW : -1,
	HAND : -2
};
SWFUpload.WINDOW_MODE = {
	WINDOW : "window",
	TRANSPARENT : "transparent",
	OPAQUE : "opaque"
};

// Private: takes a URL, determines if it is relative and converts to an absolute URL
// using the current site. Only processes the URL if it can, otherwise returns the URL untouched
SWFUpload.completeURL = function(url) {
	if (typeof(url) !== "string" || url.match(/^https?:\/\//i) || url.match(/^\//)) {
		return url;
	}

	var currentURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ":" + window.location.port : "");

	var indexSlash = window.location.pathname.lastIndexOf("/");
	if (indexSlash <= 0) {
		path = "/";
	} else {
		path = window.location.pathname.substr(0, indexSlash) + "/";
	}

	return /*currentURL +*/ path + url;

};


/* ******************** */
/* Instance Members  */
/* ******************** */

// Private: initSettings ensures that all the
// settings are set, getting a default value if one was not assigned.
SWFUpload.prototype.initSettings = function () {
	this.ensureDefault = function (settingName, defaultValue) {
		this.settings[settingName] = (this.settings[settingName] == undefined) ? defaultValue : this.settings[settingName];
	};

	// Upload backend settings
	this.ensureDefault("upload_url", "");
	this.ensureDefault("preserve_relative_urls", false);
	this.ensureDefault("file_post_name", "Filedata");
	this.ensureDefault("post_params", {});
	this.ensureDefault("use_query_string", false);
	this.ensureDefault("requeue_on_error", false);
	this.ensureDefault("http_success", []);
	this.ensureDefault("assume_success_timeout", 0);

	// File Settings
	this.ensureDefault("file_types", "*.*");
	this.ensureDefault("file_types_description", "All Files");
	this.ensureDefault("file_size_limit", 0);	// Default zero means "unlimited"
	this.ensureDefault("file_upload_limit", 0);
	this.ensureDefault("file_queue_limit", 0);

	// Flash Settings
	this.ensureDefault("flash_url", "swfupload.swf");
	this.ensureDefault("prevent_swf_caching", true);

	// Button Settings
	this.ensureDefault("button_image_url", "");
	this.ensureDefault("button_width", 1);
	this.ensureDefault("button_height", 1);
	this.ensureDefault("button_text", "");
	this.ensureDefault("button_text_style", "color: #000000; font-size: 16pt;");
	this.ensureDefault("button_text_top_padding", 0);
	this.ensureDefault("button_text_left_padding", 0);
	this.ensureDefault("button_action", SWFUpload.BUTTON_ACTION.SELECT_FILES);
	this.ensureDefault("button_disabled", false);
	this.ensureDefault("button_placeholder_id", "");
	this.ensureDefault("button_placeholder", null);
	this.ensureDefault("button_cursor", SWFUpload.CURSOR.ARROW);
	this.ensureDefault("button_window_mode", SWFUpload.WINDOW_MODE.WINDOW);

	// Debug Settings
	this.ensureDefault("debug", false);
	this.settings.debug_enabled = this.settings.debug;	// Here to maintain v2 API

	// Event Handlers
	this.settings.return_upload_start_handler = this.returnUploadStart;
	this.ensureDefault("swfupload_loaded_handler", null);
	this.ensureDefault("file_dialog_start_handler", null);
	this.ensureDefault("file_queued_handler", null);
	this.ensureDefault("file_queue_error_handler", null);
	this.ensureDefault("file_dialog_complete_handler", null);

	this.ensureDefault("upload_start_handler", null);
	this.ensureDefault("upload_progress_handler", null);
	this.ensureDefault("upload_error_handler", null);
	this.ensureDefault("upload_success_handler", null);
	this.ensureDefault("upload_complete_handler", null);

	this.ensureDefault("debug_handler", this.debugMessage);

	this.ensureDefault("custom_settings", {});

	// Other settings
	this.customSettings = this.settings.custom_settings;

	// Update the flash url if needed
	if (!!this.settings.prevent_swf_caching) {
		this.settings.flash_url = this.settings.flash_url + (this.settings.flash_url.indexOf("?") < 0 ? "?" : "&") + "preventswfcaching=" + new Date().getTime();
	}

	if (!this.settings.preserve_relative_urls) {
		//this.settings.flash_url = SWFUpload.completeURL(this.settings.flash_url);	// Don't need to do this one since flash doesn't look at it
		this.settings.upload_url = SWFUpload.completeURL(this.settings.upload_url);
		this.settings.button_image_url = SWFUpload.completeURL(this.settings.button_image_url);
	}

	delete this.ensureDefault;
};

// Private: loadFlash replaces the button_placeholder element with the flash movie.
SWFUpload.prototype.loadFlash = function () {
	var targetElement, tempParent;

	// Make sure an element with the ID we are going to use doesn't already exist
	if (document.getElementById(this.movieName) !== null) {
		throw "ID " + this.movieName + " is already in use. The Flash Object could not be added";
	}

	// Get the element where we will be placing the flash movie
	targetElement = document.getElementById(this.settings.button_placeholder_id) || this.settings.button_placeholder;

	if (targetElement == undefined) {
		throw "Could not find the placeholder element: " + this.settings.button_placeholder_id;
	}

	// Append the container and load the flash
	tempParent = document.createElement("div");
//	console.log('Adding flash HTML');
	tempParent.innerHTML = this.getFlashHTML();	// Using innerHTML is non-standard but the only sensible way to dynamically add Flash in IE (and maybe other browsers)
	targetElement.parentNode.replaceChild(tempParent.firstChild, targetElement);

	// Fix IE Flash/Form bug
	if (window[this.movieName] == undefined) {
		window[this.movieName] = this.getMovieElement();
	}

};

// Private: getFlashHTML generates the object tag needed to embed the flash in to the document
SWFUpload.prototype.getFlashHTML = function () {
	// Flash Satay object syntax: http://www.alistapart.com/articles/flashsatay
	return ['<object id="', this.movieName, '" type="application/x-shockwave-flash" data="', this.settings.flash_url, '" width="', this.settings.button_width, '" height="', this.settings.button_height, '" class="swfupload">',
		'<param name="wmode" value="', this.settings.button_window_mode, '" />',
		'<param name="movie" value="', this.settings.flash_url, '" />',
		'<param name="quality" value="high" />',
		'<param name="menu" value="false" />',
		'<param name="allowScriptAccess" value="always" />',
		'<param name="flashvars" value="' + this.getFlashVars() + '" />',
		'</object>'].join("");
};

// Private: getFlashVars builds the parameter string that will be passed
// to flash in the flashvars param.
SWFUpload.prototype.getFlashVars = function () {
	// Build a string from the post param object
	var paramString = this.buildParamString();
	var httpSuccessString = this.settings.http_success.join(",");

	// Build the parameter string
	return ["movieName=", encodeURIComponent(this.movieName),
		"&amp;uploadURL=", encodeURIComponent(this.settings.upload_url),
		"&amp;useQueryString=", encodeURIComponent(this.settings.use_query_string),
		"&amp;requeueOnError=", encodeURIComponent(this.settings.requeue_on_error),
		"&amp;httpSuccess=", encodeURIComponent(httpSuccessString),
		"&amp;assumeSuccessTimeout=", encodeURIComponent(this.settings.assume_success_timeout),
		"&amp;params=", encodeURIComponent(paramString),
		"&amp;filePostName=", encodeURIComponent(this.settings.file_post_name),
		"&amp;fileTypes=", encodeURIComponent(this.settings.file_types),
		"&amp;fileTypesDescription=", encodeURIComponent(this.settings.file_types_description),
		"&amp;fileSizeLimit=", encodeURIComponent(this.settings.file_size_limit),
		"&amp;fileUploadLimit=", encodeURIComponent(this.settings.file_upload_limit),
		"&amp;fileQueueLimit=", encodeURIComponent(this.settings.file_queue_limit),
		"&amp;debugEnabled=", encodeURIComponent(this.settings.debug_enabled),
		"&amp;buttonImageURL=", encodeURIComponent(this.settings.button_image_url),
		"&amp;buttonWidth=", encodeURIComponent(this.settings.button_width),
		"&amp;buttonHeight=", encodeURIComponent(this.settings.button_height),
		"&amp;buttonText=", encodeURIComponent(this.settings.button_text),
		"&amp;buttonTextTopPadding=", encodeURIComponent(this.settings.button_text_top_padding),
		"&amp;buttonTextLeftPadding=", encodeURIComponent(this.settings.button_text_left_padding),
		"&amp;buttonTextStyle=", encodeURIComponent(this.settings.button_text_style),
		"&amp;buttonAction=", encodeURIComponent(this.settings.button_action),
		"&amp;buttonDisabled=", encodeURIComponent(this.settings.button_disabled),
		"&amp;buttonCursor=", encodeURIComponent(this.settings.button_cursor)
	].join("");
};

// Public: getMovieElement retrieves the DOM reference to the Flash element added by SWFUpload
// The element is cached after the first lookup
SWFUpload.prototype.getMovieElement = function () {
	if (this.movieElement == undefined) {
		this.movieElement = document.getElementById(this.movieName);
	}

	if (this.movieElement === null) {
		throw "Could not find Flash element";
	}

	return this.movieElement;
};

// Private: buildParamString takes the name/value pairs in the post_params setting object
// and joins them up in to a string formatted "name=value&amp;name=value"
SWFUpload.prototype.buildParamString = function () {
	var postParams = this.settings.post_params;
	var paramStringPairs = [];

	if (typeof(postParams) === "object") {
		for (var name in postParams) {
			if (postParams.hasOwnProperty(name)) {
				paramStringPairs.push(encodeURIComponent(name.toString()) + "=" + encodeURIComponent(postParams[name].toString()));
			}
		}
	}

	return paramStringPairs.join("&amp;");
};

// Public: Used to remove a SWFUpload instance from the page. This method strives to remove
// all references to the SWF, and other objects so memory is properly freed.
// Returns true if everything was destroyed. Returns a false if a failure occurs leaving SWFUpload in an inconsistant state.
// Credits: Major improvements provided by steffen
SWFUpload.prototype.destroy = function () {
	try {
		// Make sure Flash is done before we try to remove it
		this.cancelUpload(null, false);


		// Remove the SWFUpload DOM nodes
		var movieElement = null;
		movieElement = this.getMovieElement();

		if (movieElement && typeof(movieElement.CallFunction) === "unknown") { // We only want to do this in IE
			// Loop through all the movie's properties and remove all function references (DOM/JS IE 6/7 memory leak workaround)
			for (var i in movieElement) {
				try {
					if (typeof(movieElement[i]) === "function") {
						movieElement[i] = null;
					}
				} catch (ex1) {}
			}

			// Remove the Movie Element from the page
			try {
				movieElement.parentNode.removeChild(movieElement);
			} catch (ex) {}
		}

		// Remove IE form fix reference
		window[this.movieName] = null;

		// Destroy other references
		SWFUpload.instances[this.movieName] = null;
		delete SWFUpload.instances[this.movieName];

		this.movieElement = null;
		this.settings = null;
		this.customSettings = null;
		this.eventQueue = null;
		this.movieName = null;


		return true;
	} catch (ex2) {
		return false;
	}
};


// Public: displayDebugInfo prints out settings and configuration
// information about this SWFUpload instance.
// This function (and any references to it) can be deleted when placing
// SWFUpload in production.
SWFUpload.prototype.displayDebugInfo = function () {
	this.debug(
		[
			"---SWFUpload Instance Info---\n",
			"Version: ", SWFUpload.version, "\n",
			"Movie Name: ", this.movieName, "\n",
			"Settings:\n",
			"\t", "upload_url:               ", this.settings.upload_url, "\n",
			"\t", "flash_url:                ", this.settings.flash_url, "\n",
			"\t", "use_query_string:         ", this.settings.use_query_string.toString(), "\n",
			"\t", "requeue_on_error:         ", this.settings.requeue_on_error.toString(), "\n",
			"\t", "http_success:             ", this.settings.http_success.join(", "), "\n",
			"\t", "assume_success_timeout:   ", this.settings.assume_success_timeout, "\n",
			"\t", "file_post_name:           ", this.settings.file_post_name, "\n",
			"\t", "post_params:              ", this.settings.post_params.toString(), "\n",
			"\t", "file_types:               ", this.settings.file_types, "\n",
			"\t", "file_types_description:   ", this.settings.file_types_description, "\n",
			"\t", "file_size_limit:          ", this.settings.file_size_limit, "\n",
			"\t", "file_upload_limit:        ", this.settings.file_upload_limit, "\n",
			"\t", "file_queue_limit:         ", this.settings.file_queue_limit, "\n",
			"\t", "debug:                    ", this.settings.debug.toString(), "\n",

			"\t", "prevent_swf_caching:      ", this.settings.prevent_swf_caching.toString(), "\n",

			"\t", "button_placeholder_id:    ", this.settings.button_placeholder_id.toString(), "\n",
			"\t", "button_placeholder:       ", (this.settings.button_placeholder ? "Set" : "Not Set"), "\n",
			"\t", "button_image_url:         ", this.settings.button_image_url.toString(), "\n",
			"\t", "button_width:             ", this.settings.button_width.toString(), "\n",
			"\t", "button_height:            ", this.settings.button_height.toString(), "\n",
			"\t", "button_text:              ", this.settings.button_text.toString(), "\n",
			"\t", "button_text_style:        ", this.settings.button_text_style.toString(), "\n",
			"\t", "button_text_top_padding:  ", this.settings.button_text_top_padding.toString(), "\n",
			"\t", "button_text_left_padding: ", this.settings.button_text_left_padding.toString(), "\n",
			"\t", "button_action:            ", this.settings.button_action.toString(), "\n",
			"\t", "button_disabled:          ", this.settings.button_disabled.toString(), "\n",

			"\t", "custom_settings:          ", this.settings.custom_settings.toString(), "\n",
			"Event Handlers:\n",
			"\t", "swfupload_loaded_handler assigned:  ", (typeof this.settings.swfupload_loaded_handler === "function").toString(), "\n",
			"\t", "file_dialog_start_handler assigned: ", (typeof this.settings.file_dialog_start_handler === "function").toString(), "\n",
			"\t", "file_queued_handler assigned:       ", (typeof this.settings.file_queued_handler === "function").toString(), "\n",
			"\t", "file_queue_error_handler assigned:  ", (typeof this.settings.file_queue_error_handler === "function").toString(), "\n",
			"\t", "upload_start_handler assigned:      ", (typeof this.settings.upload_start_handler === "function").toString(), "\n",
			"\t", "upload_progress_handler assigned:   ", (typeof this.settings.upload_progress_handler === "function").toString(), "\n",
			"\t", "upload_error_handler assigned:      ", (typeof this.settings.upload_error_handler === "function").toString(), "\n",
			"\t", "upload_success_handler assigned:    ", (typeof this.settings.upload_success_handler === "function").toString(), "\n",
			"\t", "upload_complete_handler assigned:   ", (typeof this.settings.upload_complete_handler === "function").toString(), "\n",
			"\t", "debug_handler assigned:             ", (typeof this.settings.debug_handler === "function").toString(), "\n"
		].join("")
	);
};

/* Note: addSetting and getSetting are no longer used by SWFUpload but are included
 the maintain v2 API compatibility
 */
// Public: (Deprecated) addSetting adds a setting value. If the value given is undefined or null then the default_value is used.
SWFUpload.prototype.addSetting = function (name, value, default_value) {
	if (value == undefined) {
		return (this.settings[name] = default_value);
	} else {
		return (this.settings[name] = value);
	}
};

// Public: (Deprecated) getSetting gets a setting. Returns an empty string if the setting was not found.
SWFUpload.prototype.getSetting = function (name) {
	if (this.settings[name] != undefined) {
		return this.settings[name];
	}

	return "";
};



// Private: callFlash handles function calls made to the Flash element.
// Calls are made with a setTimeout for some functions to work around
// bugs in the ExternalInterface library.
SWFUpload.prototype.callFlash = function (functionName, argumentArray) {
	argumentArray = argumentArray || [];

	var movieElement = this.getMovieElement();
	var returnValue, returnString;

	// Flash's method if calling ExternalInterface methods (code adapted from MooTools).
	try {
		returnString = movieElement.CallFunction('<invoke name="' + functionName + '" returntype="javascript">' + __flash__argumentsToXML(argumentArray, 0) + '</invoke>');
		returnValue = eval(returnString);
	} catch (ex) {
		throw "Call to " + functionName + " failed";
	}

	// Unescape file post param values
	if (returnValue != undefined && typeof returnValue.post === "object") {
		returnValue = this.unescapeFilePostParams(returnValue);
	}

	return returnValue;
};

/* *****************************
 -- Flash control methods --
 Your UI should use these
 to operate SWFUpload
 ***************************** */

// WARNING: this function does not work in Flash Player 10
// Public: selectFile causes a File Selection Dialog window to appear.  This
// dialog only allows 1 file to be selected.
SWFUpload.prototype.selectFile = function () {
	this.callFlash("SelectFile");
};

// WARNING: this function does not work in Flash Player 10
// Public: selectFiles causes a File Selection Dialog window to appear/ This
// dialog allows the user to select any number of files
// Flash Bug Warning: Flash limits the number of selectable files based on the combined length of the file names.
// If the selection name length is too long the dialog will fail in an unpredictable manner.  There is no work-around
// for this bug.
SWFUpload.prototype.selectFiles = function () {
	this.callFlash("SelectFiles");
};


// Public: startUpload starts uploading the first file in the queue unless
// the optional parameter 'fileID' specifies the ID
SWFUpload.prototype.startUpload = function (fileID) {
	this.callFlash("StartUpload", [fileID]);
};

// Public: cancelUpload cancels any queued file.  The fileID parameter may be the file ID or index.
// If you do not specify a fileID the current uploading file or first file in the queue is cancelled.
// If you do not want the uploadError event to trigger you can specify false for the triggerErrorEvent parameter.
SWFUpload.prototype.cancelUpload = function (fileID, triggerErrorEvent) {
	if (triggerErrorEvent !== false) {
		triggerErrorEvent = true;
	}
	this.callFlash("CancelUpload", [fileID, triggerErrorEvent]);
};

// Public: stopUpload stops the current upload and requeues the file at the beginning of the queue.
// If nothing is currently uploading then nothing happens.
SWFUpload.prototype.stopUpload = function () {
	this.callFlash("StopUpload");
};

/* ************************
 * Settings methods
 *   These methods change the SWFUpload settings.
 *   SWFUpload settings should not be changed directly on the settings object
 *   since many of the settings need to be passed to Flash in order to take
 *   effect.
 * *********************** */

// Public: getStats gets the file statistics object.
SWFUpload.prototype.getStats = function () {
	return this.callFlash("GetStats");
};

// Public: setStats changes the SWFUpload statistics.  You shouldn't need to
// change the statistics but you can.  Changing the statistics does not
// affect SWFUpload accept for the successful_uploads count which is used
// by the upload_limit setting to determine how many files the user may upload.
SWFUpload.prototype.setStats = function (statsObject) {
	this.callFlash("SetStats", [statsObject]);
};

// Public: getFile retrieves a File object by ID or Index.  If the file is
// not found then 'null' is returned.
SWFUpload.prototype.getFile = function (fileID) {
	if (typeof(fileID) === "number") {
		return this.callFlash("GetFileByIndex", [fileID]);
	} else {
		return this.callFlash("GetFile", [fileID]);
	}
};

// Public: addFileParam sets a name/value pair that will be posted with the
// file specified by the Files ID.  If the name already exists then the
// exiting value will be overwritten.
SWFUpload.prototype.addFileParam = function (fileID, name, value) {
	return this.callFlash("AddFileParam", [fileID, name, value]);
};

// Public: removeFileParam removes a previously set (by addFileParam) name/value
// pair from the specified file.
SWFUpload.prototype.removeFileParam = function (fileID, name) {
	this.callFlash("RemoveFileParam", [fileID, name]);
};

// Public: setUploadUrl changes the upload_url setting.
SWFUpload.prototype.setUploadURL = function (url) {
	this.settings.upload_url = url.toString();
	this.callFlash("SetUploadURL", [url]);
};

// Public: setPostParams changes the post_params setting
SWFUpload.prototype.setPostParams = function (paramsObject) {
	this.settings.post_params = paramsObject;
	this.callFlash("SetPostParams", [paramsObject]);
};

// Public: addPostParam adds post name/value pair.  Each name can have only one value.
SWFUpload.prototype.addPostParam = function (name, value) {
	this.settings.post_params[name] = value;
	this.callFlash("SetPostParams", [this.settings.post_params]);
};

// Public: removePostParam deletes post name/value pair.
SWFUpload.prototype.removePostParam = function (name) {
	delete this.settings.post_params[name];
	this.callFlash("SetPostParams", [this.settings.post_params]);
};

// Public: setFileTypes changes the file_types setting and the file_types_description setting
SWFUpload.prototype.setFileTypes = function (types, description) {
	this.settings.file_types = types;
	this.settings.file_types_description = description;
	this.callFlash("SetFileTypes", [types, description]);
};

// Public: setFileSizeLimit changes the file_size_limit setting
SWFUpload.prototype.setFileSizeLimit = function (fileSizeLimit) {
	this.settings.file_size_limit = fileSizeLimit;
	this.callFlash("SetFileSizeLimit", [fileSizeLimit]);
};

// Public: setFileUploadLimit changes the file_upload_limit setting
SWFUpload.prototype.setFileUploadLimit = function (fileUploadLimit) {
	this.settings.file_upload_limit = fileUploadLimit;
	this.callFlash("SetFileUploadLimit", [fileUploadLimit]);
};

// Public: setFileQueueLimit changes the file_queue_limit setting
SWFUpload.prototype.setFileQueueLimit = function (fileQueueLimit) {
	this.settings.file_queue_limit = fileQueueLimit;
	this.callFlash("SetFileQueueLimit", [fileQueueLimit]);
};

// Public: setFilePostName changes the file_post_name setting
SWFUpload.prototype.setFilePostName = function (filePostName) {
	this.settings.file_post_name = filePostName;
	this.callFlash("SetFilePostName", [filePostName]);
};

// Public: setUseQueryString changes the use_query_string setting
SWFUpload.prototype.setUseQueryString = function (useQueryString) {
	this.settings.use_query_string = useQueryString;
	this.callFlash("SetUseQueryString", [useQueryString]);
};

// Public: setRequeueOnError changes the requeue_on_error setting
SWFUpload.prototype.setRequeueOnError = function (requeueOnError) {
	this.settings.requeue_on_error = requeueOnError;
	this.callFlash("SetRequeueOnError", [requeueOnError]);
};

// Public: setHTTPSuccess changes the http_success setting
SWFUpload.prototype.setHTTPSuccess = function (http_status_codes) {
	if (typeof http_status_codes === "string") {
		http_status_codes = http_status_codes.replace(" ", "").split(",");
	}

	this.settings.http_success = http_status_codes;
	this.callFlash("SetHTTPSuccess", [http_status_codes]);
};

// Public: setHTTPSuccess changes the http_success setting
SWFUpload.prototype.setAssumeSuccessTimeout = function (timeout_seconds) {
	this.settings.assume_success_timeout = timeout_seconds;
	this.callFlash("SetAssumeSuccessTimeout", [timeout_seconds]);
};

// Public: setDebugEnabled changes the debug_enabled setting
SWFUpload.prototype.setDebugEnabled = function (debugEnabled) {
	this.settings.debug_enabled = debugEnabled;
	this.callFlash("SetDebugEnabled", [debugEnabled]);
};

// Public: setButtonImageURL loads a button image sprite
SWFUpload.prototype.setButtonImageURL = function (buttonImageURL) {
	if (buttonImageURL == undefined) {
		buttonImageURL = "";
	}

	this.settings.button_image_url = buttonImageURL;
	this.callFlash("SetButtonImageURL", [buttonImageURL]);
};

// Public: setButtonDimensions resizes the Flash Movie and button
SWFUpload.prototype.setButtonDimensions = function (width, height) {
	this.settings.button_width = width;
	this.settings.button_height = height;

	var movie = this.getMovieElement();
	if (movie != undefined) {
		movie.style.width = width + "px";
		movie.style.height = height + "px";
	}

	this.callFlash("SetButtonDimensions", [width, height]);
};
// Public: setButtonText Changes the text overlaid on the button
SWFUpload.prototype.setButtonText = function (html) {
	this.settings.button_text = html;
	this.callFlash("SetButtonText", [html]);
};
// Public: setButtonTextPadding changes the top and left padding of the text overlay
SWFUpload.prototype.setButtonTextPadding = function (left, top) {
	this.settings.button_text_top_padding = top;
	this.settings.button_text_left_padding = left;
	this.callFlash("SetButtonTextPadding", [left, top]);
};

// Public: setButtonTextStyle changes the CSS used to style the HTML/Text overlaid on the button
SWFUpload.prototype.setButtonTextStyle = function (css) {
	this.settings.button_text_style = css;
	this.callFlash("SetButtonTextStyle", [css]);
};
// Public: setButtonDisabled disables/enables the button
SWFUpload.prototype.setButtonDisabled = function (isDisabled) {
	this.settings.button_disabled = isDisabled;
	this.callFlash("SetButtonDisabled", [isDisabled]);
};
// Public: setButtonAction sets the action that occurs when the button is clicked
SWFUpload.prototype.setButtonAction = function (buttonAction) {
	this.settings.button_action = buttonAction;
	this.callFlash("SetButtonAction", [buttonAction]);
};

// Public: setButtonCursor changes the mouse cursor displayed when hovering over the button
SWFUpload.prototype.setButtonCursor = function (cursor) {
	this.settings.button_cursor = cursor;
	this.callFlash("SetButtonCursor", [cursor]);
};

/* *******************************
 Flash Event Interfaces
 These functions are used by Flash to trigger the various
 events.

 All these functions a Private.

 Because the ExternalInterface library is buggy the event calls
 are added to a queue and the queue then executed by a setTimeout.
 This ensures that events are executed in a determinate order and that
 the ExternalInterface bugs are avoided.
 ******************************* */

SWFUpload.prototype.queueEvent = function (handlerName, argumentArray) {
	// Warning: Don't call this.debug inside here or you'll create an infinite loop

	if (argumentArray == undefined) {
		argumentArray = [];
	} else if (!(argumentArray instanceof Array)) {
		argumentArray = [argumentArray];
	}

	var self = this;
	if (typeof this.settings[handlerName] === "function") {
		// Queue the event
		this.eventQueue.push(function () {
			this.settings[handlerName].apply(this, argumentArray);
		});

		// Execute the next queued event
		setTimeout(function () {
			self.executeNextEvent();
		}, 0);

	} else if (this.settings[handlerName] !== null) {
		throw "Event handler " + handlerName + " is unknown or is not a function";
	}
};

// Private: Causes the next event in the queue to be executed.  Since events are queued using a setTimeout
// we must queue them in order to garentee that they are executed in order.
SWFUpload.prototype.executeNextEvent = function () {
	// Warning: Don't call this.debug inside here or you'll create an infinite loop

	var  f = this.eventQueue ? this.eventQueue.shift() : null;
	if (typeof(f) === "function") {
		f.apply(this);
	}
};

// Private: unescapeFileParams is part of a workaround for a flash bug where objects passed through ExternalInterface cannot have
// properties that contain characters that are not valid for JavaScript identifiers. To work around this
// the Flash Component escapes the parameter names and we must unescape again before passing them along.
SWFUpload.prototype.unescapeFilePostParams = function (file) {
	var reg = /[$]([0-9a-f]{4})/i;
	var unescapedPost = {};
	var uk;

	if (file != undefined) {
		for (var k in file.post) {
			if (file.post.hasOwnProperty(k)) {
				uk = k;
				var match;
				while ((match = reg.exec(uk)) !== null) {
					uk = uk.replace(match[0], String.fromCharCode(parseInt("0x" + match[1], 16)));
				}
				unescapedPost[uk] = file.post[k];
			}
		}

		file.post = unescapedPost;
	}

	return file;
};

// Private: Called by Flash to see if JS can call in to Flash (test if External Interface is working)
SWFUpload.prototype.testExternalInterface = function () {
	try {
		return this.callFlash("TestExternalInterface");
	} catch (ex) {
		return false;
	}
};

// Private: This event is called by Flash when it has finished loading. Don't modify this.
// Use the swfupload_loaded_handler event setting to execute custom code when SWFUpload has loaded.
SWFUpload.prototype.flashReady = function () {
	// Check that the movie element is loaded correctly with its ExternalInterface methods defined
	var movieElement = this.getMovieElement();

	if (!movieElement) {
		this.debug("Flash called back ready but the flash movie can't be found.");
		return;
	}

	this.cleanUp(movieElement);

	this.queueEvent("swfupload_loaded_handler");
};

// Private: removes Flash added fuctions to the DOM node to prevent memory leaks in IE.
// This function is called by Flash each time the ExternalInterface functions are created.
SWFUpload.prototype.cleanUp = function (movieElement) {
	// Pro-actively unhook all the Flash functions
	try {
		if (this.movieElement && typeof(movieElement.CallFunction) === "unknown") { // We only want to do this in IE
			this.debug("Removing Flash functions hooks (this should only run in IE and should prevent memory leaks)");
			for (var key in movieElement) {
				try {
					if (typeof(movieElement[key]) === "function") {
						movieElement[key] = null;
					}
				} catch (ex) {
				}
			}
		}
	} catch (ex1) {

	}

	// Fix Flashes own cleanup code so if the SWFMovie was removed from the page
	// it doesn't display errors.
	window["__flash__removeCallback"] = function (instance, name) {
		try {
			if (instance) {
				instance[name] = null;
			}
		} catch (flashEx) {

		}
	};

};


/* This is a chance to do something before the browse window opens */
SWFUpload.prototype.fileDialogStart = function () {
	this.queueEvent("file_dialog_start_handler");
};


/* Called when a file is successfully added to the queue. */
SWFUpload.prototype.fileQueued = function (file) {
	file = this.unescapeFilePostParams(file);
	this.queueEvent("file_queued_handler", file);
};


/* Handle errors that occur when an attempt to queue a file fails. */
SWFUpload.prototype.fileQueueError = function (file, errorCode, message) {
	file = this.unescapeFilePostParams(file);
	this.queueEvent("file_queue_error_handler", [file, errorCode, message]);
};

/* Called after the file dialog has closed and the selected files have been queued.
 You could call startUpload here if you want the queued files to begin uploading immediately. */
SWFUpload.prototype.fileDialogComplete = function (numFilesSelected, numFilesQueued, numFilesInQueue) {
	this.queueEvent("file_dialog_complete_handler", [numFilesSelected, numFilesQueued, numFilesInQueue]);
};

SWFUpload.prototype.uploadStart = function (file) {
	file = this.unescapeFilePostParams(file);
	this.queueEvent("return_upload_start_handler", file);
};

SWFUpload.prototype.returnUploadStart = function (file) {
	var returnValue;
	if (typeof this.settings.upload_start_handler === "function") {
		file = this.unescapeFilePostParams(file);
		returnValue = this.settings.upload_start_handler.call(this, file);
	} else if (this.settings.upload_start_handler != undefined) {
		throw "upload_start_handler must be a function";
	}

	// Convert undefined to true so if nothing is returned from the upload_start_handler it is
	// interpretted as 'true'.
	if (returnValue === undefined) {
		returnValue = true;
	}

	returnValue = !!returnValue;

	this.callFlash("ReturnUploadStart", [returnValue]);
};



SWFUpload.prototype.uploadProgress = function (file, bytesComplete, bytesTotal) {
	file = this.unescapeFilePostParams(file);
	this.queueEvent("upload_progress_handler", [file, bytesComplete, bytesTotal]);
};

SWFUpload.prototype.uploadError = function (file, errorCode, message) {
	file = this.unescapeFilePostParams(file);
	this.queueEvent("upload_error_handler", [file, errorCode, message]);
};

SWFUpload.prototype.uploadSuccess = function (file, serverData, responseReceived) {
	file = this.unescapeFilePostParams(file);
	this.queueEvent("upload_success_handler", [file, serverData, responseReceived]);
};

SWFUpload.prototype.uploadComplete = function (file) {
	file = this.unescapeFilePostParams(file);
	this.queueEvent("upload_complete_handler", file);
};

/* Called by SWFUpload JavaScript and Flash functions when debug is enabled. By default it writes messages to the
 internal debug console.  You can override this event and have messages written where you want. */
SWFUpload.prototype.debug = function (message) {
	this.queueEvent("debug_handler", message);
};


/* **********************************
 Debug Console
 The debug console is a self contained, in page location
 for debug message to be sent.  The Debug Console adds
 itself to the body if necessary.

 The console is automatically scrolled as messages appear.

 If you are using your own debug handler or when you deploy to production and
 have debug disabled you can remove these functions to reduce the file size
 and complexity.
 ********************************** */

// Private: debugMessage is the default debug_handler.  If you want to print debug messages
// call the debug() function.  When overriding the function your own function should
// check to see if the debug setting is true before outputting debug information.
SWFUpload.prototype.debugMessage = function (message) {
	if (this.settings.debug) {
		var exceptionMessage, exceptionValues = [];

		// Check for an exception object and print it nicely
		if (typeof message === "object" && typeof message.name === "string" && typeof message.message === "string") {
			for (var key in message) {
				if (message.hasOwnProperty(key)) {
					exceptionValues.push(key + ": " + message[key]);
				}
			}
			exceptionMessage = exceptionValues.join("\n") || "";
			exceptionValues = exceptionMessage.split("\n");
			exceptionMessage = "EXCEPTION: " + exceptionValues.join("\nEXCEPTION: ");
			SWFUpload.Console.writeLine(exceptionMessage);
		} else {
			SWFUpload.Console.writeLine(message);
		}
	}
};

SWFUpload.Console = {};
SWFUpload.Console.writeLine = function (message) {
	var console, documentForm;

	try {
		console = document.getElementById("SWFUpload_Console");

		if (!console) {
			documentForm = document.createElement("form");
			document.getElementsByTagName("body")[0].appendChild(documentForm);

			console = document.createElement("textarea");
			console.id = "SWFUpload_Console";
			console.style.fontFamily = "monospace";
			console.setAttribute("wrap", "off");
			console.wrap = "off";
			console.style.overflow = "auto";
			console.style.width = "700px";
			console.style.height = "350px";
			console.style.margin = "5px";
			documentForm.appendChild(console);
		}

		console.value += message + "\n";

		console.scrollTop = console.scrollHeight - console.clientHeight;
	} catch (ex) {
		alert("Exception: " + ex.name + " Message: " + ex.message);
	}
};



/*
 SWFUpload.SWFObject Plugin

 Summary:
 This plugin uses SWFObject to embed SWFUpload dynamically in the page.  SWFObject provides accurate Flash Player detection and DOM Ready loading.
 This plugin replaces the Graceful Degradation plugin.

 Features:
 * swfupload_load_failed_hander event
 * swfupload_pre_load_handler event
 * minimum_flash_version setting (default: "9.0.28")
 * SWFUpload.onload event for early loading

 Usage:
 Provide handlers and settings as needed.  When using the SWFUpload.SWFObject plugin you should initialize SWFUploading
 in SWFUpload.onload rather than in window.onload.  When initialized this way SWFUpload can load earlier preventing the UI flicker
 that was seen using the Graceful Degradation plugin.

 <script type="text/javascript">
 var swfu;
 SWFUpload.onload = function () {
 swfu = new SWFUpload({
 minimum_flash_version: "9.0.28",
 swfupload_pre_load_handler: swfuploadPreLoad,
 swfupload_load_failed_handler: swfuploadLoadFailed
 });
 };
 </script>

 Notes:
 You must provide set minimum_flash_version setting to "8" if you are using SWFUpload for Flash Player 8.
 The swfuploadLoadFailed event is only fired if the minimum version of Flash Player is not met.  Other issues such as missing SWF files, browser bugs
 or corrupt Flash Player installations will not trigger this event.
 The swfuploadPreLoad event is fired as soon as the minimum version of Flash Player is found.  It does not wait for SWFUpload to load and can
 be used to prepare the SWFUploadUI and hide alternate content.
 swfobject's onDomReady event is cross-browser safe but will default to the window.onload event when DOMReady is not supported by the browser.
 Early DOM Loading is supported in major modern browsers but cannot be guaranteed for every browser ever made.
 */


/* SWFObject v2.1 <http://code.google.com/p/swfobject/>
 Copyright (c) 2007-2008 Geoff Stearns, Michael Williams, and Bobby van der Sluis
 This software is released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
var swfobject=function(){var b="undefined",Q="object",n="Shockwave Flash",p="ShockwaveFlash.ShockwaveFlash",P="application/x-shockwave-flash",m="SWFObjectExprInst",j=window,K=document,T=navigator,o=[],N=[],i=[],d=[],J,Z=null,M=null,l=null,e=false,A=false;var h=function(){var v=typeof K.getElementById!=b&&typeof K.getElementsByTagName!=b&&typeof K.createElement!=b,AC=[0,0,0],x=null;if(typeof T.plugins!=b&&typeof T.plugins[n]==Q){x=T.plugins[n].description;if(x&&!(typeof T.mimeTypes!=b&&T.mimeTypes[P]&&!T.mimeTypes[P].enabledPlugin)){x=x.replace(/^.*\s+(\S+\s+\S+$)/,"$1");AC[0]=parseInt(x.replace(/^(.*)\..*$/,"$1"),10);AC[1]=parseInt(x.replace(/^.*\.(.*)\s.*$/,"$1"),10);AC[2]=/r/.test(x)?parseInt(x.replace(/^.*r(.*)$/,"$1"),10):0}}else{if(typeof j.ActiveXObject!=b){var y=null,AB=false;try{y=new ActiveXObject(p+".7")}catch(t){try{y=new ActiveXObject(p+".6");AC=[6,0,21];y.AllowScriptAccess="always"}catch(t){if(AC[0]==6){AB=true}}if(!AB){try{y=new ActiveXObject(p)}catch(t){}}}if(!AB&&y){try{x=y.GetVariable("$version");if(x){x=x.split(" ")[1].split(",");AC=[parseInt(x[0],10),parseInt(x[1],10),parseInt(x[2],10)]}}catch(t){}}}}var AD=T.userAgent.toLowerCase(),r=T.platform.toLowerCase(),AA=/webkit/.test(AD)?parseFloat(AD.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,q=false,z=r?/win/.test(r):/win/.test(AD),w=r?/mac/.test(r):/mac/.test(AD);/*@cc_on q=true;@if(@_win32)z=true;@elif(@_mac)w=true;@end@*/return{w3cdom:v,pv:AC,webkit:AA,ie:q,win:z,mac:w}}();var L=function(){if(!h.w3cdom){return }f(H);if(h.ie&&h.win){try{K.write("<script id=__ie_ondomload defer=true src=//:><\/script>");J=C("__ie_ondomload");if(J){I(J,"onreadystatechange",S)}}catch(q){}}if(h.webkit&&typeof K.readyState!=b){Z=setInterval(function(){if(/loaded|complete/.test(K.readyState)){E()}},10)}if(typeof K.addEventListener!=b){K.addEventListener("DOMContentLoaded",E,null)}R(E)}();function S(){if(J.readyState=="complete"){J.parentNode.removeChild(J);E()}}function E(){if(e){return }if(h.ie&&h.win){var v=a("span");try{var u=K.getElementsByTagName("body")[0].appendChild(v);u.parentNode.removeChild(u)}catch(w){return }}e=true;if(Z){clearInterval(Z);Z=null}var q=o.length;for(var r=0;r<q;r++){o[r]()}}function f(q){if(e){q()}else{o[o.length]=q}}function R(r){if(typeof j.addEventListener!=b){j.addEventListener("load",r,false)}else{if(typeof K.addEventListener!=b){K.addEventListener("load",r,false)}else{if(typeof j.attachEvent!=b){I(j,"onload",r)}else{if(typeof j.onload=="function"){var q=j.onload;j.onload=function(){q();r()}}else{j.onload=r}}}}}function H(){var t=N.length;for(var q=0;q<t;q++){var u=N[q].id;if(h.pv[0]>0){var r=C(u);if(r){N[q].width=r.getAttribute("width")?r.getAttribute("width"):"0";N[q].height=r.getAttribute("height")?r.getAttribute("height"):"0";if(c(N[q].swfVersion)){if(h.webkit&&h.webkit<312){Y(r)}W(u,true)}else{if(N[q].expressInstall&&!A&&c("6.0.65")&&(h.win||h.mac)){k(N[q])}else{O(r)}}}}else{W(u,true)}}}function Y(t){var q=t.getElementsByTagName(Q)[0];if(q){var w=a("embed"),y=q.attributes;if(y){var v=y.length;for(var u=0;u<v;u++){if(y[u].nodeName=="DATA"){w.setAttribute("src",y[u].nodeValue)}else{w.setAttribute(y[u].nodeName,y[u].nodeValue)}}}var x=q.childNodes;if(x){var z=x.length;for(var r=0;r<z;r++){if(x[r].nodeType==1&&x[r].nodeName=="PARAM"){w.setAttribute(x[r].getAttribute("name"),x[r].getAttribute("value"))}}}t.parentNode.replaceChild(w,t)}}function k(w){A=true;var u=C(w.id);if(u){if(w.altContentId){var y=C(w.altContentId);if(y){M=y;l=w.altContentId}}else{M=G(u)}if(!(/%$/.test(w.width))&&parseInt(w.width,10)<310){w.width="310"}if(!(/%$/.test(w.height))&&parseInt(w.height,10)<137){w.height="137"}K.title=K.title.slice(0,47)+" - Flash Player Installation";var z=h.ie&&h.win?"ActiveX":"PlugIn",q=K.title,r="MMredirectURL="+j.location+"&MMplayerType="+z+"&MMdoctitle="+q,x=w.id;if(h.ie&&h.win&&u.readyState!=4){var t=a("div");x+="SWFObjectNew";t.setAttribute("id",x);u.parentNode.insertBefore(t,u);u.style.display="none";var v=function(){u.parentNode.removeChild(u)};I(j,"onload",v)}U({data:w.expressInstall,id:m,width:w.width,height:w.height},{flashvars:r},x)}}function O(t){if(h.ie&&h.win&&t.readyState!=4){var r=a("div");t.parentNode.insertBefore(r,t);r.parentNode.replaceChild(G(t),r);t.style.display="none";var q=function(){t.parentNode.removeChild(t)};I(j,"onload",q)}else{t.parentNode.replaceChild(G(t),t)}}function G(v){var u=a("div");if(h.win&&h.ie){u.innerHTML=v.innerHTML}else{var r=v.getElementsByTagName(Q)[0];if(r){var w=r.childNodes;if(w){var q=w.length;for(var t=0;t<q;t++){if(!(w[t].nodeType==1&&w[t].nodeName=="PARAM")&&!(w[t].nodeType==8)){u.appendChild(w[t].cloneNode(true))}}}}}return u}function U(AG,AE,t){var q,v=C(t);if(v){if(typeof AG.id==b){AG.id=t}if(h.ie&&h.win){var AF="";for(var AB in AG){if(AG[AB]!=Object.prototype[AB]){if(AB.toLowerCase()=="data"){AE.movie=AG[AB]}else{if(AB.toLowerCase()=="styleclass"){AF+=' class="'+AG[AB]+'"'}else{if(AB.toLowerCase()!="classid"){AF+=" "+AB+'="'+AG[AB]+'"'}}}}}var AD="";for(var AA in AE){if(AE[AA]!=Object.prototype[AA]){AD+='<param name="'+AA+'" value="'+AE[AA]+'" />'}}v.outerHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+AF+">"+AD+"</object>";i[i.length]=AG.id;q=C(AG.id)}else{if(h.webkit&&h.webkit<312){var AC=a("embed");AC.setAttribute("type",P);for(var z in AG){if(AG[z]!=Object.prototype[z]){if(z.toLowerCase()=="data"){AC.setAttribute("src",AG[z])}else{if(z.toLowerCase()=="styleclass"){AC.setAttribute("class",AG[z])}else{if(z.toLowerCase()!="classid"){AC.setAttribute(z,AG[z])}}}}}for(var y in AE){if(AE[y]!=Object.prototype[y]){if(y.toLowerCase()!="movie"){AC.setAttribute(y,AE[y])}}}v.parentNode.replaceChild(AC,v);q=AC}else{var u=a(Q);u.setAttribute("type",P);for(var x in AG){if(AG[x]!=Object.prototype[x]){if(x.toLowerCase()=="styleclass"){u.setAttribute("class",AG[x])}else{if(x.toLowerCase()!="classid"){u.setAttribute(x,AG[x])}}}}for(var w in AE){if(AE[w]!=Object.prototype[w]&&w.toLowerCase()!="movie"){F(u,w,AE[w])}}v.parentNode.replaceChild(u,v);q=u}}}return q}function F(t,q,r){var u=a("param");u.setAttribute("name",q);u.setAttribute("value",r);t.appendChild(u)}function X(r){var q=C(r);if(q&&(q.nodeName=="OBJECT"||q.nodeName=="EMBED")){if(h.ie&&h.win){if(q.readyState==4){B(r)}else{j.attachEvent("onload",function(){B(r)})}}else{q.parentNode.removeChild(q)}}}function B(t){var r=C(t);if(r){for(var q in r){if(typeof r[q]=="function"){r[q]=null}}r.parentNode.removeChild(r)}}function C(t){var q=null;try{q=K.getElementById(t)}catch(r){}return q}function a(q){return K.createElement(q)}function I(t,q,r){t.attachEvent(q,r);d[d.length]=[t,q,r]}function c(t){var r=h.pv,q=t.split(".");q[0]=parseInt(q[0],10);q[1]=parseInt(q[1],10)||0;q[2]=parseInt(q[2],10)||0;return(r[0]>q[0]||(r[0]==q[0]&&r[1]>q[1])||(r[0]==q[0]&&r[1]==q[1]&&r[2]>=q[2]))?true:false}function V(v,r){if(h.ie&&h.mac){return }var u=K.getElementsByTagName("head")[0],t=a("style");t.setAttribute("type","text/css");t.setAttribute("media","screen");if(!(h.ie&&h.win)&&typeof K.createTextNode!=b){t.appendChild(K.createTextNode(v+" {"+r+"}"))}u.appendChild(t);if(h.ie&&h.win&&typeof K.styleSheets!=b&&K.styleSheets.length>0){var q=K.styleSheets[K.styleSheets.length-1];if(typeof q.addRule==Q){q.addRule(v,r)}}}function W(t,q){var r=q?"visible":"hidden";if(e&&C(t)){C(t).style.visibility=r}else{V("#"+t,"visibility:"+r)}}function g(s){var r=/[\\\"<>\.;]/;var q=r.exec(s)!=null;return q?encodeURIComponent(s):s}var D=function(){if(h.ie&&h.win){window.attachEvent("onunload",function(){var w=d.length;for(var v=0;v<w;v++){d[v][0].detachEvent(d[v][1],d[v][2])}var t=i.length;for(var u=0;u<t;u++){X(i[u])}for(var r in h){h[r]=null}h=null;for(var q in swfobject){swfobject[q]=null}swfobject=null})}}();return{registerObject:function(u,q,t){if(!h.w3cdom||!u||!q){return }var r={};r.id=u;r.swfVersion=q;r.expressInstall=t?t:false;N[N.length]=r;W(u,false)},getObjectById:function(v){var q=null;if(h.w3cdom){var t=C(v);if(t){var u=t.getElementsByTagName(Q)[0];if(!u||(u&&typeof t.SetVariable!=b)){q=t}else{if(typeof u.SetVariable!=b){q=u}}}}return q},embedSWF:function(x,AE,AB,AD,q,w,r,z,AC){if(!h.w3cdom||!x||!AE||!AB||!AD||!q){return }AB+="";AD+="";if(c(q)){W(AE,false);var AA={};if(AC&&typeof AC===Q){for(var v in AC){if(AC[v]!=Object.prototype[v]){AA[v]=AC[v]}}}AA.data=x;AA.width=AB;AA.height=AD;var y={};if(z&&typeof z===Q){for(var u in z){if(z[u]!=Object.prototype[u]){y[u]=z[u]}}}if(r&&typeof r===Q){for(var t in r){if(r[t]!=Object.prototype[t]){if(typeof y.flashvars!=b){y.flashvars+="&"+t+"="+r[t]}else{y.flashvars=t+"="+r[t]}}}}f(function(){U(AA,y,AE);if(AA.id==AE){W(AE,true)}})}else{if(w&&!A&&c("6.0.65")&&(h.win||h.mac)){A=true;W(AE,false);f(function(){var AF={};AF.id=AF.altContentId=AE;AF.width=AB;AF.height=AD;AF.expressInstall=w;k(AF)})}}},getFlashPlayerVersion:function(){return{major:h.pv[0],minor:h.pv[1],release:h.pv[2]}},hasFlashPlayerVersion:c,createSWF:function(t,r,q){if(h.w3cdom){return U(t,r,q)}else{return undefined}},removeSWF:function(q){if(h.w3cdom){X(q)}},createCSS:function(r,q){if(h.w3cdom){V(r,q)}},addDomLoadEvent:f,addLoadEvent:R,getQueryParamValue:function(v){var u=K.location.search||K.location.hash;if(v==null){return g(u)}if(u){var t=u.substring(1).split("&");for(var r=0;r<t.length;r++){if(t[r].substring(0,t[r].indexOf("="))==v){return g(t[r].substring((t[r].indexOf("=")+1)))}}}return""},expressInstallCallback:function(){if(A&&M){var q=C(m);if(q){q.parentNode.replaceChild(M,q);if(l){W(l,true);if(h.ie&&h.win){M.style.display="block"}}M=null;l=null;A=false}}}}}();



var SWFUpload;
if (typeof(SWFUpload) === "function") {
	SWFUpload.onload = function () {};

	swfobject.addDomLoadEvent(function () {
		if (typeof(SWFUpload.onload) === "function") {
			SWFUpload.onload.call(window);
		}
	});

	SWFUpload.prototype.initSettings = (function (oldInitSettings) {
		return function () {
			if (typeof(oldInitSettings) === "function") {
				oldInitSettings.call(this);
			}

			this.ensureDefault = function (settingName, defaultValue) {
				this.settings[settingName] = (this.settings[settingName] == undefined) ? defaultValue : this.settings[settingName];
			};

			this.ensureDefault("minimum_flash_version", "9.0.28");
			this.ensureDefault("swfupload_pre_load_handler", null);
			this.ensureDefault("swfupload_load_failed_handler", null);

			delete this.ensureDefault;

		};
	})(SWFUpload.prototype.initSettings);


	SWFUpload.prototype.loadFlash = function (oldLoadFlash) {
		return function () {
			var hasFlash = swfobject.hasFlashPlayerVersion(this.settings.minimum_flash_version);

			if (hasFlash) {
				this.queueEvent("swfupload_pre_load_handler");
				if (typeof(oldLoadFlash) === "function") {
					oldLoadFlash.call(this);
				}
			} else {
				this.queueEvent("swfupload_load_failed_handler");
			}
		};

	}(SWFUpload.prototype.loadFlash);

	SWFUpload.prototype.displayDebugInfo = function (oldDisplayDebugInfo) {
		return function () {
			if (typeof(oldDisplayDebugInfo) === "function") {
				oldDisplayDebugInfo.call(this);
			}

			this.debug(
				[
					"SWFUpload.SWFObject Plugin settings:", "\n",
					"\t", "minimum_flash_version:                      ", this.settings.minimum_flash_version, "\n",
					"\t", "swfupload_pre_load_handler assigned:     ", (typeof(this.settings.swfupload_pre_load_handler) === "function").toString(), "\n",
					"\t", "swfupload_load_failed_handler assigned:     ", (typeof(this.settings.swfupload_load_failed_handler) === "function").toString(), "\n",
				].join("")
			);
		};
	}(SWFUpload.prototype.displayDebugInfo);
}
