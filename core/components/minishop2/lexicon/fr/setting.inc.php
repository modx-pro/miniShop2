<?php
/**
 * Settings English Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */
$_lang['area_ms2_category'] = 'Catégorie d\'article';
$_lang['area_ms2_product'] = 'Article';
$_lang['area_ms2_gallery'] = 'Gallerie';
$_lang['area_ms2_cart'] = 'Panier';
$_lang['area_ms2_order'] = 'Commande';
$_lang['area_ms2_frontend'] = 'Frontal web';
$_lang['area_ms2_payment'] = 'Payments';

$_lang['setting_ms2_category_grid_fields'] = 'Champs de la tableau d\'articles';
$_lang['setting_ms2_category_grid_fields_desc'] = 'Liste, séparée par des virgules, des champs visibles du tableau des articles dans la catégorie.';
$_lang['setting_ms2_product_main_fields'] = 'Champs principaux du panneau de l\'article';
$_lang['setting_ms2_product_main_fields_desc'] = 'Une liste de champs séparée par des virgules de la page d\'un article. Par exemple: "pagetitle,longtitle,content".';
$_lang['setting_ms2_product_extra_fields'] = 'Champs complémentaires de la page des articles';
$_lang['setting_ms2_product_extra_fields_desc'] = 'Une liste de champs séparée par des virgules de la page d\'un article., qui sont nécessaire a votre boutique. Par exemple : "price,old_price,weight".';

$_lang['setting_ms2_category_show_comments'] = 'Afficher les commentaires de la catégorie';
$_lang['setting_ms2_category_show_comments_desc'] = 'Afficher les commentaires de tous les articles de la catégories si le composant "Tickets" est installé.';
$_lang['setting_ms2_category_show_nested_products'] = 'Montrez le article inclus dans la catégorie';
$_lang['setting_ms2_category_show_nested_products_desc'] = 'Si vrai, vous verrez tous les article inclus dans la catégorie. Ils auront une autre couleur et le nom de leur catégorie apparaitra sous "pagetitle".';
$_lang['setting_ms2_product_show_comments'] = 'Afficher les commentaires sur l\'article';
$_lang['setting_ms2_product_show_comments_desc'] = 'Montrez les commentaires de l\'article si le composant "Tickets" est installé.';
$_lang['setting_ms2_template_product_default'] = 'Modèle par défaut pour un nouvel article';
$_lang['setting_ms2_template_product_default_desc'] = 'Sélectionnez le modèle affecté par défaut lors de la création d\'un nouvel article.';
$_lang['setting_ms2_product_show_in_tree_default'] = 'Montrez dans l\'arbre par défaut';
$_lang['setting_ms2_product_show_in_tree_default_desc'] = 'Si vous activez cette option, tous les articles seront montrés dans l\'arbre des ressources.';
$_lang['setting_ms2_product_source_default'] = 'Source des médias par défaut';
$_lang['setting_ms2_product_source_default_desc'] = 'Source des médias par défaut pour la gallerie des articles.';
$_lang['setting_ms2_product_vertical_tabs'] = 'Onglet verticaux de la page des articles';
$_lang['setting_ms2_product_vertical_tabs_desc'] = 'Comment afficher la page des articles dans le gestionnaire ? Désactiver cette option vous permet d\'ajuster la page de l\'article sur ​​l\'écran avec une petite taille horizontale. Non recommandé.';
$_lang['setting_ms2_product_thumbnail_size'] = 'Taille des vignettes par défaut';
$_lang['setting_ms2_product_thumbnail_size_desc'] = 'Size of default pre-generated thumbnail for field "thumb" in msProduct table. Of course, this size should exist in the settings of your media source that generates the previews. Otherwise you will receive  miniShop2 logo instead of product image in manager.';
$_lang['ms2_source_thumbnails_desc'] = 'JSON encoded array of options for generating thumbnails.';

$_lang['setting_ms2_cart_handler_class'] = 'Classe du gestionnaire de panier';
$_lang['setting_ms2_cart_handler_class_desc'] = 'The name of the class that implements the logic of a cart.';
$_lang['setting_ms2_order_handler_class'] = 'Order handler class';
$_lang['setting_ms2_order_handler_class_desc'] = 'The name of the class that implements the logic of an ordering.';
$_lang['setting_ms2_order_user_groups'] = 'Groups for registering customers';
$_lang['setting_ms2_order_user_groups_desc'] = 'Comma-separated list of user groups for adding new users when they orders.';
$_lang['setting_ms2_email_manager'] = 'Managers mailboxes';
$_lang['setting_ms2_email_manager_desc'] = 'Comma-separated list of mailboxes of managers, for sending notifications about changes of the status of the order';
$_lang['setting_ms2_date_format'] = 'Format des dates';
$_lang['setting_ms2_date_format_desc'] = 'You can specify how to format miniShop2 dates using php strftime() syntax. By default format is "%d.%m.%y %H:%M".';
$_lang['setting_ms2_price_format'] = 'Format des prix';
$_lang['setting_ms2_price_format_desc'] = 'You can specify, how to format prices of product by function number_format(). For this used JSON string with array of 3 values: number of decimals, decimals separator and thousands separator. By default format is [2,"."," "], that transforms "15336.6" into "15 336.60"';
$_lang['setting_ms2_price_format_no_zeros'] = 'Remove extra zeros in the prices';
$_lang['setting_ms2_price_format_no_zeros_desc'] = 'By default, weight of goods shown with 2 decimals: "15.20". If enabled this option, extra zeroes at the end will removed and price transforms to "15.2"';
$_lang['setting_ms2_weight_format'] = 'Format of weight';
$_lang['setting_ms2_weight_format_desc'] = 'You can specify, how to format weight of product by function number_format(). For this used JSON string with array of 3 values: number of decimals, decimals separator and thousands separator. By default format is [3,"."," "], that transforms "141.3" into "141.300"';
$_lang['setting_ms2_weight_format_no_zeros'] = 'Remove extra zeros in the weight';
$_lang['setting_ms2_weight_format_no_zeros_desc'] = 'By default, weight of goods shown with 3 decimals: "15.250". If enabled this option, extra zeroes at the end will removed and weight transforms to "15.25".';

$_lang['setting_ms2_frontend_css'] = 'Styles du frontal web';
$_lang['setting_ms2_frontend_css_desc'] = 'Path to file with styles of the shop. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_ms2_frontend_js'] = 'Scripts du frontal web';
$_lang['setting_ms2_frontend_js_desc'] = 'Path to file with scripts of the shop. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';

$_lang['setting_ms2_payment_paypal_api_url'] = 'URL de l\'API PayPal';
$_lang['setting_ms2_payment_paypal_checkout_url'] = 'URL de caisse PayPal';
$_lang['setting_ms2_payment_paypal_currency'] = 'Device PayPal';
$_lang['setting_ms2_payment_paypal_user'] = 'Utilisateur PayPal';
$_lang['setting_ms2_payment_paypal_pwd'] = 'Mot de passe PayPal';
$_lang['setting_ms2_payment_paypal_signature'] = 'Signature PayPal';
$_lang['setting_ms2_payment_paypal_success_id'] = 'PayPal successful page id';
$_lang['setting_ms2_payment_paypal_cancel_id'] = 'PayPal cancel page id';
$_lang['setting_ms2_payment_paypal_cancel_order'] = 'PayPal cancel order';
$_lang['setting_ms2_payment_paypal_cancel_order_desc'] = 'If true, order will be cancelled if customer cancel payment.';