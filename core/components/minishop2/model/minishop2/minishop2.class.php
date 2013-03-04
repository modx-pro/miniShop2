<?php
/**
 * The base class for miniShop2.
 *
 * @package minishop2
 */
class miniShop2 {
	/* @var modX $modx */
	public $modx;
	/* @var msCartHandler $cart */
	public $cart;
	/* @var msOrderHandler $order */
	public $order;

	public $initialized = array();


	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('minishop2.core_path', $config, $this->modx->getOption('core_path').'components/minishop2/');
		$assetsUrl = $this->modx->getOption('minishop2.assets_url', $config, $this->modx->getOption('assets_url').'components/minishop2/');
		$actionUrl = $this->modx->getOption('minishop2.action_url', $config, $assetsUrl.'action.php');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'
			,'customPath' => $corePath.'custom/'

			,'connectorUrl' => $connectorUrl
			,'actionUrl' => $actionUrl

			,'corePath' => $corePath
			,'modelPath' => $corePath.'model/'
			,'ctx' => 'web'

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
		$this->config = array_merge($this->config, $scriptProperties);
		$this->config['ctx'] = $ctx;
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		switch ($ctx) {
			case 'mgr': break;
			default:
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

				require_once dirname(__FILE__) . '/mscarthandler.class.php';
				$cart_class = $this->modx->getOption('ms2_cart_handler_class', null, 'msCartHandler');
				if ($cart_class != 'msCartHandler') {$this->loadCustomClasses('cart');}
				if (!class_exists($cart_class)) {$cart_class = 'msCartHandler';}

				$this->cart = new $cart_class($this, $this->config);
				if (!($this->cart instanceof msCartInterface) || $this->cart->initialize($ctx) !== true) {
					$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize miniShop2 cart handler class: "'.$cart_class.'"');
					return false;
				}

				require_once dirname(__FILE__).'/msorderhandler.class.php';
				$order_class = $this->modx->getOption('ms2_order_handler_class', null, 'msOrderHandler');
				if ($order_class != 'msOrderHandler') {$this->loadCustomClasses('order');}
				if (!class_exists($order_class)) {$order_class = 'msOrderHandler';}

				$this->order = new $order_class($this, $this->config);
				if (!($this->order instanceof msOrderInterface) || $this->order->initialize($ctx) !== true) {
					$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize miniShop2 order handler class: "'.$order_class.'"');
					return false;
				}

				$this->initialized[$ctx] = true;
			break;
		}
		return true;
	}


	/* Method for transform array to placeholdres
	 *
	 * @var array $array With keys and values
	 * @return array $array Two nested arrays With placeholders and values
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


	/* Method loads custom classes from specified directory
	 *
	 * @var string $dir Directory for load classes
	 * @return void
	 * */
	public function loadCustomClasses($dir) {
		$files = scandir($this->config['customPath'] . $dir);
		foreach ($files as $file) {
			if (preg_match('/.*?\.class\.php$/i', $file)) {
				include_once($this->config['customPath'] . $dir . '/' . $file);
			}
		}
	}


	/* Returns id of current customer. If no exists - register him and returns id.
	 *
	 * @return integer $id
	 * */
	public function getCustomerId() {
		if ($this->modx->user->isAuthenticated()) {
			$profile = $this->modx->user->Profile;
			if (!$email = $profile->get('email')) {
				$profile->set('email', $this->order['email']);
				$profile->save();
			}
			$uid = $this->modx->user->id;
		}
		else {
			/* @var modUser $user */
			$order = $this->order->get();
			$email = $order['email'];
			if ($user = $this->modx->getObject('modUser', array('username' => $email))) {
				$uid = $user->get('id');
			}
			else {
				$user = $this->modx->newObject('modUser', array('username' => $email, 'password' => md5(rand())));
				$profile = $this->modx->newObject('modUserProfile', array('email' => $email, 'fullname' => $order['receiver']));
				$user->addOne($profile);
				$user->save();

				if ($groups = $this->modx->getOption('ms2_order_user_groups', null, false)) {
					$groups = array_map('trim', explode(',', $groups));
					foreach ($groups as $group) {
						$user->joinGroup($group);
					}
				}
				$uid = $user->get('id');
			}
		}

		return $uid;
	}


	/* Switch order status
	 *
	 * @param integer $order_id The id of msOrder
	 * @param integer $status_id The id of msOrderStatus
	 * @return boolean
	 * */
	public function switchOrderStatus($order_id, $status_id) {
		if (!$order = $this->modx->getObject('msOrder', $order_id)) {
			return false;
		}
		if (!$status = $this->modx->getObject('msOrderStatus', $status_id)) {
			return false;
		}
		$order->set('status', $status->get('id'));
		$order->save();
		return true;
	}

}