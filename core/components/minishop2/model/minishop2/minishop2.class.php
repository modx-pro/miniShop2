<?php

class miniShop2
{
    public $version = '2.4.5-pl';
    /** @var modX $modx */
    public $modx;
    /** @var pdoFetch $pdoTools */
    public $pdoTools;

    /** @var msCartHandler $cart */
    public $cart;
    /** @var msOrderHandler $order */
    public $order;
    /** @var array $initialized */
    public $initialized = array();

    /** @var array $optionTypes */
    public $optionTypes = array();
    /** @var array $plugins */
    public $plugins = array();


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('minishop2.core_path', $config, MODX_CORE_PATH . 'components/minishop2/');
        $assetsPath = $this->modx->getOption('minishop2.assets_path', $config,
            MODX_ASSETS_PATH . 'components/minishop2/'
        );
        $assetsUrl = $this->modx->getOption('minishop2.assets_url', $config, MODX_ASSETS_URL . 'components/minishop2/');
        $actionUrl = $this->modx->getOption('minishop2.action_url', $config, $assetsUrl . 'action.php');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'assetsPath' => $assetsPath,
            'modelPath' => $corePath . 'model/',
            'customPath' => $corePath . 'custom/',
            'pluginsPath' => $corePath . 'plugins/',

            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'connectorUrl' => $connectorUrl,
            'connector_url' => $connectorUrl,
            'actionUrl' => $actionUrl,

