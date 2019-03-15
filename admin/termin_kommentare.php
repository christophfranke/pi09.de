<?php
@session_start() or die('Could not start session in '.__FILE__);

include '../inc/all.php';

if (empty($_GET['id']))
{
}
else
{
   $kommentarmanager=new kommentarmanager();
   $id=$_GET['id'];
   $kommentarliste=$kommentarmanager->anzeigen($id,'termin');

?>


<h3>Kommentare</h3>
<?php

   if ($kommentarliste->size()==0)
    echo 'Keine Kommentare.';
   else
      foreach($kommentarliste as $kommentar)
         echo '<br /><input type="button" onClick="javascript:kommentarLoeschen('.$kommentar->ID.');" value="Kommentar l&ouml;schen" />'.
              " <b>".$kommentar->name()."</b> schrieb am ".date('d. m.',$kommentar->zeit)." um ".date('H:i',$kommentar->zeit).
              "<p>$kommentar->inhalt</p>";
}
?>
