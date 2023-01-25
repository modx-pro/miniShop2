<?php

/**
 * Properties Lexicon Entries
 * Sorted by key, alphabetically
 *
 * @package minishop2
 * @subpackage lexicon
 */

$_lang['ms2_prop_class'] = 'Numele clasei pentru eșantionare. Implicit, "msProduct".';
$_lang['ms2_prop_depth'] = 'Adâncimea de căutare a produselor de la fiecare părinte.';
$_lang['ms2_prop_fastMode'] = 'Dacă este activat – în chank ale rezultatului se vor afișa doar valorile din baza de date. Toate tagg-urile MODX neprelucrate , precum filtre, activarea snippet-elor – vor fi șterse. ';
$_lang['ms2_prop_filetype'] = 'Tipul fișierelor pentru eșantionare. Puteți utiliza "image" pentru a specifica imagini și extensii pentru alte fișiere. De exemplu: "image,pdf,xls,doc".';
$_lang['ms2_prop_groups'] = 'A afișa opțiuni numai pentru grupurile specificate (denumirea sau identificatorul categoriei, separate prin virgule, "0" înseamnă fără grupuri)';
$_lang['ms2_prop_hideEmpty'] = 'A nu afișa opțiuni cu valori goale.';
$_lang['ms2_prop_ignoreGroups'] = 'Grupurile ale căror opțiuni nu trebuie să fie afișate în listă, separate prin virgule.';
$_lang['ms2_prop_ignoreOptions'] = 'Opțiuni care nu trebuie să fie afișate în listă, separate prin virgule.';
$_lang['ms2_prop_includeContent'] = 'A alege cîmpul "content" al produsului .';
$_lang['ms2_prop_includeTVs'] = 'Lista parametrilor TV pentru eșantionare, separate prin virgule. De exemplu: " action,time " vor da placeholderi [[+action]] și [[+time]].';
$_lang['ms2_prop_includeThumbs'] = 'Lista dimensiunilor miniaturilor pentru eșantionare, separate prin virgule. De exemplu "small, medium" vor da placeholderi  [[+small]] și [[+medium]]. Imaginile trebuie generate dinainte în galeria de produse.';
$_lang['ms2_prop_limit'] = 'Limita rezultatelor eșantioanelor';
$_lang['ms2_prop_link'] = 'ID a legăturii produselor, care se atribuie automat la crearea unei noi legături în setări.';
$_lang['ms2_prop_master'] = 'ID al produsului principal. Dacă sunt specificate atât "master", cât și "slave" - eșantionarea va fi executată utilizînd "master".';
$_lang['ms2_prop_offset'] = 'Trecerea peste rezultatele de la începutul eșantionului';
$_lang['ms2_prop_onlyOptions'] = 'A afișa numai această listă de opțiuni, separate prin virgule.';
$_lang['ms2_prop_optionFilters'] = 'Filtre după opțiunile produselor. Se transmit de șirul JSON, de exemplu, {"optionkey:>":10}';
$_lang['ms2_prop_optionName'] = 'Denumirea opțiunii pentru afișare.';
$_lang['ms2_prop_optionSelected'] = 'Denumirea opțiunii active pentru a pune atributul "selected"';
$_lang['ms2_prop_options'] = 'Lista de opțiuni pentru afișare, separate prin virgule.';
$_lang['ms2_prop_outputSeparator'] = 'Șir opțional pentru divizarea rezultatelor lucrului.';
$_lang['ms2_prop_parents'] = 'Lista categoriilor, separate prin virgulă, pentru rezultatele căutării. În mod implicit, eșantionarea este limitată la părintele curent. Dacă puneți 0 - eșantionul nu este limitat.';
$_lang['ms2_prop_product'] = 'ID-ul produsului. Dacă nu este specificat, se utilizează ID-ul documentului curent.';
$_lang['ms2_prop_resources'] = 'Lista produselor, separate prin virgulă, pentru afișarea în rezultate. Dacă ID produsului se începe cu minus, atunci acest produs se exclude din eșantionare.';
$_lang['ms2_prop_return'] = 'Metoda de afișare a rezultatelor';
$_lang['ms2_prop_returnIds'] = 'Returnarea cîmpului cu ID-ul produsului, în loc de chank-uri.';
$_lang['ms2_prop_showDeleted'] = 'A arăta produsele șterse.';
$_lang['ms2_prop_showHidden'] = 'A afișa produsele ascunse în meniu.';
$_lang['ms2_prop_showLog'] = 'A afișa informație suplimentară despre funcționarea snippet-ului. Numai pentru useri autorizați în context "mgr".';
$_lang['ms2_prop_showUnpublished'] = 'A afișa produsele nepublicate.';
$_lang['ms2_prop_showZeroPrice'] = 'A afișa produsele cu preț zero.';
$_lang['ms2_prop_slave'] = 'ID produsului subordonat. Dacă este indicat "master" - această opțiune este ignorată.';
$_lang['ms2_prop_sortGroups'] = 'Specifică ordinea de sortare a grupurilor de opțiuni. Acceptă atât nume de identificare, cât și nume de text ale grupurilor. Transmisă sub forma unui șir de caractere, de exemplu: "22,23,24" sau "Dimensiuni,Electronice,Diverse".';
$_lang['ms2_prop_sortOptionValues'] = 'Specifică ordinea în care sunt sortate valorile opțiunilor. Transmisă sub forma unui șir de caractere, de exemplu: "size:SORT_DESC:SORT_NUMERIC:100,color:SORT_ASC:SORT_STRING"';
$_lang['ms2_prop_sortOptions'] = 'Specifică ordinea în care sunt sortate opțiunile. Transmisă sub forma unui șir de caractere, de exemplu: "dimensiune, culoare".';
$_lang['ms2_prop_sortby'] = 'Sortarea eșantionului. Pentru a sorta după câmpurile de produse, trebuie să adăugați prefixul "Data.", de exemplu: "&sortby=`Data.price`"';
$_lang['ms2_prop_sortbyOptions'] = 'Specificați ce opțiuni și cum să sortați printre cele enumerate în & sortby. Se transmit în rind, de exemplu, "optionkey:integer,optionkey2:datetime"';
$_lang['ms2_prop_sortdir'] = 'Direcția de sortare';
$_lang['ms2_prop_toPlaceholder'] = 'Dacă nu este gol, snippetul va salva toate datele în placeholder cu acest nume, în loc de afișare pe ecran';
$_lang['ms2_prop_toSeparatePlaceholders'] = 'Dacă veți introduce cuvînt în acest parametru, atunci TOATE rezultatele vor fi plasate în placeholderi diferiți, începând cu acest cuvânt și terminând cu numărul de ordine a rîndului, de la 0. De exemplu al liniei, pornind de la zero. De exemplu, indicînd în parametru "myPl", veți primi placeholderi [[+myPl0]], [[+myPl1]] etc. ';
$_lang['ms2_prop_tpl'] = 'Chunk-ul părții vizuale pentru fiecare rezultat ';
$_lang['ms2_prop_tplDeliveriesOuter'] = 'Chank pentru metode de livrare posibile';
$_lang['ms2_prop_tplDeliveriesRow'] = 'Chank pentru o metodă de livrare.';
$_lang['ms2_prop_tplEmpty'] = 'Chank-ul care apare dacă nu este nici-un rezultat.';
$_lang['ms2_prop_tplOuter'] = 'Wrapper pentru afișarea rezultatelor snippet-ului.';
$_lang['ms2_prop_tplPaymentsOuter'] = 'Chank-ul pentru blocul metodelor de achitare posibile .';
$_lang['ms2_prop_tplPaymentsRow'] = 'Chank-ul pentru o metodă de achitare';
$_lang['ms2_prop_tplRow'] = 'Chank-ul părții vizuale a unui element din eșantion.';
$_lang['ms2_prop_tplSingle'] = 'Chank-ul părții vizuale a unicului element din eșantion.';
$_lang['ms2_prop_tplSuccess'] = 'Chank-ul cu mesaj despre funcționarea cu succes a snippet-ului.';
$_lang['ms2_prop_tplValue'] = 'Șablon pentru o valoare (numai pentru opțiuni multiple)';
$_lang['ms2_prop_tvPrefix'] = 'Frefix pentru TV placeholderi, de exemplu "tv.". Implicit acest parametru este gol.';
$_lang['ms2_prop_userFields'] = 'Array asociativ corespunderii cîmpurilor comenzii cu cîmpurile profilului în format "cîmpul comenzii" => "cîmpul profilului"';
$_lang['ms2_prop_valuesSeparator'] = 'Separator pentru valori a opțiunilor multiple';
$_lang['ms2_prop_where'] = 'Șir codificat în JSON cu condiții suplimentare de eșantionare.';
$_lang['ms2_prop_wrapIfEmpty'] = 'Activează afișarea wrapper-ului chank-ului (tplWrapper) chiar dacă nu sunt rezultate.';
