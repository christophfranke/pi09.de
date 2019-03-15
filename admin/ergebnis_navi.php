<?php
@session_start() or die('Could not start session in '._FILE__);

include '../inc/all.php';

$loginmanager=new loginmanager();
$terminmanager=new terminmanager($loginmanager);
$terminmanager->vergangene_anzeigen=true;
$terminmanager->zukuenftige_anzeigen=false;
$terminliste=$terminmanager->alle_anzeigen();
echo '<h3>&Uuml;bersicht Ergebnisse</h3>';
foreach($terminliste as $termin)
{
   echo '<a href="javascript:ergebnisDetail.navigate(\'ergebnis_detail.php?id='.$termin->ID.'\');">'.$termin->beschreibung().'</a><br />';
}
?>
<br />
