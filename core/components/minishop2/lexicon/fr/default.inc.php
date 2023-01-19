<?php

/**
 * Default Russian Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */

include_once('setting.inc.php');
$files = scandir(dirname(__FILE__));
foreach ($files as $file) {
    if (strpos($file, 'msp.') === 0) {
        @include_once($file);
    }
}

$_lang['minishop2'] = 'miniShop2';
$_lang['ms2_menu_desc'] = 'Impressionnante extension de e-commerce';
$_lang['ms2_order'] = 'Commande';
$_lang['ms2_orders'] = 'Commandes';
$_lang['ms2_orders_intro'] = 'Gestion de vos commandes';
$_lang['ms2_orders_desc'] = 'Gestion de vos commandes';
$_lang['ms2_settings'] = 'Paramètres';
$_lang['ms2_settings_intro'] = 'Paramètres principaux du magasin. Ici vous pouvez définir les moyens de paiements, les méthodes de livraisons et l\'état des commandes';
$_lang['ms2_settings_desc'] = 'États des commandes, options de paiements et de livraisons';
$_lang['ms2_system_settings'] = 'Системные настройки';
$_lang['ms2_system_settings_desc'] = 'Системные настройки miniShop2';
$_lang['ms2_payment'] = 'Paiement';
$_lang['ms2_payments'] = 'Paiements';
$_lang['ms2_payments_intro'] = 'Vous pouvez créer n\'importe quel type de paiements. La logique de paiement (redirection de l\'acheteur sur un service distant, la réception du paiement, etc.) est mis en oeuvre dans la "classe" que vous indiquez.<br/>Pour les méthodes de paiements le paramètre "classe" est nécessaire.';
$_lang['ms2_delivery'] = 'Livraison';
$_lang['ms2_deliveries'] = 'Options de livraisons';
$_lang['ms2_deliveries_intro'] = 'Options possibles pour la livraison. Définit la logique du calcul des coûts d\'expédition en fonction de la distance et de la catégorie de poids.<br/> Si vous ne spécifiez pas de classe, les calculs seront effectués par l\'algorithme par défaut.';
$_lang['ms2_statuses'] = 'États';
$_lang['ms2_statuses_intro'] = 'Il y a plusieurs états obligatoires dans une commande: "nouveau", "payée", "envoyée" et "annulée". Ils peuvent être modifiés mais pas enlevés car ils sont indispensables au fonctionnement du magasin. Vous pouvez définir vos propre états pour étendre la logique de travail avec les commandes.<br/>Un état peut être "final", cela signifie que vous ne pourrez plus le modifier pour un autre, par exemple, "envoyé" et "annulé". Un état peut être fixé, c\'est à dire qu\'une fois positionné vous ne pourrez pas revenir a un état précédent, un état "payé" ne peut pas être remis sur "nouveau".';
$_lang['ms2_vendors'] = 'Fournisseurs des articles';
$_lang['ms2_vendors_intro'] = 'Список возможных производителей товаров. То, что вы сюда добавите, можно выбрать в поле "vendor" товара.';
$_lang['ms2_link'] = 'Lien de produits';
$_lang['ms2_links'] = 'Lien de produits';
$_lang['ms2_links_intro'] = 'Liste des liens possible de produits entre eux. Le type de connexion décrit exactement comment il va fonctionner, il n\'est pas possible d\'en créer de nouveau, vous pouvez seulement le sélectionner dans la liste.';
$_lang['ms2_option'] = 'Свойство товаров';
$_lang['ms2_options'] = 'Свойства товаров';
$_lang['ms2_options_intro'] = 'Список возможных свойств товаров. Дерево категорий используется для фильтрации свойств выбранных категорий.<br/>Чтобы назначить категориям сразу несколько опций, выберите их через Ctrl(Cmd) или Shift.';
$_lang['ms2_options_category_intro'] = 'Список возможных свойств товаров в данной категории.';
$_lang['ms2_default_value'] = 'Значение по умолчанию';
$_lang['ms2_customer'] = 'Clients';
$_lang['ms2_all'] = 'Tout';
$_lang['ms2_type'] = 'Type';

