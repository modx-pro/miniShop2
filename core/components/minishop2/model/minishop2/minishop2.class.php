<?php
/**
 * The base class for miniShop2.
 *
 * @package minishop2
 */
class miniShop2 {
	/* @var modX $modx */
	public $modx;
	/* @var modContext $context */
	public $context;
	/* @var msCart $cart */
	public $cart;
	public $initialized = array();


	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('minishop2.core_path',$config,$this->modx->getOption('core_path').'components/minishop2/');
		$assetsUrl = $this->modx->getOption('minishop2.assets_url',$config,$this->modx->getOption('assets_url').'components/minishop2/');
		$connectorUrl = $assetsUrl.'connector.php';
		$actionUrl = $assetsUrl.'action.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'

			,'connectorUrl' => $connectorUrl
			,'actionUrl' => $actionUrl

			,'corePath' => $corePath
			,'modelPath' => $corePath.'model/'

			//,'chunksPath' => $corePath.'elements/chunks/'
			,'templatesPath' => $corePath.'elements/templates/'
			//,'snippetsPath' => $corePath.'elements/snippets/'
			//,'processorsPath' => $corePath.'processors/'
		),$config);

		$this->modx->addPackage('minishop2',$this->config['modelPath']);
		$this->modx->lexicon->load('minishop2:default');
	}


	/**
	 * Initializes Tickets into different contexts.
	 *
	 * @access public
	 * @param string $ctx The context to load. Defaults to web.
	 * @param array $scriptProperties Properties for initialization.
	 */
	public function initialize($ctx = 'web', $scriptProperties = array()) {
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		switch ($ctx) {
			case 'mgr': break;
			default:
				$this->config = array_merge($this->config, $scriptProperties);
				require_once dirname(__FILE__).'/mscart.class.php';

				$cart_class = $this->modx->getOption('ms2_cart_handler_class', null, 'msCart');
				if (!in_array($cart_class, get_declared_classes())) {
					$cart_class = 'msCart';
				}

				$this->cart = new $cart_class($this->modx, $this->config);
				if (!($this->cart instanceof msCartInterface)) {
					$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize miniShop2 cart handler class: "'.$cart_class.'" is not instance of msCartInterface');
					return false;
				}

				if (!MODX_API_MODE) {
					$config = $this->makePlaceholders($this->config);
					if ($css = $this->modx->getOption('ms2_frontend_css')) {
						$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
					}
					if ($js = $this->modx->getOption('ms2_frontend_js')) {
						$this->modx->regClientStartupScript(str_replace('					', '', '
						<script type="text/javascript">
						miniShop2Config = {
							cssUrl: "'.$this->config['cssUrl'].'web/"
							,jsUrl: "'.$this->config['jsUrl'].'web/"
							,imagesUrl: "'.$this->config['imagesUrl'].'web/"
							,actionUrl: "'.$this->config['actionUrl'].'"
							,ctx: "'.$this->modx->context->get('key').'"
							,close_all_message: "'.$this->modx->lexicon('ms2_message_close_all').'"
						};
						if(typeof jQuery == "undefined") {
							document.write("<script src=\""+miniShop2Config.jsUrl+"lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
						}
						</script>
					'), true);
						$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
					}
				}

				$this->initialized[$ctx] = true;
			break;
		}

		return true;
	}


	/*
	 *
	 *
	 * */
	public function makePlaceholders(array $array = array()) {
		$result = array(
			'pl' => array()
			,'vl' => array()
		);
		foreach ($array as $k => $v) {
			$result['pl'][$k] = "[[+{$k}]]";
			$result['vl'][$k] = $v;
		}

		return $result;
	}


}