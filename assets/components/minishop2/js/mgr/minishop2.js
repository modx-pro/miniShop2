var miniShop2 = function(config) {
	config = config || {};
	miniShop2.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config:{},view:{},keymap:{}, plugin:{}

    ,getOptCaption: function(field) {
        var opts = miniShop2.config.option_fields;
        if (!opts) return '';
        for (var i= 0, len = opts.length; i<len; i++) {
            if (opts[i]['key'] == field) {
                return opts[i]['caption'];
            }
        }
        return '';
    }
});
Ext.reg('minishop2',miniShop2);

miniShop2 = new miniShop2();

miniShop2.PanelSpacer = { html: '<br />' ,border: false, cls: 'minishop2-panel-spacer' };

// DnD + SelectModel in grid
Ext.override(Ext.dd.DragSource, {
	handleMouseDown: function(e) {
		var t = e.getTarget();
		var classes = t.className.split(' ');
		if (classes.indexOf('x-grid3-row-checker') !== -1) {
			return false;
		}

		if (!this.dragging) {
			var data = this.getDragData(e);
			if (data && this.onBeforeDrag(data, e) !== false) {
				this.dragData = data;
				this.proxy.stop();
				Ext.dd.DragSource.superclass.handleMouseDown.apply(this, arguments);
			}
		}
		return true;
	}
});