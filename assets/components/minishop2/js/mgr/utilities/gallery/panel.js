miniShop2.panel.UtilitiesGallery = function (config) {
	config = config || {};

	Ext.apply(config, {
		cls: 'container form-with-labels',
		autoHeight: true,
		url: miniShop2.config.connector_url,
		saveMsg: _('ms2_utilities_gallery_updating'),

		progress: true,
		baseParams: {
			action: 'mgr/utilities/gallery/update'
		},
		items: [{
			layout: 'form',
			cls: 'main-wrapper',
			labelWidth: 200,
			labelAlign: 'left',
			border: false,
			buttonAlign: 'left',
			style: 'padding: 0 0 0 7px',
			items: [
				{
					html: String.format(
						_('ms2_utilities_gallery_information'),
						miniShop2.config.utility_gallery_source_name,
						miniShop2.config.utility_gallery_source_id,
						miniShop2.config.utility_gallery_total_products,
						miniShop2.config.utility_gallery_total_products_files
					),
				},
				{
					xtype: 'fieldset',
					title: _('ms2_utilities_params'),
					id: 'ms2-utilities-gallery-params',
					cls: 'x-fieldset-checkbox-toggle',
					style: 'margin: 5px 0 15px ',
					collapsible: true,
					collapsed: true,
					stateful: true,
					labelAlign: 'top',
					stateEvents: ['collapse', 'expand'],
					items: [
						{
							html: miniShop2.config.utility_gallery_thumbnails
						},
					]
				},
				{
					name: 'limit',
					xtype: 'numberfield',
					value: 10,
					width: 80,
					fieldLabel: _('ms2_utilities_gallery_for_step')
				},
				{
					name: 'offset',
					xtype: 'numberfield',
					value: 0,
					fieldLabel: _('ms2_utilities_gallery_step_offset')
				},
				{
					xtype: 'button',
					style: 'margin: 15px 0 0 2px',
					text: '<i class="icon icon-refresh"></i> &nbsp;' + _('ms2_utilities_gallery_refresh'),
					handler: function () {
						this.submit(this);
					}, scope: this
				},
				{
					style: 'padding: 15px 0',
					html: '\
						<div id="ms-utility-gallery-range_outer">\
							<div class="ms-utility-gallery-labels"><span id="ms-utility-gallery-label">0%</span><span id="ms-utility-gallery-iteration"></span></div>\
							<div id="ms-utility-gallery-progress"><span id="ms-utility-gallery-progress-bar"></span></div>\
						</div>\
					'
				}
			]
		}],
		listeners: {
			success: {
				fn: function (response) {
					var data = response.result.object;
					var form = this.getForm();
					this.updateProgress(data);

					if (!data.done) {
						form.setValues({
							offset: Number(data.offset)
						});
						this.submit(this);
					}
					else {
						MODx.msg.status({
							title: _('ms2_utilities_gallery_done'),
							message: _('ms2_utilities_gallery_done_message'),
							delay: 5
						});
					}
				}, scope: this
			}
		}
	});
	miniShop2.panel.UtilitiesGallery.superclass.constructor.call(this, config);
};

Ext.extend(miniShop2.panel.UtilitiesGallery, MODx.FormPanel, {

	updateProgress: function (data) {
		const progressblock = document.getElementById('ms-utility-gallery-range_outer');
		const progresslabel = document.getElementById('ms-utility-gallery-label');
		const progressbar = document.getElementById('ms-utility-gallery-progress-bar');
		const progressiteration = document.getElementById('ms-utility-gallery-iteration');
		progressblock.style.visibility = 'visible';

		if (data.done) {
			progresslabel.innerHTML = '100%';
			progressbar.style.width = '100%';
			progressiteration.style.visibility = 'hidden';
		} else {
			let progress = (parseFloat((data.offset / data.total) * 100)).toFixed(2);
			progresslabel.innerHTML = progress + '%';
			progressbar.style.width = progress + '%';

			// count iterations
			const totalIterations = Math.ceil(data.total / data.limit);
			const currentIteration = data.offset / data.limit;
			progressiteration.innerHTML = currentIteration + "/" + totalIterations;
		}
	}
});
Ext.reg('minishop2-utilities-gallery', miniShop2.panel.UtilitiesGallery);
