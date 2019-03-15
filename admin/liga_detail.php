<?php
@session_start() or die('Could not start session in '.__FILE__);

if (isset($_GET['id']))
{
   $id=$_GET['id'];
   include '../inc/all.php';
   
   $ligamanager=new ligamanager();
   $liga=$ligamanager->anzeigen($id);
   
   $action="javascript:ligaBearbeiten.send();";
   $submit='speichern';
   $mit_loeschen=true;
   
   echo "<h3>$liga->name</h3>";
}
else
{
   $action="javascript:ligaAnlegen.send();";
   $submit='anlegen';
   $mit_loeschen=false;
   echo '<h3>Neue Liga anlegen</h3>';
}

include '../formulare/ligaform.php';

?>
