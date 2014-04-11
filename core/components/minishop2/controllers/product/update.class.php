<?php
class msProductUpdateManagerController extends ResourceUpdateManagerController {
	/* @var msProduct $resource */
	public $resource;


	/**
	 * Returns language topics
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('resource','minishop2:default','minishop2:product','minishop2:manager','tickets:default');
	}


	/**
	 * Check for any permissions or requirements to load page
	 * @return bool
	 */
	public function checkPermissions() {
		return $this->modx->hasPermission('edit_document');
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
		$minishopImgUrl = $minishopAssetsUrl.'img/mgr/';

		// Customizable product fields feature
		$product_fields = array_merge($this->resource->getAllFieldsNames(), array('syncsite'));
		$product_data_fields = $this->resource->getDataFieldsNames();

		if (!$product_main_fields = $this->modx->getOption('ms2_product_main_fields')) {
			$product_main_fields = 'pagetitle,longtitle,introtext,content,publishedon,pub_date,unpub_date,template,parent,alias,menutitle,searchable,cacheable,richtext,uri_override,uri,hidemenu,show_in_tree';
		}
		$product_main_fields = array_map('trim', explode(',',$product_main_fields));
		$product_main_fields = array_values(array_intersect($product_main_fields, $product_fields));

		if (!$product_extra_fields = $this->modx->getOption('ms2_product_extra_fields')) {
			$product_extra_fields = 'article,price,old_price,weight,color,remains,reserved,vendor,made_in,tags';
		}
		$product_extra_fields = array_map('trim', explode(',',$product_extra_fields));
		$product_extra_fields = array_values(array_intersect($product_extra_fields, $product_fields));
		//---

		$showComments = class_exists('Ticket') && $this->modx->getOption('ms2_product_show_comments') ? 1 : 0;

		$this->addJavascript($mgrUrl.'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($minishopJsUrl.'product/product.tv.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl.'assets/modext/sections/resource/update.js');
		$this->addJavascript($minishopJsUrl.'minishop2.js');
		$this->addJavascript($minishopJsUrl.'misc/ms2.combo.js');
		$this->addJavascript($minishopJsUrl.'misc/ms2.utils.js');
		$this->addJavascript($minishopJsUrl.'misc/plupload/plupload.full.js');
		$this->addJavascript($minishopJsUrl.'misc/ext.ddview.js');
		$this->addLastJavascript($minishopJsUrl.'product/category.tree.js');
		$this->addLastJavascript($minishopJsUrl.'product/gallery.panel.js');
		$this->addLastJavascript($minishopJsUrl.'product/links.grid.js');
		$this->addLastJavascript($minishopJsUrl.'product/product.common.js');
		$this->addLastJavascript($minishopJsUrl.'product/update.js');

		$this->prepareFields();

		if ($showComments) {$this->loadTickets();}
		if ($this->resource instanceof msProduct) {
			$neighborhood = $this->resource->getNeighborhood();
		}

		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		miniShop2.config = {
			assets_url: "'.$minishopAssetsUrl.'"
			,connector_url: "'.$connectorUrl.'"
			,show_comments: '.$showComments.'
			,logo_small: "'.$minishopImgUrl.'ms2_small.png"
			,main_fields: '.json_encode($product_main_fields).'
			,extra_fields: '.json_encode($product_extra_fields).'
			,vertical_tabs: '.$this->modx->getOption('ms2_product_vertical_tabs', null, true).'
			,product_tab_extra: '.$this->modx->getOption('ms2_product_tab_extra', null, true).'
			,product_tab_gallery: '.$this->modx->getOption('ms2_product_tab_gallery', null, true).'
			,product_tab_links: '.$this->modx->getOption('ms2_product_tab_links', null, true).'
			,data_fields: '.json_encode($product_data_fields).'
			,additional_fields: []
			,media_source: '.json_encode($this->getSourceProperties()).'
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
				,canPublish: '.($this->canPublish ? 1 : 0).'
				,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
				,next_page: '.(!empty($neighborhood['right'][0]) ? $neighborhood['right'][0] : 0).'
				,prev_page: '.(!empty($neighborhood['left'][0]) ? $neighborhood['left'][0] : 0).'
				,up_page: '.$this->resource->parent.'
				,mode: "update"
			});
		});
		// ]]>
		</script>');
		/* load RTE */
		$this->loadRichTextEditor();
		$this->modx->invokeEvent('msOnManagerCustomCssJs',array('controller' => &$this, 'page' => 'product_update'));
		$this->loadPlugins();
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
		$this->addLastJavascript($ticketsJsUrl.'misc/utils.js');
		$this->addLastJavascript($ticketsJsUrl.'comment/comments.common.js');
		$this->addLastJavascript($ticketsJsUrl.'comment/comments.grid.js');
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


	/**
	 * Setup permissions for this page
	 * @return void
	 */
	public function setPermissions() {
		if ($this->canSave) {
			$this->canSave = $this->resource->checkPolicy('save');
		}
		$this->canEdit = $this->modx->hasPermission('edit_document');
		$this->canCreate = $this->modx->hasPermission('new_document');
		$this->canPublish = $this->modx->hasPermission('publish_document');
		$this->canDelete = ($this->modx->hasPermission('delete_document') && $this->resource->checkPolicy(array('save' => true, 'delete' => true)));
		$this->canDuplicate = $this->resource->checkPolicy('save');
	}


	/*
	 * Additional preparation of the resource fields
	 * @return void
	 * */
	function prepareFields() {
		$data = array_keys($this->modx->getFieldMeta('msProductData'));
		foreach ($this->resourceArray as $k => $v) {
			if (is_array($v) && in_array($k, $data)) {
				$tmp = $this->resourceArray[$k];
				$this->resourceArray[$k] = array();
				foreach ($tmp as $v2) {
					if (!empty($v2)) {
						$this->resourceArray[$k][] = array('value' => $v2);
					}
				}
			}
		}

		if (empty($this->resourceArray['vendor'])) {
			$this->resourceArray['vendor'] = '';
		}
	}


	/*
	 * Loads additional scripts for product form from miniShop2 plugins
	 *
	 * @return void
	 * */
	function loadPlugins() {
		foreach ($this->modx->ms2Plugins->plugins as $plugin) {
			if (!empty($plugin['manager']['msProductData'])) {
				$this->addJavascript($plugin['manager']['msProductData']);
			}
		}
	}


	/**
	 * Loads media source properties
	 *
	 * @return array
	 */
	function getSourceProperties() {
		$properties = array();
		/* @var $source modMediaSource */
		if ($source = $this->resource->initializeMediaSource()) {
			$tmp = $source->getProperties();
			$properties = array();
			foreach ($tmp as $v) {
				$properties[$v['name']] = $v['value'];
			}
		}

		return $properties;
	}
}
