<?php

class miniShop2
{
    public $version = '4.1.5-pl';
    /** @var modX $modx */
    public $modx;
    /** @var pdoFetch $pdoTools */
    public $pdoTools;

    /** @var msCartHandler $cart */
    public $cart;
    /** @var msOrderHandler $order */
    public $order;
    /** @var array $initialized */
    public $initialized = [];

    /** @var array $optionTypes */
    public $optionTypes = [];
    /** @var array $plugins */
    public $plugins = [];
    /**
     * @var array
     */
    public $config = [];

    /**
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX $modx, array $config = [])
    {
        $this->modx = $modx;

        $corePath = $this->modx->getOption('minishop2.core_path', $config, MODX_CORE_PATH . 'components/minishop2/');
        $assetsPath = $this->modx->getOption(
            'minishop2.assets_path',
            $config,
            MODX_ASSETS_PATH . 'components/minishop2/'
        );
        $assetsUrl = $this->modx->getOption('minishop2.assets_url', $config, MODX_ASSETS_URL . 'components/minishop2/');
        $actionUrl = $this->modx->getOption('minishop2.action_url', $config, $assetsUrl . 'action.php');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge([
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

            'defaultThumb' => trim($this->modx->getOption('ms2_product_thumbnail_default', null, true)),
            'ctx' => 'web',
            'json_response' => false,
        ], $config);

        $this->modx->addPackage('minishop2', $this->config['modelPath']);

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
    public function initialize($ctx = 'web', $scriptProperties = [])
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;
        $this->modx->lexicon->load('minishop2:default');

        $load = $this->loadServices($ctx);
        $this->initialized[$ctx] = $load;

        return $load;
    }

    public function registerFrontend($ctx = 'web')
    {
        if ($ctx != 'mgr' && (!defined('MODX_API_MODE') || !MODX_API_MODE)) {
            $this->modx->lexicon->load('minishop2:default');

            $config = $this->pdoTools->makePlaceholders($this->config);

            // Register CSS
            $css = trim($this->modx->getOption('ms2_frontend_css'));
            if (!empty($css) && preg_match('/\.css/i', $css)) {
                if (preg_match('/\.css$/i', $css)) {
                    $css .= '?v=' . substr(md5($this->version), 0, 10);
                }
                $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
            }

            if ((bool)$this->modx->getOption('ms2_toggle_js_type')) {
                // Register Vanila JS
                $js = trim($this->modx->getOption('ms2_vanila_js'));
                if (!empty($js) && preg_match('/\.js/i', $js)) {
                    if (preg_match('/\.js$/i', $js)) {
                        $js .= '?v=' . substr(md5($this->version), 0, 10);
                    }
                    $js = str_replace($config['pl'], $config['vl'], $js);
                    $this->modx->regClientStartupScript('<script type="module" src="' . $js . '"></script>', 1);
                }

                $js_setting = [
                    'cartClassPath' => str_replace(
                        '[[+jsUrl]]',
                        $this->config['jsUrl'],
                        $this->modx->getOption('ms2_cart_js_class_path', null, '')
                    ),
                    'cartClassName' => $this->modx->getOption('ms2_cart_js_class_name', null, ''),
                    'orderClassPath' => str_replace(
                        '[[+jsUrl]]',
                        $this->config['jsUrl'],
                        $this->modx->getOption('ms2_order_js_class_path', null, '')
                    ),
                    'orderClassName' => $this->modx->getOption('ms2_order_js_class_name', null, ''),
                    'notifyClassPath' => str_replace(
                        '[[+jsUrl]]',
                        $this->config['jsUrl'],
                        $this->modx->getOption('ms2_notify_js_class_path', null, '')
                    ),
                    'notifyClassName' => $this->modx->getOption('ms2_notify_js_class_name', null, ''),
                    'notifySettingsPath' => str_replace(
                        '[[+jsUrl]]',
                        $this->config['jsUrl'],
                        $this->modx->getOption('ms2_frontend_notify_js_settings', null, '')
                    ),

                    'cssUrl' => $this->config['cssUrl'] . 'web/',
                    'jsUrl' => $this->config['jsUrl'] . 'web/',
                    'actionUrl' => $this->config['actionUrl'],
                    'ctx' => $ctx,
                    'price_format' => json_decode(
                        $this->modx->getOption('ms2_price_format', null, '[2, ".", " "]'),
                        true
                    ),
                    'price_format_no_zeros' => (bool)$this->modx->getOption('ms2_price_format_no_zeros', null, true),
                    'weight_format' => json_decode(
                        $this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]'),
                        true
                    ),
                    'weight_format_no_zeros' => (bool)$this->modx->getOption('ms2_weight_format_no_zeros', null, true),
                ];

                $data = json_encode($js_setting, true);
                $this->modx->regClientStartupScript(
                    '<script>miniShop2Config = ' . $data . ';</script>',
                    true
                );
            } else {
                // Register notify plugin CSS
                $message_css = trim($this->modx->getOption('ms2_frontend_message_css'));
                if (!empty($message_css) && preg_match('/\.css/i', $message_css)) {
                    $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $message_css));
                }

                // Register JS
                $js = trim($this->modx->getOption('ms2_frontend_js'));
                if (!empty($js) && preg_match('/\.js/i', $js)) {
                    if (preg_match('/\.js$/i', $js)) {
                        $js .= '?v=' . substr(md5($this->version), 0, 10);
                    }
                    $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
                }

                $message_setting = [
                    'close_all_message' => $this->modx->lexicon('ms2_message_close_all'),
                ];

                $js_setting = [
                    'cssUrl' => $this->config['cssUrl'] . 'web/',
                    'jsUrl' => $this->config['jsUrl'] . 'web/',
                    'actionUrl' => $this->config['actionUrl'],
                    'ctx' => $ctx,
                    'price_format' => json_decode(
                        $this->modx->getOption('ms2_price_format', null, '[2, ".", " "]'),
                        true
                    ),
                    'price_format_no_zeros' => (bool)$this->modx->getOption('ms2_price_format_no_zeros', null, true),
                    'weight_format' => json_decode(
                        $this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]'),
                        true
                    ),
                    'weight_format_no_zeros' => (bool)$this->modx->getOption('ms2_weight_format_no_zeros', null, true),
                ];

                $data = json_encode(array_merge($message_setting, $js_setting), true);
                $this->modx->regClientStartupScript(
                    '<script>miniShop2Config = ' . $data . ';</script>',
                    true
                );

                // Register notify plugin JS
                $message_js = trim($this->modx->getOption('ms2_frontend_message_js'));
                if (!empty($message_js) && preg_match('/\.js/i', $message_js)) {
                    $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $message_js));
                }

                $message_settings_js = trim($this->modx->getOption('ms2_frontend_message_js_settings'));
                if (!empty($message_settings_js) && preg_match('/\.js/i', $message_settings_js)) {
                    $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $message_settings_js));
                }
            }
        }
    }

    /**
     * Handle frontend requests with actions
     *
     * @param $action
     * @param array $data
     *
     * @return array|bool|string
     */
    public function handleRequest($action, $data = [])
    {
        $ctx = !empty($data['ctx'])
            ? (string)$data['ctx']
            : 'web';
        if ($ctx != 'web') {
            $this->modx->switchContext($ctx);
        }
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        $this->initialize($ctx, ['json_response' => $isAjax]);

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
            require_once dirname(__FILE__, 3) . '/handlers/mscarthandler.class.php';
        }
        if (!class_exists('msOrderHandler')) {
            require_once dirname(__FILE__, 3) . '/handlers/msorderhandler.class.php';
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
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                'Could not initialize miniShop2 cart handler class: "' . $cart_class . '"'
            );

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
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                'Could not initialize miniShop2 order handler class: "' . $order_class . '"'
            );

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
        $services = $this->getSetting('ms2_services');
        $type = strtolower($type);
        $name = strtolower($name);
        if (!isset($services[$type])) {
            $services[$type] = [$name => $controller];
        } else {
            $services[$type][$name] = $controller;
        }

        $this->updateSetting('ms2_services', $services);
    }

    /**
     * Remove service from miniShop2
     *
     * @param $type
     * @param $name
     */
    public function removeService($type, $name)
    {
        $services = $this->getSetting('ms2_services');
        $type = strtolower($type);
        $name = strtolower($name);
        unset($services[$type][$name]);
        $this->updateSetting('ms2_services', $services);
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
        $services = $this->getSetting('ms2_services');

        if (is_array($services)) {
            return !empty($type) && isset($services[$type])
                ? $services[$type]
                : $services;
        }

        return [];
    }

    /**
     * Register plugin into miniShop2
     *
     * @param $name
     * @param $controller
     */
    public function addPlugin($name, $controller)
    {
        $plugins = $this->getSetting('ms2_plugins');
        $plugins[strtolower($name)] = $controller;

        $this->updateSetting('ms2_plugins', $plugins);
    }

    /**
     * Remove plugin from miniShop2
     *
     * @param $name
     */
    public function removePlugin($name)
    {
        $plugins = $this->getSetting('ms2_plugins');
        unset($plugins[strtolower($name)]);
        $this->updateSetting('ms2_plugins', $plugins);
    }

    /**
     * Get all registered plugins
     *
     * @return array|mixed
     */
    public function getPlugins()
    {
        return $this->getSetting('ms2_plugins');
    }

    /**
     * Load custom classes from specified directory
     *
     * @return void
     * @var string $type Type of class
     *
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
        $placeholders = [
            'base_path' => MODX_BASE_PATH,
            'core_path' => MODX_CORE_PATH,
            'assets_path' => MODX_ASSETS_PATH,
        ];
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
                if (strpos($file, MODX_BASE_PATH) === false && strpos($file, MODX_CORE_PATH) === false) {
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
        $placeholders = [
            'base_path' => MODX_BASE_PATH,
            'core_path' => MODX_CORE_PATH,
            'assets_path' => MODX_ASSETS_PATH,
        ];
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
                if (strpos($file, MODX_BASE_PATH) === false && strpos($file, MODX_CORE_PATH) === false) {
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
        $list = [];

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
                $o = [];
                $s = explode(' ', str_replace(['_', '-'], ' ', $type));
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
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                'Could not initialize miniShop2 option type class: "' . $className . '"'
            );

            return null;
        }
    }

    /**
     * @param array $options
     * @param array|string $sorting
     *
     * @return array
     */
    public function sortOptionValues(array $options, $sorting)
    {
        if (!empty($sorting)) {
            $sorting = array_map('trim', is_array($sorting) ? $sorting : explode(',', $sorting));
            foreach ($sorting as $sort) {
                @list($key, $order, $type, $first) = explode(':', $sort);
                if (array_key_exists($key, $options)) {
                    $order = empty($order) ? SORT_ASC : constant($order);
                    $type = empty($type) ? SORT_STRING : constant($type);

                    $values = &$options[$key];
                    if (isset($options[$key]['value'])) {
                        $values = &$options[$key]['value'];
                    }

                    array_multisort($values, $order, $type);

                    if (!is_null($first) && ($index = array_search($first, $values)) !== false) {
                        unset($values[$index]);
                        array_unshift($values, $first);
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Loads additional metadata for miniShop2 objects
     */
    public function loadMap()
    {
        if (method_exists($this->pdoTools, 'makePlaceholders')) {
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
        } else {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                'pdoTools not installed, metadata for miniShop2 objects not loaded'
            );
        }
    }

    /**
     * Returns id for current customer. If customer is not exists, registers him and returns id.
     *
     * @return integer $id
     */
    public function getCustomerId()
    {
        $customer = null;

        $response = $this->invokeEvent('msOnBeforeGetOrderCustomer', [
            'order' => $this->order,
            'customer' => $customer,
        ]);
        if (!$response['success']) {
            return $response['message'];
        }

        if (!$customer) {
            $data = $this->order->get();
            $email = $data['email'] ?? '';
            $receiver = $data['receiver'] ?? '';
            $phone = $data['phone'] ?? '';
            if (empty($receiver)) {
                $receiver = $email
                    ? substr($email, 0, strpos($email, '@'))
                    : ($phone
                        ? preg_replace('#[^0-9]#', '', $phone)
                        : uniqid('user_', false));
            }
            if (empty($email)) {
                $email = $receiver . '@' . $this->modx->getOption('http_host');
            }

            if ($this->modx->user->isAuthenticated()) {
                $profile = $this->modx->user->Profile;
                if (!$profile->get('email')) {
                    $profile->set('email', $email);
                    $profile->save();
                }
                $customer = $this->modx->user;
            } else {
                $c = $this->modx->newQuery('modUser');
                $c->leftJoin('modUserProfile', 'Profile');
                $filter = ['username' => $email, 'OR:Profile.email:=' => $email];
                if (!empty($phone)) {
                    $filter['OR:Profile.mobilephone:='] = $phone;
                }
                $c->where($filter);
                $c->select('modUser.id');
                if (!$customer = $this->modx->getObject('modUser', $c)) {
                    $customer = $this->modx->newObject('modUser', ['username' => $email, 'password' => md5(rand())]);
                    $profile = $this->modx->newObject('modUserProfile', [
                        'email' => $email,
                        'fullname' => $receiver,
                        'mobilephone' => $phone
                    ]);
                    $customer->addOne($profile);
                    /** @var modUserSetting $setting */
                    $setting = $this->modx->newObject('modUserSetting');
                    $setting->fromArray([
                        'key' => 'cultureKey',
                        'area' => 'language',
                        'value' => $this->modx->getOption('cultureKey', null, 'en', true),
                    ], '', true);
                    $customer->addMany($setting);
                    if (!$customer->save()) {
                        $customer = null;
                    } elseif ($groups = $this->modx->getOption('ms2_order_user_groups', null, false)) {
                        $groupRoles = array_map('trim', explode(',', $groups));
                        foreach ($groupRoles as $groupRole) {
                            $groupRole = explode(':', $groupRole);
                            if (count($groupRole) > 1 && !empty($groupRole[1])) {
                                if (is_numeric($groupRole[1])) {
                                    $roleId = (int)$groupRole[1];
                                } else {
                                    $roleId = $groupRole[1];
                                }
                            } else {
                                $roleId = null;
                            }
                            $customer->joinGroup($groupRole[0], $roleId);
                        }
                    }
                }
            }
        }

        $response = $this->invokeEvent('msOnGetOrderCustomer', [
            'order' => $this->order,
            'customer' => $customer,
        ]);
        if (!$response['success']) {
            return $response['message'];
        }

        return $customer instanceof modUser
            ? $customer->get('id')
            : 0;
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
        /** @var msOrder $order */
        if (!$order = $this->modx->getObject('msOrder', ['id' => $order_id], false)) {
            return $this->modx->lexicon('ms2_err_order_nf');
        }

        $ctx = $order->get('context');
        $this->modx->switchContext($ctx);
        $this->initialize($ctx);
        // This method could be overwritten from custom order handler
        if (is_object($this->order) && method_exists($this->order, 'changeOrderStatus')) {
            return $this->order->changeOrderStatus($order_id, $status_id);
        }

        $error = '';
        /** @var msOrderStatus $status */
        $status = $this->modx->getObject('msOrderStatus', ['id' => $status_id, 'active' => 1]);
        if (!$status) {
            $error = 'ms2_err_status_nf';
            return $this->modx->lexicon($error);
        }

        /** @var msOrderStatus $old_status */
        $old_status = $this->modx->getObject(
            'msOrderStatus',
            ['id' => $order->get('status'), 'active' => 1]
        );
        if ($old_status) {
            if ($old_status->get('final')) {
                $error = 'ms2_err_status_final';
                return $this->modx->lexicon($error);
            }
            if ($old_status->get('fixed')) {
                if ($status->get('rank') <= $old_status->get('rank')) {
                    $error = 'ms2_err_status_fixed';
                    return $this->modx->lexicon($error);
                }
            }
        }
        if ($order->get('status') == $status_id) {
            $error = 'ms2_err_status_same';
            return $this->modx->lexicon($error);
        }

        $response = $this->invokeEvent('msOnBeforeChangeOrderStatus', [
            'order' => $order,
            'status' => $order->get('status'),
        ]);
        if (!$response['success']) {
            return $response['message'];
        }

        $order->set('status', $status_id);

        if ($order->save()) {
            $this->orderLog($order->get('id'), 'status', $status_id);
            $response = $this->invokeEvent('msOnChangeOrderStatus', [
                'order' => $order,
                'status' => $status_id,
            ]);
            if (!$response['success']) {
                return $response['message'];
            }

            $lang = $this->modx->getOption('cultureKey', null, 'en', true);
            $userLang = $this->modx->getObject(
                'modUserSetting',
                ['key' => 'cultureKey', 'user' => $order->get('user_id')]
            );
            $contextLang = $this->modx->getObject(
                'modContextSetting',
                ['key' => 'cultureKey', 'context_key' => $order->get('context')]
            );
            if ($userLang) {
                $lang = $userLang->get('value');
            } elseif ($contextLang) {
                $lang = $contextLang->get('value');
            }
            $this->modx->setOption('cultureKey', $lang);
            $this->modx->lexicon->load($lang . ':minishop2:default', $lang . ':minishop2:cart');

            $pls = $order->toArray();
            $pls['cost'] = $this->formatPrice($pls['cost']);
            $pls['cart_cost'] = $this->formatPrice($pls['cart_cost']);
            $pls['delivery_cost'] = $this->formatPrice($pls['delivery_cost']);
            $pls['weight'] = $this->formatWeight($pls['weight']);
            $pls['payment_link'] = '';
            if ($tv_list = $this->modx->getOption('ms2_order_tv_list', null, '')) {
                $pls['includeTVs'] = $tv_list;
            }
            $payment = $order->getOne('Payment');
            if ($payment) {
                $class = $payment->get('class');
                if ($class) {
                    $this->loadCustomClasses('payment');
                    if (class_exists($class)) {
                        /** @var msPaymentHandler $handler */
                        $handler = new $class($order);
                        if (method_exists($handler, 'getPaymentLink')) {
                            $link = $handler->getPaymentLink($order);
                            $pls['payment_link'] = $link;
                        }
                    }
                }
            }

            $useScheduler = $this->modx->getOption('ms2_use_scheduler', null, false);
            $task = null;
            if ($useScheduler) {
                /** @var Scheduler $scheduler */
                $path = $this->modx->getOption(
                    'scheduler.core_path',
                    null,
                    $this->modx->getOption('core_path') . 'components/scheduler/'
                );
                $scheduler = $this->modx->getService('scheduler', 'Scheduler', $path . 'model/scheduler/');
                if ($scheduler) {
                    $task = $scheduler->getTask('minishop2', 'ms2_send_email');
                    if (!$task) {
                        $task = $this->createEmailTask();
                    }
                } else {
                    $useScheduler = false;
                    $this->modx->log(1, 'not found Scheduler extra');
                }
            }

            if ($status->get('email_manager')) {
                $subject = $this->pdoTools->getChunk('@INLINE ' . $status->get('subject_manager'), $pls);
                $tpl = '';
                if ($chunk = $this->modx->getObject('modChunk', ['id' => $status->get('body_manager')])) {
                    $tpl = $chunk->get('name');
                }
                $body = $this->modx->runSnippet('msGetOrder', array_merge($pls, ['tpl' => $tpl]));
                $emails = array_map(
                    'trim',
                    explode(
                        ',',
                        $this->modx->getOption('ms2_email_manager', null, $this->modx->getOption('emailsender'))
                    )
                );
                if (!empty($subject)) {
                    foreach ($emails as $email) {
                        if (preg_match('#.*?@#', $email)) {
                            if ($useScheduler && $task instanceof sTask) {
                                $task->schedule('+1 second', [
                                    'email' => $email,
                                    'subject' => $subject,
                                    'body' => $body
                                ]);
                            } else {
                                $this->sendEmail($email, $subject, $body);
                            }
                        }
                    }
                }
            }

            if ($status->get('email_user')) {
                if ($profile = $this->modx->getObject('modUserProfile', ['internalKey' => $pls['user_id']])) {
                    $subject = $this->pdoTools->getChunk('@INLINE ' . $status->get('subject_user'), $pls);
                    $tpl = '';
                    if ($chunk = $this->modx->getObject('modChunk', ['id' => $status->get('body_user')])) {
                        $tpl = $chunk->get('name');
                    }
                    $body = $this->modx->runSnippet('msGetOrder', array_merge($pls, ['tpl' => $tpl]));
                    $email = $profile->get('email');
                    if (!empty($subject) && preg_match('#.*?@#', $email)) {
                        if ($useScheduler && $task instanceof sTask) {
                            $task->schedule('+1 second', [
                                'email' => $email,
                                'subject' => $subject,
                                'body' => $body
                            ]);
                        } else {
                            $this->sendEmail($email, $subject, $body);
                        }
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
     * @return bool
     */
    public function sendEmail($email, $subject, $body = '')
    {
        $result = true;
        $this->modx->getParser()->processElementTags('', $body, true, false, '[[', ']]', [], 10);
        $this->modx->getParser()->processElementTags('', $body, true, true, '[[', ']]', [], 10);

        /** @var modPHPMailer $mail */
        $mail = $this->modx->getService('mail', 'mail.modPHPMailer');
        $mail->setHTML(true);

        $mail->address('to', trim($email));
        $mail->set(modMail::MAIL_SUBJECT, trim($subject));
        $mail->set(modMail::MAIL_BODY, $body);
        $mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        if (!$mail->send()) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo
            );
            $result = false;
        }
        $mail->reset();
        return $result;
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
        if (!$order = $this->modx->getObject('msOrder', ['id' => $order_id])) {
            return false;
        }

        if (empty($this->modx->request)) {
            $this->modx->getRequest();
        }

        $user_id = ($action == 'status' && $entry == 1) || !$this->modx->user->id
            ? $order->get('user_id')
            : $this->modx->user->id;
        $log = $this->modx->newObject('msOrderLog', [
            'order_id' => $order_id,
            'user_id' => $user_id,
            'timestamp' => time(),
            'action' => $action,
            'entry' => $entry,
            'ip' => $this->modx->request->getClientIp(),
        ]);

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
            $pf = [2, '.', ' '];
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
            $wf = [3, '.', ' '];
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
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param $eventName
     * @param array $params
     * @param $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = [], $glue = '<br/>')
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

        return [
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        ];
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
    public function error($message = '', $data = [], $placeholders = [])
    {
        $response = [
            'success' => false,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        ];

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
    public function success($message = '', $data = [], $placeholders = [])
    {
        $response = [
            'success' => true,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        ];

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
    public function runProcessor($action = '', $data = [])
    {
        if (empty($action)) {
            return false;
        }
        $this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath'])
            ? $this->config['processorsPath']
            : MODX_CORE_PATH . 'components/minishop2/processors/';

        return $this->modx->runProcessor($action, $data, [
            'processors_path' => $processorsPath,
        ]);
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
            $path = strtr($path, ['\\' => '/']);

            preg_match('#[^/]+$#', $path, $file);
            preg_match('#([^/]+)[.$]+(.*)#', $path, $file_ext);
            preg_match('#(.*)[/$]+#', $path, $dirname);

            $info = [
                'dirname' => (isset($dirname[1]))
                    ? $dirname[1]
                    : '.',
                'basename' => $file[0],
                'extension' => (isset($file_ext[2]))
                    ? $file_ext[2]
                    : '',
                'filename' => (isset($file_ext[1]))
                    ? $file_ext[1]
                    : $file[0],
            ];
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
    protected function getSetting($key)
    {
        $setting = $this->modx->getObject('modSystemSetting', ['key' => $key]);
        if (!$setting) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('key', $key);
            $setting->set('value', '[]');
            $setting->save();
        }

        $value = json_decode($setting->get('value'), true);
        if (!is_array($value)) {
            $value = [];
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
    protected function updateSetting($key, $value)
    {
        $setting = $this->modx->getObject('modSystemSetting', ['key' => $key]);
        if (!$setting) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('key', $key);
        }
        $setting->set('value', json_encode($value));
        $setting->save();
    }

    /**
     * Creating Sheduler's task for sending email
     * @return false|object|null
     */
    private function createEmailTask()
    {
        $task = $this->modx->newObject('sFileTask');
        $task->fromArray([
            'class_key' => 'sFileTask',
            'content' => '/tasks/sendEmail.php',
            'namespace' => 'minishop2',
            'reference' => 'ms2_send_email',
            'description' => 'MiniShop2 Email'
        ]);
        if (!$task->save()) {
            return false;
        }
        return $task;
    }
}
