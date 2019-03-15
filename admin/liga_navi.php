<?php
@session_start() or die('Could not start session in '.__FILE__);

include '../inc/all.php';

$ligamanager=new ligamanager();
$liste=$ligamanager->alle_anzeigen();

foreach($liste as $liga)
{
   echo '<a href="javascript:ligaDetail.navigate(\'liga_detail.php?id='.$liga->ID.'\');">'.$liga->name.'</a><br />';
}

?>
<br />
<a href="javascript:ligaDetail.navigate('liga_detail.php');">Neue Liga</a>
