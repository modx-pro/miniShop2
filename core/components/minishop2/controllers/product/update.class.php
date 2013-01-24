<?php
class msProductUpdateManagerController extends ResourceUpdateManagerController {
	/**
	 * Returns language topics
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('resource','minishop2:default','tickets:default');
	}


	/**
	 * Check for any permissions or requirements to load page
	 * @return bool
	 */
	public function checkPermissions() {
		return $this->modx->hasPermission('msproduct_save');
	}


	/**
	 * Register custom CSS/JS for the page
	 * @return void
	 */
	public function loadCustomCssJs() {
		$mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);

		$minishopAssetsUrl = $this->modx->getOption('minishop2.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/minishop2/');
		$connectorUrl = $minishopAssetsUrl.'connector.php';
		$minishopJsUrl = $minishopAssetsUrl.'js/mgr/';

		$showComments = $this->modx->getCount('transport.modTransportPackage', array('package_name' => 'Tickets')) && $this->modx->getOption('ms2_category_show_comments')? 1 : 0;

		$this->addJavascript($mgrUrl.'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl.'assets/modext/sections/resource/update.js');
		$this->addJavascript($minishopJsUrl.'minishop2.js');
		$this->addLastJavascript($minishopJsUrl.'product/update.js');

		if ($showComments) {$this->loadTickets();}

		$this->addLastJavascript($minishopJsUrl.'product/update.js');
		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		miniShop2.config = {
			assets_url: "'.$minishopAssetsUrl.'"
			,connector_url: "'.$connectorUrl.'"
			,show_comments: '.$showComments.'
		}
		MODx.config.publish_document = "'.$this->canPublish.'";
		MODx.onDocFormRender = "'.$this->onDocFormRender.'";
		MODx.ctx = "'.$this->ctx.'";
		Ext.onReady(function() {
			MODx.load({
				xtype: "minishop2-page-product-update"
				,resource: "'.$this->resource->get('id').'"
				,record: '.$this->modx->toJSON($this->resourceArray).'
				,publish_document: "'.$this->canPublish.'"
				,preview_url: "'.$this->previewUrl.'"
				,locked: '.($this->locked ? 1 : 0).'
				,lockedText: "'.$this->lockedText.'"
				,canSave: '.($this->canSave ? 1 : 0).'
				,canEdit: '.($this->canEdit ? 1 : 0).'
				,canCreate: '.($this->canCreate ? 1 : 0).'
				,canDuplicate: '.($this->canDuplicate ? 1 : 0).'
				,canDelete: '.($this->canDelete ? 1 : 0).'
				,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
				,mode: "update"
			});
		});
		// ]]>
		</script>');
		/* load RTE */
		$this->loadRichTextEditor();
	}


	/*
	 * Loads component Tickets for displaying comments
	 *
	 * */
	public function loadTickets() {
		$ticketsAssetsUrl = $this->modx->getOption('tickets.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/tickets/');
		$connectorUrl = $ticketsAssetsUrl.'connector.php';
		$ticketsJsUrl = $ticketsAssetsUrl.'js/mgr/';

		$this->addJavascript($ticketsJsUrl.'tickets.js');
		$this->addJavascript($ticketsJsUrl.'comment/comments.common.js');
		$this->addJavascript($ticketsJsUrl.'comment/comments.grid.js');
		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		Tickets.config = {
			assets_url: "'.$ticketsAssetsUrl.'"
			,connector_url: "'.$connectorUrl.'"
		};
		// ]]>
		</script>');
	}

}