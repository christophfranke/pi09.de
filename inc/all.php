<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

require_once('const.php');
require_once('entity.php');
require_once('fct.php');
require_once('db.php');
require_once('loginmanager.php');
require_once('usermanager.php');
require_once('terminmanager.php');
require_once('ergebnismanager.php');
require_once('kommentarmanager.php');
require_once('einladungsmanager.php');
require_once('ligamanager.php');
require_once('user_entities.php');

?>
