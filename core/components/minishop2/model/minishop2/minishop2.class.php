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
	 * Initializes component into different contexts.
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
				if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
					$config = $this->makePlaceholders($this->config);
					if ($css = $this->modx->getOption('ms2_frontend_css')) {
						$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
					}
					if ($js = trim($this->modx->getOption('ms2_frontend_js'))) {
						$this->modx->regClientStartupScript(str_replace('					', '', '
						<script type="text/javascript">
						miniShop2Config = {
							cssUrl: "'.$this->config['cssUrl'].'web/"
							,jsUrl: "'.$this->config['jsUrl'].'web/"
							,imagesUrl: "'.$this->config['imagesUrl'].'web/"
							,actionUrl: "'.$this->config['actionUrl'].'"
							,ctx: "'.$this->modx->context->get('key').'"
							,close_all_message: "'.$this->modx->lexicon('ms2_message_close_all').'"
							,price_format: '.$this->modx->getOption('ms2_price_format', null, '[2, ".", " "]').'
							,price_format_no_zeros: '.$this->modx->getOption('ms2_price_format_no_zeros', null, true).'
							,weight_format: '.$this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]').'
							,weight_format_no_zeros: '.$this->modx->getOption('ms2_weight_format_no_zeros', null, true).'
						};
						</script>
					'), true);
						if (!empty($js) && preg_match('/\.js$/i', $js)) {
							$this->modx->regClientScript(str_replace('							', '', '
							<script type="text/javascript">
							if(typeof jQuery == "undefined") {
								document.write("<script src=\"'.$this->config['jsUrl'].'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
							}
							</script>
							'), true);
							$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
						}
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


	/* Method for transform array to placeholders
	 *
	 * @var array $array With keys and values
	 * @return array $array Two nested arrays With placeholders and values
	 * */
	public function makePlaceholders(array $array = array(), $prefix = '') {
		$result = array(
			'pl' => array()
			,'vl' => array()
		);
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$result = array_merge_recursive($result, $this->makePlaceholders($v, $k.'.'));
			}
			else {
				$result['pl'][$prefix.$k] = '[[+'.$prefix.$k.']]';
				$result['vl'][$prefix.$k] = $v;
			}
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
		$order = $this->order->get();
		if (empty($order['email'])) {return false;}

		if ($this->modx->user->isAuthenticated()) {
			$profile = $this->modx->user->Profile;
			if (!$email = $profile->get('email')) {
				$profile->set('email', $order['email']);
				$profile->save();
			}
			$uid = $this->modx->user->id;
		}
		else {
			/* @var modUser $user */
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
	public function changeOrderStatus($order_id, $status_id) {

		// This method can be overriden by order class
		if (empty($this->order) || !is_object($this->order)) {
			$ctx = !$this->modx->context->key || $this->modx->context->key == 'mgr' ? 'web' : $this->modx->context->key;
			$this->initialize($ctx);
		}
		if (is_object($this->order) && method_exists($this->order, 'changeOrderStatus')) {
			return $this->order->changeOrderStatus($order_id, $status_id);
		}

		$error = '';
		/* @var msOrder $order */
		if (!$order = $this->modx->getObject('msOrder', $order_id)) {$error = 'ms2_err_order_nf';}

		/* @var msOrderStatus $status */
		if (!$status = $this->modx->getObject('msOrderStatus', array('id' => $status_id, 'active' => 1))) {$error = 'ms2_err_status_nf';}
		/* @var msOrderStatus $old_status */
		else if ($old_status = $this->modx->getObject('msOrderStatus', array('id' => $order->get('status'), 'active' => 1))) {
			if ($old_status->get('final')) {$error = 'ms2_err_status_final';}
			else if ($old_status->get('fixed')) {
				if ($status->get('rank') <= $old_status->get('rank')) {
					$error = 'ms2_err_status_fixed';
				}
			}
		}
		if ($order->get('status') == $status_id) {
			$error = 'ms2_err_status_same';
		}

		if (!empty($error)) {
			return $this->modx->lexicon($error);
		}

		$this->modx->invokeEvent('msOnBeforeChangeOrderStatus', array('order' => $order, 'status' => $order->get('status')));
		$order->set('status', $status_id);

		if ($order->save()) {
			$this->modx->invokeEvent('msOnChangeOrderStatus', array('order' => $order, 'status' => $status_id));
			$this->orderLog($order->get('id'), 'status', $status_id);

			/* @var modContext $context */
			if ($context = $this->modx->getObject('modContext', array('key' => $order->get('context')))) {
				$context->prepare(true);
				$lang = $context->getOption('cultureKey');
				$this->modx->setOption('cultureKey',$lang);
				$this->modx->lexicon->load($lang.':minishop2:default',$lang.':minishop2:cart');
			}

			$pls = $order->toArray();
			$pls['cost'] = $this->formatPrice($pls['cost']);
			$pls['cart_cost'] = $this->formatPrice($pls['cart_cost']);
			$pls['delivery_cost'] = $this->formatPrice($pls['delivery_cost']);
			$pls['weight'] = $this->formatWeight($pls['weight']);

			/* @var modChunk $chunk*/
			if ($status->get('email_manager')) {
				$subject = '';
				if ($chunk = $this->modx->newObject('modChunk', array('snippet' => $status->get('subject_manager')))){
					$chunk->setCacheable(false);
					$subject = $this->processTags($chunk->process($pls));
				}
				$body = 'no chunk set';
				if ($chunk = $this->modx->getObject('modChunk', $status->get('body_manager'))) {
					$chunk->setCacheable(false);
					$body = $this->processTags($chunk->process($pls));
				}
				$emails = array_map('trim', explode(',', $this->modx->getOption('ms2_email_manager', null, $this->modx->getOption('emailsender'))));
				if (!empty($subject)) {
					foreach ($emails as $email) {
						if (preg_match('/.+@.+..+/i', $email)) {
							$this->sendEmail($email, $subject, $body);
						}
					}
				}
			}

			if ($status->get('email_user')) {
				/* @var modUserProfile $profile */
				if ($profile = $this->modx->getObject('modUserProfile', array('internalKey' => $order->get('user_id')))) {
					$subject = '';
					if ($chunk = $this->modx->newObject('modChunk', array('snippet' => $status->get('subject_user')))){
						$chunk->setCacheable(false);
						$subject = $this->processTags($chunk->process($pls));
					}
					$body = 'no chunk set';
					if ($chunk = $this->modx->getObject('modChunk', $status->get('body_user'))) {
						$chunk->setCacheable(false);
						$body = $this->processTags($chunk->process($pls));
					}
					$email = $profile->get('email');
					if (!empty($subject) && preg_match('/.+@.+..+/i', $email)) {
						$this->sendEmail($email, $subject, $body);
					}
				}
			}
		}
		return true;
	}


	/* Collects and processes any set of tags
	 *
	 * @param mixed $html Source code for parse
	 * @param integer $maxIterations
	 * @return mixed $html Parsed html
	 * */
	public function processTags($html, $maxIterations = 10) {
		$this->modx->getParser()->processElementTags('', $html, false, false, '[[', ']]', array(), $maxIterations);
		$this->modx->getParser()->processElementTags('', $html, true, true, '[[', ']]', array(), $maxIterations);
		return $html;
	}


	/* Function for sending email
	 *
	 * @param string $email
	 * @param string $subject
	 * @param string $body
	 *
	 * @return void
	 * */
	public function sendEmail($email, $subject, $body = 'no body set') {
		if (!isset($this->modx->mail) || !is_object($this->modx->mail)) {
			$this->modx->getService('mail', 'mail.modPHPMailer');
		}
		$this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
		$this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
		$this->modx->mail->setHTML(true);
		$this->modx->mail->set(modMail::MAIL_SUBJECT, trim($subject));
		$this->modx->mail->set(modMail::MAIL_BODY, $body);
		$this->modx->mail->address('to', trim($email));
		if (!$this->modx->mail->send()) {
			$this->modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$this->modx->mail->mailer->ErrorInfo);
		}
		$this->modx->mail->reset();
	}


	/* Function for logging changes of the order
	 *
	 * @param integer $order_id The id of the order
	 * @param string $action The name of action made with order
	 * @param string $entry The value of action
	 *
	 * @return void
	 * */
	public function orderLog($order_id, $action = 'status', $entry) {
		/* @var msOrder $order */
		if (!$order = $this->modx->getObject('msOrder', $order_id)) {
			return false;
		}

		if (empty($this->modx->request)) {
			$this->modx->getRequest();
		}

		$user_id = ($action == 'status' && $entry == 1) || !$this->modx->user->id ? $order->get('user_id') : $this->modx->user->id;
		$log = $this->modx->newObject('msOrderLog', array(
			'order_id' => $order_id
			,'user_id' => $user_id
			,'timestamp' => time()
			,'action' => $action
			,'entry' => $entry
			,'ip' => $this->modx->request->getClientIp()
		));

		return $log->save();
	}


	/* Function for formatting dates
	 *
	 * @param string $date Source date
	 * @return string $date Formatted date
	 * */
	public function formatDate($date = '') {
		$df = $this->modx->getOption('ms2_date_format', null, '%d.%m.%Y %H:%M');
		return (!empty($date) && $date !== '0000-00-00 00:00:00') ? strftime($df, strtotime($date)) : '&nbsp;';
	}


	/* Function for formatting price
	 *
	 * @param string $price Source price
	 * @return string $price Formatted price
	 * */
	public function formatPrice($price = 0) {
		$pf = json_decode($this->modx->getOption('ms2_price_format', null, '[2, ".", " "]'), true);
		$price = number_format($price, $pf[0], $pf[1], $pf[2]);

		if ($this->modx->getOption('ms2_price_format_no_zeros', null, true)) {
			$price = preg_replace('/(0+)$/', '', $price);
			$price = preg_replace('/[^0-9]$/', '', $price);
		}

		return $price;
	}


	/* Function for formatting weight
	 *
	 * @param string $weight Source weight
	 * @return string $weight Formatted weight
	 * */
	public function formatWeight($weight = 0) {
		$wf = json_decode($this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]'), true);
		$weight = number_format($weight, $wf[0], $wf[1], $wf[2]);

		if ($this->modx->getOption('ms2_weight_format_no_zeros', null, true)) {
			$weight = preg_replace('/(0+)$/', '', $weight);
			$weight = preg_replace('/[^0-9]$/', '', $weight);
		}

		return $weight;
	}


	/*
	 * Gets matching resources by tags. This is adapted function from miniShop1 for backward compatibility
	 *
	 * @param array $tags Tags for search
	 * @param int $only_ids Return only ids of matched resources
	 * @param int $strict 0 - goods must have at least one specified tag
	 *					  1 - goods must have all specified tags, but can have more
	 * 					  2 - goods must have exactly the same tags.
	 * @return array $ids Or array with resources with data and tags
	 * */
	function getTagged($tags = array(), $strict = 0, $only_ids = 0) {
		if (!is_array($tags)) {$tags = explode(',', $tags);}

		$q = $this->modx->newQuery('msProductOption', array('key' => 'tags', 'value:IN' => $tags));
		$q->select('product_id');
		$ids = array();
		if ($q->prepare() && $q->stmt->execute()){
			$ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
		}
		$ids = array_unique($ids);

		// If needed only ids of not strictly mathed items - return.
		if (!$strict && $only_ids) {return $ids;}

		// Filtering ids
		$count = count($tags);

		/* @var PDOStatement $stmt*/
		if ($strict) {
			foreach ($ids as $key => $product_id) {
				if ($strict > 1) {
					$sql = "SELECT COUNT(*) FROM {$this->modx->getTableName('msProductOption')} WHERE `product_id` = {$product_id} AND `key` = 'tags';";
					$stmt = $this->modx->prepare($sql);
					$stmt->execute();
					if ($stmt->fetch(PDO::FETCH_COLUMN) != $count) {
						unset($ids[$key]);
						continue;
					}
				}

				foreach ($tags as $tag) {
					$sql = "SELECT COUNT(`product_id`) FROM {$this->modx->getTableName('msProductOption')} WHERE `product_id` = {$product_id} AND `key` = 'tags' AND `value` = '{$tag}';";
					$stmt = $this->modx->prepare($sql);
					$stmt->execute();
					if (!$stmt->fetch(PDO::FETCH_COLUMN)) {
						unset($ids[$key]);
						break;
					}
				}
			}
		}

		// Return strictly ids, if needed
		$ids = array_unique($ids);
		if ($only_ids) {
			return $ids;
		}

		// Process results
		$data = array();
		foreach ($ids as $id) {
			if (!$only_ids) {
				if ($res = $this->modx->getObject('msProduct', $id)) {
					$data[$id] = $res->toArray();
				}
			}
		}

		return $data;
	}

}