<?php

//Verhalten
define("ANZAHL_AKTUELL",10); //wie viele Termine werden bei 'aktuell' angezeigt


//Debug Konfiguration
error_reporting(E_ALL);
/* Disable ini set damit keine Warnungen im Produktionsbetrieb entstehen
ini_set("ignore_repeated_errors", "off");
ini_set("display_errors","on");
ini_set("log_errors","off");
ini_set("error_log","log.txt");
*/
//----------------------



//Programmkonstanten
define("AKTUELL",0);
define("ALLES",-1);

//Zeitkonstanten
define("UNIX_JAHR",31536000);
define("UNIX_TAG",86400);
define("UNIX_SCHALTJAHR", UNIX_JAHR+UNIX_TAG);

//Rechteflags, müssen 2er potenzen sein
define('STATUS_LOGIN',1);
define('STATUS_SPIELER',2);
define('STATUS_ADMIN',4);

//Termintypen
define('TYP_SPIEL',1);
define('TYP_TRAINING',2);
define('TYP_FREI',3);

//Besondere Monate
define('MONAT_AKTUELL','aktuell');
define('MONAT_EINLADUNG','eingeladen');
define('MONAT_EINLADUNG_UNGELESEN','eingeladen_ungelesen');

//keine gute Konstante. Besser dynamisch lesen.
define('FREUNDSCHAFTSSPIEL',1); //Diese Konstante muss mit der ID von Freundschaftsspiel in der Datenbank übereinstimmen.

//Zusagestatuus
define('ZUSAGE_KEINEANTWORT', 0);
define('ZUSAGE_JA',1);
define('ZUSAGE_VIELLEICHT',2);
define('ZUSAGE_NEIN',3);

//logindaten

define('MYSQL_HOST', 'localhost');
define('MYSQL_USER','db10603786-home');
define('MYSQL_PASSWORD', 'sahnetorte');
define('MYSQL_DATABASE', 'db10603786-phrasendrescher');

/*
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER','root');
define('MYSQL_PASSWORD', 'cosmicchaos');
define('MYSQL_DATABASE', 'phrasendrescher');
*/

//Timezone settings
date_default_timezone_set('Europe/Berlin');

//Monate ausgeschrieben
$MONAT=array('Kein g&uuml;ltiger Monat','Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
$MONAT_KURZ=array('Keing&uuml;ltiger Monat','Jan','Feb','Mrz','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez');
//Tage ausgeschrieben
$WOCHENTAG_KURZ=array('So','Mo','Di','Mi','Do','Fr','Sa');
?>
