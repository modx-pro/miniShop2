<?php
/**
 * Manager German Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */
$_lang['ms2_menu_create'] = 'Anlegen';
$_lang['ms2_menu_update'] = 'Bearbeiten';
$_lang['ms2_menu_remove'] = 'Entfernen';
$_lang['ms2_menu_remove_multiple'] = 'Ausgewählte entfernen';
$_lang['ms2_menu_remove_confirm'] = 'Diesen Eintrag entfernen?';
$_lang['ms2_menu_remove_multiple_confirm'] = 'Alle ausgewählten Einträge entfernen?';

$_lang['ms2_combo_select'] = 'Für Auswahl klicken';
$_lang['ms2_combo_select_status'] = 'Nach Status sortieren';

$_lang['ms2_id'] = 'ID';
$_lang['ms2_name'] = 'Name';
$_lang['ms2_color'] = 'Farbe';
$_lang['ms2_country'] = 'Land';
$_lang['ms2_logo'] = 'Logo';
$_lang['ms2_address'] = 'Adresse';
$_lang['ms2_phone'] = 'Telefon';
$_lang['ms2_fax'] = 'Fax';
$_lang['ms2_email'] = 'e-Mail';
$_lang['ms2_active'] = 'Aktiv';
$_lang['ms2_class'] = 'Handler class';
$_lang['ms2_description'] = 'Beschreibung';
$_lang['ms2_num'] = 'Nummer';
$_lang['ms2_status'] = 'Status';
$_lang['ms2_count'] = 'Anzahl';
$_lang['ms2_cost'] = 'Preis';
$_lang['ms2_order_cost'] = 'Bestellpreis';
$_lang['ms2_cart_cost'] = 'Gesamtpreis der Produkte';
$_lang['ms2_delivery_cost'] = 'Lieferungspreis';
$_lang['ms2_weight'] = 'Gewicht';
$_lang['ms2_createdon'] = 'Erstellt am';
$_lang['ms2_updatedon'] = 'Bearbeitet am';
$_lang['ms2_user'] = 'Benutzer';
$_lang['ms2_timestamp'] = 'Zeitpunkt';
$_lang['ms2_order_log'] = 'Bestellprotokoll';
$_lang['ms2_order_products'] = 'Produkte';
$_lang['ms2_action'] = 'Aktion';
$_lang['ms2_entry'] = 'Eintrag';
$_lang['ms2_username'] = 'Benutzername';
$_lang['ms2_fullname'] = 'Vollständiger Name';
$_lang['ms2_resource'] = 'Resource';

$_lang['ms2_receiver'] = 'Empfänger';
$_lang['ms2_index'] = 'PLZ';
$_lang['ms2_region'] = 'Bundesland';
$_lang['ms2_city'] = 'Stadt';
$_lang['ms2_metro'] = 'Metro';
$_lang['ms2_street'] = 'Straße';
$_lang['ms2_building'] = 'Gebäude';
$_lang['ms2_room'] = 'Raum';
$_lang['ms2_comment'] = 'Kommentar';

$_lang['ms2_email_user'] = 'e-Mail Kunde';
$_lang['ms2_email_manager'] = 'e-Mail Manager';
$_lang['ms2_subject_user'] = 'Betreff der e-Mail an Kunde';
$_lang['ms2_subject_manager'] = 'Betreff der e-Mail an Manager';
$_lang['ms2_body_user'] = 'Chunk für e-Mail an Kunde';
$_lang['ms2_body_manager'] = 'Chunk für e-Mail an Manager';
$_lang['ms2_status_final'] = 'Final';
$_lang['ms2_status_final_help'] = '';
$_lang['ms2_status_fixed'] = 'Fixed';
$_lang['ms2_status_fixed_help'] = '';
$_lang['ms2_options'] = 'Optionen';
$_lang['ms2_price'] = 'Preis';
$_lang['ms2_price_help'] = 'Basislieferpreis';
$_lang['ms2_weight_price'] = 'Preis für 1 St./kg';
$_lang['ms2_weight_price_help'] = 'Zusätzliche Kosten pro Gewichtseinheit.<br/>Kann in benutzerdefinierten Klassen verwendet werden.';
$_lang['ms2_distance_price'] = 'Preis für 1 St./Entfernung';
$_lang['ms2_distance_price_help'] = 'Zusätzliche Kosten pro Entfernungseinheit<br/>Kann in benutzerdefinierten Klassen verwendet werden.';
$_lang['ms2_order_requires'] = 'Pflichtfelder';
$_lang['ms2_order_requires_help'] = 'Bei der Bestellung ist das Ausfüllen dieser Felder Pflicht';

$_lang['ms2_orders_selected_status'] = 'Status geändert';

$_lang['ms2_link_name'] = 'Linkname';
$_lang['ms2_link_one_to_one'] = 'Eins zu Eins';
$_lang['ms2_link_one_to_one_desc'] = 'Equal union of two goods. If you want to connect more than 2 product, you need to use the "many-to-many".';
$_lang['ms2_link_one_to_many'] = 'One to many';
$_lang['ms2_link_one_to_many_desc'] = 'The connection of the master of the goods with slaves. For example, the product is a set of other goods. Well suited for the specifying recommended goods.';
$_lang['ms2_link_many_to_one'] = 'Many to one';
$_lang['ms2_link_many_to_one_desc'] = 'Link slaves with the master and slaves has no connection with each other. For example, goods are included in a set.';
$_lang['ms2_link_many_to_many'] = 'Many to many';
$_lang['ms2_link_many_to_many_desc'] = 'Equal union of many goods. All the goods of the group are connected with each other and with the addition of a new connection to one product, all other will have the same. Typical applications: link by one parameter, such as color, size, language, version, etc.';
$_lang['ms2_link_master'] = 'Eltern-Produkt';
$_lang['ms2_link_slave'] = 'Kind-Produkt';