$_lang['ms2_btn_create'] = 'Création';
$_lang['ms2_btn_copy'] = 'Скопировать';
$_lang['ms2_btn_save'] = 'Enregistrer';
$_lang['ms2_btn_edit'] = 'Modifier';
$_lang['ms2_btn_view'] = 'Voir';
$_lang['ms2_btn_delete'] = 'Supprimer';
$_lang['ms2_btn_undelete'] = 'Récupérer';
$_lang['ms2_btn_publish'] = 'Publier';
$_lang['ms2_btn_unpublish'] = 'dépublier';
$_lang['ms2_btn_cancel'] = 'Annuler';
$_lang['ms2_btn_back'] = 'Retour (alt + &uarr;)';
$_lang['ms2_btn_prev'] = 'Précédent (alt + &larr;)';
$_lang['ms2_btn_next'] = 'Suivant (alt + &rarr;)';
$_lang['ms2_btn_help'] = 'Aide';
$_lang['ms2_btn_duplicate'] = 'Dupliquer un article';
$_lang['ms2_btn_addoption'] = 'Добавить';
$_lang['ms2_btn_assign'] = 'Назначить';

$_lang['ms2_actions'] = 'Actions';
$_lang['ms2_search'] = 'Chercher';
$_lang['ms2_search_clear'] = 'Éffacer';

$_lang['ms2_category'] = 'Catégorie des articles';
$_lang['ms2_category_tree'] = 'Arbre des catégories';
$_lang['ms2_category_type'] = 'Catégorie des articles';
$_lang['ms2_category_create'] = 'Ajout de catégorie';
$_lang['ms2_category_create_here'] = 'Catégorie ayant des articles';
$_lang['ms2_category_manage'] = 'Gestion des catégories';
$_lang['ms2_category_duplicate'] = 'Copie la catégorie';
$_lang['ms2_category_publish'] = 'Publie la catégorie';
$_lang['ms2_category_unpublish'] = 'Dépublie la catégorie';
$_lang['ms2_category_delete'] = 'Supprime la catégorie';
$_lang['ms2_category_undelete'] = 'Restaure la catégorie';
$_lang['ms2_category_view'] = 'Mettre en ligne';
$_lang['ms2_category_new'] = 'Nouvelle catégorie';
$_lang['ms2_category_option_add'] = 'Добавить характеристику';
$_lang['ms2_category_option_rank'] = 'Порядок сортировки';
$_lang['ms2_category_show_nested'] = 'Показывать вложенные товары';

$_lang['ms2_product'] = 'Article du magasin';
$_lang['ms2_product_type'] = 'Article du magasin';
$_lang['ms2_product_create_here'] = 'Article de la catégorie';
$_lang['ms2_product_create'] = 'Ajout d\'article';

$_lang['ms2_option_type'] = 'Тип свойства';

$_lang['ms2_frontend_currency'] = 'EUR';
$_lang['ms2_frontend_weight_unit'] = 'kg';
$_lang['ms2_frontend_count_unit'] = 'pcs';
$_lang['ms2_frontend_add_to_cart'] = 'Ajout au panier';
$_lang['ms2_frontend_tags'] = 'Étiquettes';
$_lang['ms2_frontend_colors'] = 'Couleurs';
$_lang['ms2_frontend_color'] = 'Couleur';
$_lang['ms2_frontend_sizes'] = 'Tailles';
$_lang['ms2_frontend_size'] = 'Taille';
$_lang['ms2_frontend_popular'] = 'Populaire';
$_lang['ms2_frontend_favorite'] = 'Favori';
$_lang['ms2_frontend_new'] = 'Nouveau';
$_lang['ms2_frontend_deliveries'] = 'Livraisons';
$_lang['ms2_frontend_delivery'] = 'Доставка';
$_lang['ms2_frontend_payments'] = 'Paiements';
$_lang['ms2_frontend_payment'] = 'Оплата';
$_lang['ms2_frontend_delivery_select'] = 'Choisissez une livraison';
$_lang['ms2_frontend_payment_select'] = 'Choisisez un moyen de paiement';
$_lang['ms2_frontend_credentials'] = 'Identités';
$_lang['ms2_frontend_address'] = 'Adresse';

$_lang['ms2_frontend_comment'] = 'Commentaire';
$_lang['ms2_frontend_receiver'] = 'Destinataire';
$_lang['ms2_frontend_email'] = 'Courriel';
$_lang['ms2_frontend_phone'] = 'Téléphone';
$_lang['ms2_frontend_index'] = 'Code postal';
$_lang['ms2_frontend_country'] = 'Pays';
$_lang['ms2_frontend_region'] = 'Département';
$_lang['ms2_frontend_city'] = 'Ville';
$_lang['ms2_frontend_street'] = 'Rue';
$_lang['ms2_frontend_building'] = 'Immeuble';
$_lang['ms2_frontend_room'] = 'Porte';
$_lang['ms2_frontend_entrance'] = 'Подъезд';
$_lang['ms2_frontend_floor'] = 'Этаж';
$_lang['ms2_frontend_text_address'] = 'Адрес одной строкой';

