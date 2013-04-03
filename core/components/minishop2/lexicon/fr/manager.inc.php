<?php
/**
 * Manager French Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */
$_lang['ms2_menu_create'] = 'Créer';
$_lang['ms2_menu_update'] = 'Mettre à jour';
$_lang['ms2_menu_remove'] = 'Supprimer';
$_lang['ms2_menu_remove_multiple'] = 'Supprimer la sélection';
$_lang['ms2_menu_remove_confirm'] = 'Êtes vous sûr de vouloir supprimer la sélection ?';
$_lang['ms2_menu_remove_multiple_confirm'] = 'Êtes vous sûr de vouloir supprimer toutes les entrées sélectionnées ?';

$_lang['ms2_combo_select'] = 'Cliquer pour sélectionner';
$_lang['ms2_combo_select_status'] = 'Filtrer par l\'état';

$_lang['ms2_id'] = 'Id';
$_lang['ms2_name'] = 'Nom';
$_lang['ms2_color'] = 'Couleur';
$_lang['ms2_country'] = 'Pays';
$_lang['ms2_logo'] = 'Logo';
$_lang['ms2_address'] = 'Adresse';
$_lang['ms2_phone'] = 'Téléphone';
$_lang['ms2_fax'] = 'Fax';
$_lang['ms2_email'] = 'Courriel';
$_lang['ms2_active'] = 'Actif';
$_lang['ms2_class'] = 'Gestionnaire de classe';
$_lang['ms2_description'] = 'Description';
$_lang['ms2_num'] = 'Nombre';
$_lang['ms2_status'] = 'États';
$_lang['ms2_count'] = 'Compter';
$_lang['ms2_cost'] = 'Coût';
$_lang['ms2_order_cost'] = 'Montant de la commande';
$_lang['ms2_cart_cost'] = 'Coût des produits';
$_lang['ms2_delivery_cost'] = 'Coût de la livraison';
$_lang['ms2_weight'] = 'Poids';
$_lang['ms2_createdon'] = 'Créé le';
$_lang['ms2_updatedon'] = 'Mis a jour le';
$_lang['ms2_user'] = 'Utilisateur';
$_lang['ms2_timestamp'] = 'Horodatage';
$_lang['ms2_order_log'] = 'Journal de commande';
$_lang['ms2_order_products'] = 'Articles';
$_lang['ms2_action'] = 'Action';
$_lang['ms2_entry'] = 'Écriture';
$_lang['ms2_username'] = 'Nom de l\'utilisateur';
$_lang['ms2_fullname'] = 'Nom complet';
$_lang['ms2_resource'] = 'Ressource';

$_lang['ms2_receiver'] = 'Destinnataire';
$_lang['ms2_index'] = 'Code postal';
$_lang['ms2_region'] = 'Région';
$_lang['ms2_city'] = 'Ville';
$_lang['ms2_metro'] = 'Métro';
$_lang['ms2_street'] = 'Rue';
$_lang['ms2_building'] = 'Immeuble';
$_lang['ms2_room'] = 'Appartement';
$_lang['ms2_comment'] = 'Commentaire';

$_lang['ms2_email_user'] = 'Message à l\'utilisateur';
$_lang['ms2_email_manager'] = 'Message au responsable';
$_lang['ms2_subject_user'] = 'Sujet du message pour l\'utilisateur';
$_lang['ms2_subject_manager'] = 'Sujet du message pour le responsable';
$_lang['ms2_body_user'] = 'Partie du message pour l\'utilisateur';
$_lang['ms2_body_manager'] = 'Partie du message au responsable';
$_lang['ms2_status_final'] = 'Finale';
$_lang['ms2_status_final_help'] = '';
$_lang['ms2_status_fixed'] = 'Correction d\'';
$_lang['ms2_status_fixed_help'] = '';
$_lang['ms2_options'] = 'Options';
$_lang['ms2_price'] = 'Prix';
$_lang['ms2_price_help'] = 'Coût minimal de la livraison';
$_lang['ms2_weight_price'] = 'Prix par unité de poids';
$_lang['ms2_weight_price_help'] = 'Coût supplémentaire par unité de poids.<br/>Peut être utilisé dans des classes personnalisées.';
$_lang['ms2_distance_price'] = 'Prix par unité de distance';
$_lang['ms2_distance_price_help'] = 'Coût supplémentaire par unité de distance.<br/>Peut être utilisé dans des classes personnalisées.';
$_lang['ms2_order_requires'] = 'Champs requis';
$_lang['ms2_order_requires_help'] = 'Lors de la commande, une classe personnalisée peut exiger le remplissage d\'un de ses champs';

$_lang['ms2_orders_selected_status'] = 'Changer l\'état de la commande sélectionnée';

$_lang['ms2_link_name'] = 'Nom du lien';
$_lang['ms2_link_one_to_one'] = 'Un pour un';
$_lang['ms2_link_one_to_one_desc'] = 'Liaison d\égalité de 2 articles. Si vous voulez lier plus de 2 articles, vous devez utiliser le type de relation "Plusieurs à plusieurs".';
$_lang['ms2_link_one_to_many'] = 'Un à plusieurs';
$_lang['ms2_link_one_to_many_desc'] = 'Liaison d\'un article maitre avec ses esclaves. Par exemple, un article est un ensemble d\'autres articles. Bien adapté aussi pour préciser des articles recommandés.';
$_lang['ms2_link_many_to_one'] = 'Plusieurs à un';
$_lang['ms2_link_many_to_one_desc'] = 'Liaison des articles esclaves vers l\'article maitre, les exclaves non aucun lien entre eux. Par exemple, les articles inclus dans un "kit".';
$_lang['ms2_link_many_to_many'] = 'Plusieurs à plusieurs';
$_lang['ms2_link_many_to_many_desc'] = 'Liaison d\'égalité entre plusieurs articles. Tous les articles sont liés entre eux. L\'ajout d\'une nouvelle liaison a un article, implique l\'ajout de cette liaison à tous les autres. Par exemple un paramètre de couleur, de taille, de langue, de version, etc.';
$_lang['ms2_link_master'] = 'Article maitre';
$_lang['ms2_link_slave'] = 'Article esclave';