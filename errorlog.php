<?php
session_start();
date_default_timezone_set('Europe/Berlin');

if (isset($_GET['msg']))
   $msg=$_GET['msg'];
else
   $msg='Keine Feherbeschreibung verfügbar';

//Keine Zeienumbrüche in Fehermeldungen
$msg=str_replace("\n",' ',$msg);

//Fehlerbeschreibung mit Zeit versehen
$errorstring=date('d.m H:i ',time());

//Fehlerbeschreibung mit Browserinformationen versehen
$errorstring.=$_SERVER['HTTP_USER_AGENT'];

//Username hinzufügen
if (isset($_SESSION['name']))
   $username=$_SESSION['name'];
else
   $username='Unbekannt';
$errorstring.=" by $username.\n$msg\n";

//Fehlerbeschreibung in js_error.log anhängen
file_put_contents('js_error.log', "$errorstring\n", FILE_APPEND);
?>