$_lang['ms2_frontend_order_cost'] = 'Coût total';
$_lang['ms2_frontend_order_submit'] = 'Paiement!';
$_lang['ms2_frontend_order_cancel'] = 'RaZ du formulaire';
$_lang['ms2_frontend_order_success'] = 'Merci pour votre commande <b>#[[+num]]</b> sur notre site <b>[[++site_name]]</b>!';

$_lang['ms2_message_close_all'] = 'fermer tout';
$_lang['ms2_err_unknown'] = 'Erreur non référencée';
$_lang['ms2_err_ns'] = 'Ce champs est requis';
$_lang['ms2_err_ae'] = 'Ce champs doit être unique';
$_lang['ms2_err_json'] = 'Это поле требует JSON строку';

$_lang['ms2_err_user_nf'] = 'Utilisateur introuvable.';
$_lang['ms2_err_order_nf'] = 'Aucune commande avec cet ID n\'a été trouvée.';
$_lang['ms2_err_status_nf'] = 'Aucun état avec cetID n\'a été trouvé.';
$_lang['ms2_err_delivery_nf'] = 'Aucune livraison avec cet ID n\'a été trouvée.';
$_lang['ms2_err_payment_nf'] = 'Aucun paiement avec cet ID n\' été trouvé.';
$_lang['ms2_err_status_final'] = 'L\'état Terminé est posistionné, vous ne pouvez pas le modifier.';
$_lang['ms2_err_status_fixed'] = 'L\'état Vérouillé est positionné, vous ne pouvez pas revenir à un état précedant.';
$_lang['ms2_err_status_wrong'] = 'Неверный статус заказа.';
$_lang['ms2_err_status_same'] = 'Cet état est déjà positionné.';
$_lang['ms2_err_register_globals'] = 'Erreur : le paramètre PHP <b>register_globals</b> doit être off.';
$_lang['ms2_err_link_equal'] = 'Vous essayez d\'ajouter un lien de produit à lui-même';
$_lang['ms2_err_value_duplicate'] = 'Вы не ввели значение или ввели повтор.';

$_lang['ms2_err_gallery_save'] = 'Файл не был сохранён (см. системный журнал).';
$_lang['ms2_err_gallery_ns'] = 'Передан пустой файл';
$_lang['ms2_err_gallery_ext'] = 'Неверное расширение файла';
$_lang['ms2_err_gallery_exists'] = 'Такое изображение уже есть в галерее товара.';
$_lang['ms2_err_gallery_thumb'] = 'Не получилось сгенерировать превьюшки. Смотрите системный лог.';
$_lang['ms2_err_wrong_image'] = 'Файл не является корректным изображением.';

$_lang['ms2_email_subject_new_user'] = 'Vous avez passé la commande n°[[+num]] sur le site [[++site_name]]';
$_lang['ms2_email_subject_new_manager'] = 'Vous avez une nouvelle commande n°[[+num]]';
$_lang['ms2_email_subject_paid_user'] = 'Vous avez payé la commande n°[[+num]]';
$_lang['ms2_email_subject_paid_manager'] = 'La commande n°[[+num]] a été payée';
$_lang['ms2_email_subject_sent_user'] = 'Votre commande n°[[+num]] a été expédiée';
$_lang['ms2_email_subject_cancelled_user'] = 'Votre commande n°[[+num]] a été annulée';

$_lang['ms2_payment_link'] = 'Если вы случайно прервали процедуру оплаты, вы всегда можете <a href="[[+link]]" style="color:#348eda;">продолжить её по этой ссылке</a>.';

$_lang['ms2_category_err_ns'] = 'Категория не выбрана';
$_lang['ms2_option_err_ns'] = 'Свойство не выбрано';
$_lang['ms2_option_err_nf'] = 'Свойство не найдено';
$_lang['ms2_option_err_ae'] = 'Свойство уже существует';
$_lang['ms2_option_err_save'] = 'Ошибка сохранения свойства';
$_lang['ms2_option_err_reserved_key'] = 'Такой ключ опции использовать нельзя';
$_lang['ms2_option_err_invalid_key'] = 'Неправильный ключ для свойства';
