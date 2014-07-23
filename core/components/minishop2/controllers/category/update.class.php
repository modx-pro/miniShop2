<?php
class msCategoryUpdateManagerController extends ResourceUpdateManagerController {
	/* @var msCategory $resource */
	public $resource;

	/**
	 * Returns language topics
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('resource','minishop2:default','minishop2:product','tickets:default');
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

		/* @var msProduct $product*/
		$product = $this->modx->newObject('msProduct');
		$product_fields = array_merge($product->getAllFieldsNames(), array('actions','className','preview_url','cls','vendor_name','category_name'));

		if (!$category_grid_fields = $this->modx->getOption('ms2_category_grid_fields')) {
			$category_grid_fields = 'id,pagetitle,article,price,weight,image';
		}

		$category_grid_fields = array_map('trim', explode(',',$category_grid_fields));
		$grid_fields = array_values(array_intersect($category_grid_fields, $product_fields));

		$showComments = class_exists('TicketsSection') && $this->modx->getOption('ms2_category_show_comments') ? 1 : 0;

		if ($this->resource instanceof msCategory) {
			$neighborhood = $this->resource->getNeighborhood();
		}

		$this->addJavascript($mgrUrl.'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl.'assets/modext/sections/resource/update.js');
		$this->addJavascript($minishopJsUrl.'minishop2.js');
		$this->addJavascript($minishopJsUrl.'misc/ms2.combo.js');
		$this->addJavascript($minishopJsUrl.'misc/ms2.utils.js');
		$this->addJavascript($minishopJsUrl.'category/category.common.js');
		$this->addJavascript($minishopJsUrl.'category/category.grid.js');

		if ($showComments) {$this->loadTickets();}

		$this->addLastJavascript($minishopJsUrl.'category/update.js');

		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		miniShop2.config = {
			assets_url: "'.$minishopAssetsUrl.'"
			,connector_url: "'.$connectorUrl.'"
			,show_comments: '.$showComments.'
			,product_fields: '.json_encode($product_fields).'
			,grid_fields: '.json_encode($grid_fields).'
		}
		MODx.config.publish_document = "'.$this->canPublish.'";
		MODx.onDocFormRender = "'.$this->onDocFormRender.'";
		MODx.ctx = "'.$this->ctx.'";
		Ext.onReady(function() {
			MODx.load({
				xtype: "minishop2-page-category-update"
				,resource: "'.$this->resource->get('id').'"
				,record: '.$this->modx->toJSON($this->resourceArray).'
				,publish_document: "'.$this->canPublish.'"
				,preview_url: "'.$this->previewUrl.'"
				,locked: '.($this->locked ? 1 : 0).'
				,lockedText: "'.$this->lockedText.'"
				,canSave: "'.($this->modx->hasPermission('mscategory_save') ? 1 : 0).'"
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
		$this->modx->invokeEvent('msOnManagerCustomCssJs',array('controller' => &$this, 'page' => 'category_update'));
		$this->loadPlugins();
	}


	/**
	 * Used to set values on the resource record sent to the template for derivative classes
	 *
	 * @return void
	 */
	public function prepareResource() {
		$settings = $this->resource->getProperties('ms2');
		if (is_array($settings) && !empty($settings)) {
			foreach ($settings as $k => $v) {
				$this->resourceArray['setting_'.$k] = $v;
			}
		}
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

}
