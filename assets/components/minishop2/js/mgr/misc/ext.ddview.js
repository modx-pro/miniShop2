MODx.DataView.dropZone = function(view, config){
	config = config || {};
	this.view = view;
	var ddGroup = config.ddGroup || 'dataviewdd';
	var dd;
	if (Ext.isArray(ddGroup)){
		dd = ddGroup.shift();
	} else {
		dd = ddGroup;
		ddGroup = null;
	}
	MODx.DataView.dropZone.superclass.constructor.call(this, view.getEl(), { containerScroll: true/*, ddGroup: dd */});
};
Ext.extend(MODx.DataView.dropZone, Ext.dd.DropZone,{
	getTargetFromEvent: function(e) {
		return e.getTarget('.modx-pb-thumb-wrap');
	}

	,onNodeEnter : function(target, dd, e, data) {
		Ext.fly(target).addClass('x-view-selected');
	}
	,onNodeOut : function(target, dd, e, data) {
		Ext.fly(target).removeClass('x-view-selected');
	}
	,onNodeOver : function(target, dd, e, data) {
		return Ext.dd.DropZone.prototype.dropAllowed && (target != data.nodes[0]);
	}

	,onNodeDrop : function(target, dd, e, data) {
		var targetNode = this.view.getRecord(target);
		var sourceNode = this.view.getRecord(data.nodes[0]);
		if (sourceNode == targetNode) { return false; }
		var targetElement = Ext.get(target);
		var sourceElement = Ext.get(data.nodes[0]);
		sourceElement.insertBefore(targetElement);
		this.view.fireEvent('sort',{
			target: targetNode
			,source: sourceNode
			,event: e
			,dd: dd
		});
		return true;
	}

});

MODx.DataView.dragZone = function(view,config) {
	config = config || {};
	this.view = view;
	MODx.DataView.dragZone.superclass.constructor.call(this,view.getEl());
};
Ext.extend(MODx.DataView.dragZone,Ext.dd.DragZone,{
	getDragData : function(e){
		var target = e.getTarget('.modx-pb-thumb-wrap');
		if(target){
			var view = this.view;
			if(!view.isSelected(target)){
				view.onClick(e);
			}
			var selNodes = view.getSelectedNodes();
			var dragData = {
				nodes: selNodes
			};
			if(selNodes.length == 1){
				dragData.ddel = target;
				dragData.single = true;
			}else{
				var div = document.createElement('div'); // create the multi element drag "ghost"
				div.className = 'multi-proxy';
				for(var i = 0, len = selNodes.length; i < len; i++){
					div.appendChild(selNodes[i].firstChild.firstChild.cloneNode(true)); // image nodes only
					if((i+1) % 3 == 0){
						div.appendChild(document.createElement('br'));
					}
				}
				var count = document.createElement('div'); // selected image count
				count.innerHTML = _('gallery.images_selected',{ count: i });
				div.appendChild(count);

				dragData.ddel = div;
				dragData.multi = true;
			}
			return dragData;
		}
		return false;
	}


	// the default action is to "highlight" after a bad drop
	// but since an image can't be highlighted, let's frame it
	,afterRepair:function(){
		for(var i = 0, len = this.dragData.nodes.length; i < len; i++){
			Ext.fly(this.dragData.nodes[i]).frame('#8db2e3', 1);
		}
		this.dragging = false;
	}

	// override the default repairXY with one offset for the margins and padding
	,getRepairXY : function(e){
		if(!this.dragData.multi){
			var xy = Ext.Element.fly(this.dragData.ddel).getXY();
			xy[0]+=3;xy[1]+=3;
			return xy;
		}
		return false;
	}
})