            'defaultThumb' => $this->modx->getOption('ms2_product_thumbnail_default', $config,
                $assetsUrl . 'img/mgr/ms2_thumb.png'
            ),
            'ctx' => 'web',
            'json_response' => false,
        ), $config);

        $this->modx->addPackage('minishop2', $this->config['modelPath']);
        $this->modx->lexicon->load('minishop2:default');

        if ($this->pdoTools = $this->modx->getService('pdoFetch')) {
            $this->pdoTools->setConfig($this->config);
        }
    }


    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array $scriptProperties Properties for initialization.
     *
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;

        if ($ctx != 'mgr' && (!defined('MODX_API_MODE') || !MODX_API_MODE)) {
            $config = $this->pdoTools->makePlaceholders($this->config);

            // Register CSS
            $css = trim($this->modx->getOption('ms2_frontend_css'));
            if (!empty($css) && preg_match('/\.css/i', $css)) {
                if (preg_match('/\.css$/i', $css)) {
                    $css .= '?v=' . substr(md5($this->version), 0, 10);
                }
                $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
            }

            // Register JS
            $js = trim($this->modx->getOption('ms2_frontend_js'));
            if (!empty($js) && preg_match('/\.js/i', $js)) {
                if (preg_match('/\.js$/i', $js)) {
                    $js .= '?v=' . substr(md5($this->version), 0, 10);
                }
                $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));

                $data = json_encode(array(
                    'cssUrl' => $this->config['cssUrl'] . 'web/',
                    'jsUrl' => $this->config['jsUrl'] . 'web/',
                    'actionUrl' => $this->config['actionUrl'],
                    'ctx' => $ctx,
                    'close_all_message' => $this->modx->lexicon('ms2_message_close_all'),
                    'price_format' => json_decode(
                        $this->modx->getOption('ms2_price_format', null, '[2, ".", " "]'), true
                    ),
                    'price_format_no_zeros' => (bool)$this->modx->getOption('ms2_price_format_no_zeros', null, true),
                    'weight_format' => json_decode(
                        $this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]'), true
                    ),
                    'weight_format_no_zeros' => (bool)$this->modx->getOption('ms2_weight_format_no_zeros', null, true),
                ), true);
                $this->modx->regClientStartupScript(
                    '<script type="text/javascript">miniShop2Config = ' . $data . ';</script>', true
                );
            }
        }
        $load = $this->loadServices($ctx);
        $this->initialized[$ctx] = $load;

        return $load;
    }


    /**
     * Handle frontend requests with actions
     *
     * @param $action
     * @param array $data
     *
     * @return array|bool|string
     */
    public function handleRequest($action, $data = array())
    {
        $ctx = !empty($data['ctx'])
            ? (string)$data['ctx']
            : 'web';
        if ($ctx != 'web') {
            $this->modx->switchContext($ctx);
        }
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        $this->initialize($ctx, array('json_response' => $isAjax));

        switch ($action) {
            case 'cart/add':
                $response = $this->cart->add(@$data['id'], @$data['count'], @$data['options']);
                break;
            case 'cart/change':
                $response = $this->cart->change(@$data['key'], @$data['count']);
                break;
            case 'cart/remove':
                $response = $this->cart->remove(@$data['key']);
                break;
            case 'cart/clean':
                $response = $this->cart->clean();
                break;
            case 'cart/get':
                $response = $this->cart->get();
                break;
            case 'order/add':
                $response = $this->order->add(@$data['key'], @$data['value']);
                break;
            case 'order/submit':
                $response = $this->order->submit($data);
                break;
            case 'order/getcost':
                $response = $this->order->getCost();
                break;
            case 'order/getrequired':
                $response = $this->order->getDeliveryRequiresFields(@$data['id']);
                break;
            case 'order/clean':
                $response = $this->order->clean();
                break;
            case 'order/get':
                $response = $this->order->get();
                break;
            default:
                $message = ($data['ms2_action'] != $action)
                    ? 'ms2_err_register_globals'
                    : 'ms2_err_unknown';
                $response = $this->error($message);
        }

        return $response;
    }


    /**
     * @param string $ctx
     *
     * @return bool
     */
    public function loadServices($ctx = 'web')
    {
        // Default classes
        if (!class_exists('msCartHandler')) {
            require_once dirname(__FILE__) . '/mscarthandler.class.php';
        }
        if (!class_exists('msOrderHandler')) {
            require_once dirname(__FILE__) . '/msorderhandler.class.php';
        }

        // Custom cart class
        $cart_class = $this->modx->getOption('ms2_cart_handler_class', null, 'msCartHandler');
        if ($cart_class != 'msCartHandler') {
            $this->loadCustomClasses('cart');
        }
        if (!class_exists($cart_class)) {
            $cart_class = 'msCartHandler';
        }

        $this->cart = new $cart_class($this, $this->config);
        if (!($this->cart instanceof msCartInterface) || $this->cart->initialize($ctx) !== true) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                'Could not initialize miniShop2 cart handler class: "' . $cart_class . '"');

            return false;
        }

        // Custom order class
        $order_class = $this->modx->getOption('ms2_order_handler_class', null, 'msOrderHandler');
        if ($order_class != 'msOrderHandler') {
            $this->loadCustomClasses('order');
        }
        if (!class_exists($order_class)) {
            $order_class = 'msOrderHandler';
        }

        $this->order = new $order_class($this, $this->config);
        if (!($this->order instanceof msOrderInterface) || $this->order->initialize($ctx) !== true) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                'Could not initialize miniShop2 order handler class: "' . $order_class . '"');

            return false;
        }

        return true;
    }


    /**
     * Register service into miniShop2
     *
     * @param $type
     * @param $name
     * @param $controller
     */
    public function addService($type, $name, $controller)
    {
        $services = $this->_getSetting('ms2_services');
        $type = strtolower($type);
        $name = strtolower($name);
        if (!isset($services[$type])) {
            $services[$type] = array($name => $controller);
        } else {
            $services[$type][$name] = $controller;
        }

        $this->_updateSetting('ms2_services', $services);
    }


    /**
     * Remove service from miniShop2
     *
     * @param $type
     * @param $name
     */
    public function removeService($type, $name)
    {
        $services = $this->_getSetting('ms2_services');
        $type = strtolower($type);
        $name = strtolower($name);
        unset($services[$type][$name]);
        $this->_updateSetting('ms2_services', $services);
    }


    /**
     * Get all registered services
     *
     * @param string $type
     *
     * @return array|mixed
     */
    public function getServices($type = '')
    {
        $services = $this->_getSetting('ms2_services');

        if (is_array($services)) {
            return !empty($type) && isset($services[$type])
                ? $services[$type]
                : $services;
        }

        return array();
    }


    /**
     * Register plugin into miniShop2
     *
     * @param $name
     * @param $controller
     */
    public function addPlugin($name, $controller)
    {
        $plugins = $this->_getSetting('ms2_plugins');
        $plugins[strtolower($name)] = $controller;

        $this->_updateSetting('ms2_plugins', $plugins);
    }


    /**
     * Remove plugin from miniShop2
     *
     * @param $name
     */
    public function removePlugin($name)
    {
        $plugins = $this->_getSetting('ms2_plugins');
        unset($plugins[strtolower($name)]);
        $this->_updateSetting('ms2_plugins', $plugins);
    }


    /**
     * Get all registered plugins
     *
     * @return array|mixed
     */
    public function getPlugins()
    {
        return $this->_getSetting('ms2_plugins');
    }


    /**
     * Load custom classes from specified directory
     *
     * @var string $type Type of class
     *
     * @return void
     */
    public function loadCustomClasses($type)
    {
        // Original classes
        $files = scandir($this->config['customPath'] . $type);
        foreach ($files as $file) {
            if (preg_match('/.*?\.class\.php$/i', $file)) {
                /** @noinspection PhpIncludeInspection */
                include_once($this->config['customPath'] . $type . '/' . $file);
            }
        }

        // 3rd party classes
        $type = strtolower($type);
        $placeholders = array(
            'base_path' => MODX_BASE_PATH,
            'core_path' => MODX_CORE_PATH,
            'assets_path' => MODX_ASSETS_PATH,
        );
        $pl1 = $this->pdoTools->makePlaceholders($placeholders, '', '[[+', ']]', false);
        $pl2 = $this->pdoTools->makePlaceholders($placeholders, '', '[[++', ']]', false);
        $pl3 = $this->pdoTools->makePlaceholders($placeholders, '', '{', '}', false);
        $services = $this->getServices();
        if (!empty($services[$type]) && is_array($services[$type])) {
            foreach ($services[$type] as $controller) {
                if (is_string($controller)) {
                    $file = $controller;
                } elseif (is_array($controller) && !empty($controller['controller'])) {
                    $file = $controller['controller'];
                } else {
                    continue;
                }

                $file = str_replace($pl1['pl'], $pl1['vl'], $file);
                $file = str_replace($pl2['pl'], $pl2['vl'], $file);
                $file = str_replace($pl3['pl'], $pl3['vl'], $file);
                if (strpos($file, MODX_BASE_PATH) === false) {
                    $file = MODX_BASE_PATH . ltrim($file, '/');
                }
                if (file_exists($file)) {
                    /** @noinspection PhpIncludeInspection */
                    include_once($file);
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "[miniShop2] Could not load custom class at \"$file\"");
                }
            }
        }

    }


    /**
     * Loads available plugins with parameters
     *
     * @return array
     */
    public function loadPlugins()
    {
        // Original plugins
        $plugins = scandir($this->config['pluginsPath']);
        foreach ($plugins as $plugin) {
            if ($plugin == '.' || $plugin == '..') {
                continue;
            }
            $dir = $this->config['pluginsPath'] . $plugin;

            if (is_dir($dir) && file_exists($dir . '/index.php')) {
                /** @noinspection PhpIncludeInspection */
                $include = include_once($dir . '/index.php');
                if (is_array($include)) {
                    $this->plugins[$plugin] = $include;
                }
            }
        }

        // 3rd party plugins
        $placeholders = array(
            'base_path' => MODX_BASE_PATH,
            'core_path' => MODX_CORE_PATH,
            'assets_path' => MODX_ASSETS_PATH,
        );
        $pl1 = $this->pdoTools->makePlaceholders($placeholders, '', '[[++', ']]', false);
        $pl2 = $this->pdoTools->makePlaceholders($placeholders, '', '{', '}', false);
        $plugins = $this->getPlugins();
        if (!empty($plugins) && is_array($plugins)) {
            foreach ($plugins as $plugin => $controller) {
                if (is_string($controller)) {
                    $file = $controller;
                } elseif (is_array($controller) && !empty($controller['controller'])) {
                    $file = $controller['controller'];
                } else {
                    continue;
                }

                $file = str_replace($pl2['pl'], $pl2['vl'], str_replace($pl1['pl'], $pl1['vl'], $file));
                if (strpos($file, MODX_BASE_PATH) === false) {
                    $file = MODX_BASE_PATH . ltrim($file, '/');
                }
                if (!preg_match('#index\.php$#', $file)) {
                    $file = rtrim($file, '/') . '/index.php';
                }
                if (file_exists($file)) {
                    /** @noinspection PhpIncludeInspection */
                    $include = include($file);
                    if (is_array($include)) {
                        $this->plugins[$plugin] = $include;
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "[miniShop2] Could not load plugin at \"$file\"");
                }
            }
        }

        return $this->plugins;
    }


    /**
     * @return array
     */
    public function loadOptionTypeList()
    {
        $typeDir = $this->config['corePath'] . 'processors/mgr/settings/option/types';
        $files = scandir($typeDir);
        $list = array();

        foreach ($files as $file) {
            if (preg_match('/.*?\.class\.php$/i', $file)) {
                $list[] = str_replace('.class.php', '', $file);
            }
        }

        return $list;
    }


    /**
     * @param string $type
     *
     * @return mixed
     */
    public function loadOptionType($type)
    {
        $this->modx->loadClass('msOption', $this->config['modelPath'] . 'minishop2/');
        $typePath = $this->config['corePath'] . 'processors/mgr/settings/option/types/' . $type . '.class.php';

        if (array_key_exists($typePath, $this->optionTypes)) {
            $className = $this->optionTypes[$typePath];
        } else {
            /** @noinspection PhpIncludeInspection */
            $className = include_once $typePath;
            // handle already included classes
            if ($className == 1) {
                $o = array();
                $s = explode(' ', str_replace(array('_', '-'), ' ', $type));
                foreach ($s as $k) {
                    $o[] = ucfirst($k);
                }
                $className = 'ms' . implode('', $o) . 'Type';
            }
            $this->optionTypes[$typePath] = $className;
        }

        return $className;
    }


    /**
     * @param msOption $option
     *
     * @return null|msOptionType
     */
    public function getOptionType($option)
    {
        $className = $this->loadOptionType($option->get('type'));

        if (class_exists($className)) {
            return new $className($option);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                'Could not initialize miniShop2 option type class: "' . $className . '"');

            return null;
        }
    }


    /**
     * Loads additional metadata for miniShop2 objects
     */
    public function loadMap()
    {
        $plugins = $this->loadPlugins();
        foreach ($plugins as $plugin) {
            // For legacy plugins
            if (isset($plugin['xpdo_meta_map']) && is_array($plugin['xpdo_meta_map'])) {
                $plugin['map'] = $plugin['xpdo_meta_map'];
            }
            if (isset($plugin['map']) && is_array($plugin['map'])) {
                foreach ($plugin['map'] as $class => $map) {
                    if (!isset($this->modx->map[$class])) {
                        $this->modx->loadClass($class, $this->config['modelPath'] . 'minishop2/');
                    }
                    if (isset($this->modx->map[$class])) {
                        foreach ($map as $key => $values) {
                            $this->modx->map[$class][$key] = array_merge($this->modx->map[$class][$key], $values);
                        }
                    }
                }
            }
        }
    }


    /**
     * Returns id for current customer. If customer is not exists, registers him and returns id.
     *
     * @return integer $id
     */
    public function getCustomerId()
    {
        $order = $this->order->get();
        if (!$email = $order['email']) {
            return false;
        }

        if ($this->modx->user->isAuthenticated()) {
            $profile = $this->modx->user->Profile;
            if (!$profile->get('email')) {
                $profile->set('email', $email);
                $profile->save();
            }
            $uid = $this->modx->user->id;
        } else {
            if ($user = $this->modx->getObject('modUser', array('username' => $email))) {
                $uid = $user->get('id');
            } elseif ($profile = $this->modx->getObject('modUserProfile', array('email' => $email))) {
                $uid = $profile->get('internalKey');
            } else {
                $user = $this->modx->newObject('modUser', array('username' => $email, 'password' => md5(rand())));
                $profile = $this->modx->newObject('modUserProfile', array(
                    'email' => $email,
                    'fullname' => $order['receiver'],
                ));
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


    /**
     * Switch order status
     *
     * @param integer $order_id The id of msOrder
     * @param integer $status_id The id of msOrderStatus
     *
     * @return boolean|string
     */
    public function changeOrderStatus($order_id, $status_id)
    {
        if (empty($this->order) || !is_object($this->order)) {
            $ctx = !$this->modx->context->key || $this->modx->context->key == 'mgr'
                ? 'web'
                : $this->modx->context->key;
            $this->initialize($ctx);
        }
        // This method could be overwritten from custom order handler
        if (is_object($this->order) && method_exists($this->order, 'changeOrderStatus')) {
            return $this->order->changeOrderStatus($order_id, $status_id);
        }

        $error = '';
        /** @var msOrder $order */
        if (!$order = $this->modx->getObject('msOrder', $order_id)) {
            $error = 'ms2_err_order_nf';
        }

        /** @var msOrderStatus $status */
        if (!$status = $this->modx->getObject('msOrderStatus', array('id' => $status_id, 'active' => 1))) {
            $error = 'ms2_err_status_nf';
        } /** @var msOrderStatus $old_status */
        else {
            if ($old_status = $this->modx->getObject('msOrderStatus',
                array('id' => $order->get('status'), 'active' => 1))
            ) {
                if ($old_status->get('final')) {
                    $error = 'ms2_err_status_final';
                } else {
                    if ($old_status->get('fixed')) {
                        if ($status->get('rank') <= $old_status->get('rank')) {
                            $error = 'ms2_err_status_fixed';
                        }
                    }
                }
            }
        }
        if ($order->get('status') == $status_id) {
            $error = 'ms2_err_status_same';
        }

        if (!empty($error)) {
            return $this->modx->lexicon($error);
        }

        $response = $this->invokeEvent('msOnBeforeChangeOrderStatus', array(
            'order' => $order,
            'status' => $order->get('status'),
        ));
        if (!$response['success']) {
            return $response['message'];
        }

        $order->set('status', $status_id);

        if ($order->save()) {
            $this->orderLog($order->get('id'), 'status', $status_id);
            $response = $this->invokeEvent('msOnChangeOrderStatus', array(
                'order' => $order,
                'status' => $status_id,
            ));
            if (!$response['success']) {
                return $response['message'];
            }

            /** @var modContext $context */
            if ($context = $this->modx->getObject('modContext', array('key' => $order->get('context')))) {
                $this->modx->getCacheManager()->generateContext($context->get('key'));
                $lang = $context->getOption('cultureKey');
                $this->modx->setOption('cultureKey', $lang);
                $this->modx->lexicon->load($lang . ':minishop2:default', $lang . ':minishop2:cart');
            }

            $pls = $order->toArray();
            $pls['cost'] = $this->formatPrice($pls['cost']);
            $pls['cart_cost'] = $this->formatPrice($pls['cart_cost']);
            $pls['delivery_cost'] = $this->formatPrice($pls['delivery_cost']);
            $pls['weight'] = $this->formatWeight($pls['weight']);
            $pls['payment_link'] = '';
            if ($payment = $order->getOne('Payment')) {
                if ($class = $payment->get('class')) {
                    $this->loadCustomClasses('payment');
                    if (class_exists($class)) {
                        /** @var msPaymentHandler|PayPal $handler */
                        $handler = new $class($order);
                        if (method_exists($handler, 'getPaymentLink')) {
                            $link = $handler->getPaymentLink($order);
                            $pls['payment_link'] = $link;
                        }
                    }
                }
            }

            if ($status->get('email_manager')) {
                $subject = $this->pdoTools->getChunk('@INLINE ' . $status->get('subject_manager'), $pls);
                $tpl = '';
                if ($chunk = $this->modx->getObject('modChunk', $status->get('body_manager'))) {
                    $tpl = $chunk->get('name');
                }
                $body = $this->modx->runSnippet('msGetOrder', array_merge($pls, array('tpl' => $tpl)));
                $emails = array_map('trim', explode(',',
                        $this->modx->getOption('ms2_email_manager', null, $this->modx->getOption('emailsender')))
                );
                if (!empty($subject)) {
                    foreach ($emails as $email) {
                        if (preg_match('#.*?@.*#', $email)) {
                            $this->sendEmail($email, $subject, $body);
                        }
                    }
                }
            }

            if ($status->get('email_user')) {
                if ($profile = $this->modx->getObject('modUserProfile', array('internalKey' => $pls['user_id']))) {
                    $subject = $this->pdoTools->getChunk('@INLINE ' . $status->get('subject_user'), $pls);
                    $tpl = '';
                    if ($chunk = $this->modx->getObject('modChunk', $status->get('body_user'))) {
                        $tpl = $chunk->get('name');
                    }
                    $body = $this->modx->runSnippet('msGetOrder', array_merge($pls, array('tpl' => $tpl)));
                    $email = $profile->get('email');
                    if (!empty($subject) && preg_match('#.*?@.*#', $email)) {
                        $this->sendEmail($email, $subject, $body);
                    }
                }
            }
        }

        return true;
    }


    /**
     * Function for sending email
     *
     * @param string $email
     * @param string $subject
     * @param string $body
     *
     * @return void
     */
    public function sendEmail($email, $subject, $body = '')
    {
        $this->modx->getParser()->processElementTags('', $body, true, false, '[[', ']]', array(), 10);
        $this->modx->getParser()->processElementTags('', $body, true, true, '[[', ']]', array(), 10);

        /** @var modPHPMailer $mail */
        $mail = $this->modx->getService('mail', 'mail.modPHPMailer');
        $mail->setHTML(true);

        $mail->address('to', trim($email));
        $mail->set(modMail::MAIL_SUBJECT, trim($subject));
        $mail->set(modMail::MAIL_BODY, $body);
        $mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        if (!$mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo
            );
        }
        $mail->reset();
    }


    /**
     * Function for logging changes of the order
     *
     * @param integer $order_id The id of the order
     * @param string $action The name of action made with order
     * @param string $entry The value of action
     *
     * @return boolean
     */
    public function orderLog($order_id, $action = 'status', $entry)
    {
        /** @var msOrder $order */
        if (!$order = $this->modx->getObject('msOrder', $order_id)) {
            return false;
        }

        if (empty($this->modx->request)) {
            $this->modx->getRequest();
        }

        $user_id = ($action == 'status' && $entry == 1) || !$this->modx->user->id
            ? $order->get('user_id')
            : $this->modx->user->id;
        $log = $this->modx->newObject('msOrderLog', array(
            'order_id' => $order_id,
            'user_id' => $user_id,
            'timestamp' => time(),
            'action' => $action,
            'entry' => $entry,
            'ip' => $this->modx->request->getClientIp(),
        ));

        return $log->save();
    }


    /**
     * Function for formatting dates
     *
     * @param string $date Source date
     *
     * @return string $date Formatted date
     */
    public function formatDate($date = '')
    {
        $df = $this->modx->getOption('ms2_date_format', null, '%d.%m.%Y %H:%M');

        return (!empty($date) && $date !== '0000-00-00 00:00:00')
            ? strftime($df, strtotime($date))
            : '&nbsp;';
    }


    /**
     * Function for price format
     *
     * @param $price
     *
     * @return int|mixed|string
     */
    public function formatPrice($price = 0)
    {
        if (!$pf = json_decode($this->modx->getOption('ms2_price_format', null, '[2, ".", " "]'), true)) {
            $pf = array(2, '.', ' ');
        }
        $price = number_format($price, $pf[0], $pf[1], $pf[2]);

        if ($this->modx->getOption('ms2_price_format_no_zeros', null, true)) {
            $tmp = explode($pf[1], $price);
            $tmp[1] = rtrim(rtrim(@$tmp[1], '0'), '.');
            $price = !empty($tmp[1])
                ? $tmp[0] . $pf[1] . $tmp[1]
                : $tmp[0];
        }

        return $price;
    }


    /**
     * Function for weight format
     *
     * @param $weight
     *
     * @return int|mixed|string
     */
    public function formatWeight($weight = 0)
    {
        if (!$wf = json_decode($this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]'), true)) {
            $wf = array(3, '.', ' ');
        }
        $weight = number_format($weight, $wf[0], $wf[1], $wf[2]);

        if ($this->modx->getOption('ms2_weight_format_no_zeros', null, true)) {
            $tmp = explode($wf[1], $weight);
            $tmp[1] = rtrim(rtrim(@$tmp[1], '0'), '.');
            $weight = !empty($tmp[1])
                ? $tmp[0] . $wf[1] . $tmp[1]
                : $tmp[0];
        }

        return $weight;
    }


    /**
     * Gets matching resources by tags. This is adapted function from miniShop1 for backward compatibility
     * @deprecated
     *
     * @param array $tags Tags for search
     * @param int $only_ids Return only ids of matched resources
     * @param int $strict 0 - goods must have at least one specified tag
     *                      1 - goods must have all specified tags, but can have more
     *                      2 - goods must have exactly the same tags.
     *
     * @return array $ids Or array with resources with data and tags
     */
    function getTagged($tags = array(), $strict = 0, $only_ids = 0)
    {
        if (!is_array($tags)) {
            $tags = explode(',', $tags);
        }

        $q = $this->modx->newQuery('msProductOption', array('key' => 'tags', 'value:IN' => $tags));
        $q->select('product_id');
        $ids = array();
        if ($q->prepare() && $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        $ids = array_unique($ids);

        // If needed only ids of not strictly matched items - return.
        if (!$strict && $only_ids) {
            return $ids;
        }

        // Filtering ids
        $count = count($tags);

        /** @var PDOStatement $stmt */
        if ($strict) {
            foreach ($ids as $key => $product_id) {
                if ($strict > 1) {
                    $found = $this->modx->getCount('msProductOption', array(
                        'product_id' => $product_id,
                        'key' => $tags,
                    ));
                    if ($found != $count) {
                        unset($ids[$key]);
                        continue;
                    }
                }

                foreach ($tags as $tag) {
                    $found = $this->modx->getCount('msProductOption', array(
                        'product_id' => $product_id,
                        'key' => $tags,
                        'value' => $tag,
                    ));
                    if (!$found) {
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


    /**
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param $eventName
     * @param array $params
     * @param $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = array(), $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return array(
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        );
    }


    /**
     * This method returns an error of the order
     *
     * @param string $message A lexicon key for error message
     * @param array $data .Additional data, for example cart status
     * @param array $placeholders Array with placeholders for lexicon entry
     *
     * @return array|string $response
     */
    public function error($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        );

        return $this->config['json_response']
            ? json_encode($response)
            : $response;
    }


    /**
     * This method returns an success of the order
     *
     * @param string $message A lexicon key for success message
     * @param array $data .Additional data, for example cart status
     * @param array $placeholders Array with placeholders for lexicon entry
     *
     * @return array|string $response
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        );

        return $this->config['json_response']
            ? json_encode($response)
            : $response;
    }


    /**
     * Shorthand for the call of processor
     *
     * @access public
     *
     * @param string $action Path to processor
     * @param array $data Data to be transmitted to the processor
     *
     * @return mixed The result of the processor
     */
    public function runProcessor($action = '', $data = array())
    {
        if (empty($action)) {
            return false;
        }
        $this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath'])
            ? $this->config['processorsPath']
            : MODX_CORE_PATH . 'components/minishop2/processors/';

        return $this->modx->runProcessor($action, $data, array(
            'processors_path' => $processorsPath,
        ));
    }


    /**
     * Pathinfo function for cyrillic files
     *
     * @param $path
     * @param string $part
     *
     * @return array
     */
    public function pathinfo($path, $part = '')
    {
        // Russian files
        if (preg_match('#[а-яё]#im', $path)) {
            $path = strtr($path, array('\\' => '/'));

            preg_match("#[^/]+$#", $path, $file);
            preg_match("#([^/]+)[.$]+(.*)#", $path, $file_ext);
            preg_match("#(.*)[/$]+#", $path, $dirname);

            $info = array(
                'dirname' => $dirname[1] ?: '.',
                'basename' => $file[0],
                'extension' => (isset($file_ext[2]))
                    ? $file_ext[2]
                    : '',
                'filename' => (isset($file_ext[1]))
                    ? $file_ext[1]
                    : $file[0],
            );
        } else {
            $info = pathinfo($path);
        }

        return !empty($part) && isset($info[$part])
            ? $info[$part]
            : $info;
    }


    /**
     * General method to get JSON settings
     *
     * @param $key
     *
     * @return array|mixed
     */
    protected function _getSetting($key)
    {
        if (!$setting = $this->modx->getObject('modSystemSetting', array('key' => $key))) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('key', $key);
            $setting->set('value', '[]');
            $setting->save();
        }

        $value = json_decode($setting->get('value'), true);
        if (!is_array($value)) {
            $value = array();
            $setting->set('value', $value);
            $setting->save();
        }

        return $value;
    }


    /**
     * General method to update JSON settings
     *
     * @param $key
     * @param $value
     */
    protected function _updateSetting($key, $value)
    {
        if (!$setting = $this->modx->getObject('modSystemSetting', array('key' => $key))) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('key', $key);
        }
        $setting->set('value', json_encode($value));
        $setting->save();
    }
}
