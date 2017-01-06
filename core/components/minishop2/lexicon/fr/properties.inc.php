<?php
/**
 * Properties French Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */
$_lang['ms2_prop_limit'] = 'Limter les résultats a ce nombre.';
$_lang['ms2_prop_offset'] = 'Décalage dans les résultats retournés';
$_lang['ms2_prop_depth'] = 'Valeur entière indiquant la profondeur de la recherche à partir de chaque parent.';
$_lang['ms2_prop_sortby'] = 'Champ de tri. Pour trier les articles sur un champ spécifique vous devez ajouter le préfix "Data.", par exemple : "&sortby=`Data.price`"';
$_lang['ms2_prop_sortdir'] = 'Direction du tri';
$_lang['ms2_prop_where'] = 'Une expression dans un style JSON pour construire une clause "where" additionnelle.';
$_lang['ms2_prop_tpl'] = 'Le "chunk" Tpl utilisé pour chaque ligne.';
$_lang['ms2_prop_toPlaceholder'] = 'Si non vide, le "snippet" sauvegardera sa sortie dans l\'emplacement de ce nom au lieu de le retourner à l\'écran.';
$_lang['ms2_prop_showLog'] = 'Afficher les information additionnelle à propos du travail de ce "snippet". Seulement si authentifié dans le context "mgr".';
$_lang['ms2_prop_parents'] = 'Liste des conteneurs séparée par une virgule pour chaque résultats. Cette requête est par défaut limitée au parent courrant. Si 0, elle ne sera plus limitée.';
$_lang['ms2_prop_resources'] = 'Liste des ids a inclure dans le résultat. Préfixer un id par un tiret va l\'exclure du résultat.';
$_lang['ms2_prop_fastMode'] = 'Si activé, alors le "chunk" recevera seulement les valeurs la base de données. Toutes les balises de MODX, tel que les filtres, appels de "snippets" seront supprimés.';
$_lang['ms2_prop_includeContent'] = 'Récupérer le champ "contenu" des articles.';
$_lang['ms2_prop_where'] = 'Une expression JSON pour construire les critères supplémentaires de la clause "where".';
$_lang['ms2_prop_includeTVs'] = 'Une liste optionnelle, séparée par des virgules, de nom de "TemplateVar" a inclure dans la sélection.';
$_lang['ms2_prop_includeThumbs'] = 'Une liste optionnelle, séparée par des virgules, de taille de vignette à inclure dans la sélection. Par exemple : "small,medium" vous donne les espaces réservés [[+small]] et [[+medium]]. Les vignettes doivent être générée dans la gallerie des articles.';
$_lang['ms2_prop_link'] = 'ID du lien des marchandises, qui est automatiquement attribué quand vous créez un nouveau lien dans les paramètres.';
$_lang['ms2_prop_master'] = 'ID de l\'article maître. Si spécifié il est à la fois "maître" et "esclave" - La requête sera construite pour le maître.';
$_lang['ms2_prop_slave'] = 'ID de l\'article esclave. Si "maître" est spécifié, cette option sera ignorée.';
$_lang['ms2_prop_class'] = 'Nom de la classe pour la sélection. Par défaut, "msProduct".';
$_lang['ms2_prop_tvPrefix'] = 'Préfix pour le propriétés "TemplateVar", "tv.", par exemple. Vide par défaut.';
$_lang['ms2_prop_outputSeparator'] = 'Chaine de caratères optionnelle séparant chaque instance de modèle.';

$_lang['ms2_prop_showUnpublished'] = 'Afficher les articles non publiés.';
$_lang['ms2_prop_showDeleted'] = 'Afficher les article supprimés.';
$_lang['ms2_prop_showHidden'] = 'Afficher les articles cachés dans le menu.';
$_lang['ms2_prop_showZeroPrice'] = 'Afficher les articles avec un prix à zéro.';

$_lang['ms2_prop_tplRow'] = '"Chunk" pour le modèle  d\'une ligne de requête.';
$_lang['ms2_prop_tplOuter'] = 'Modèle d\'enveloppe pour les résultats du travail d\'un "snippet".';
$_lang['ms2_prop_tplEmpty'] = '"Chunk" renvoyé quand il n\'y a aucun résultat.';
$_lang['ms2_prop_tplSuccess'] = '"Chunk" d\'un message de succés à propos du travail d\'un "snippet".';
$_lang['ms2_prop_tplPaymentsOuter'] = '"Chunk" pour les modèles de bloc de mode de paiement possible.';
$_lang['ms2_prop_tplPaymentsRow'] = '"Chunk" pour traiter une méthode de paiement.';
$_lang['ms2_prop_tplDeliveriesOuter'] = '"Chunk" pour le modèle de mode de livraison possible.';
$_lang['ms2_prop_tplDeliveriesRow'] = '"Chunk" pour traiter un mode de livraison.';

$_lang['ms2_prop_product'] = 'ID de l\'article. Si vide, l\'ID du document courant sera utilisé.';
$_lang['ms2_prop_optionSelected'] = 'Nom de l\'option activé pour définir l\'attribut de paramètrage "sélectionné".';
$_lang['ms2_prop_optionName'] = 'Nom d\'affichage de l\'option.';
$_lang['ms2_prop_filetype'] = 'Type de fichier pour l\'échantillonnage. Vous pouvez utiliser "image" pour spécifier les images et les extensions pour les fichiers restants. Par exemple: "image,pdf,xls,doc".';