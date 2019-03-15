<?php
@session_start() or die('Could not start session in '.__FILE__);

include '../inc/all.php';

$loginmanager=new loginmanager();
$terminmanager=new terminmanager($loginmanager);
$kommentarmanager=new kommentarmanager($loginmanager);
$einladungsmanager=new einladungsmanager($loginmanager);
$ligamanager=new ligamanager($loginmanager);
$ligaliste=$ligamanager->alle_anzeigen();
@$id=$_GET['id'];

if (isset($id))
{
   $termin=$terminmanager->anzeigen($id);
   if ($termin!==false)
   {
      @$zeit=new zeit();
      $zeit->set($termin->zeit);
   }
   else
      $zeit=false;

   $meldung=$terminmanager->meldung();

   //termin_form.php im Bearbeiten Modus
   $action='javascript:bearbeiten.send();';
   $submit='speichern';
   
   echo '<h3>'.$termin->beschreibung().'</h3>';
}
else
{
   $termin=false;
   $zeit=false;
      
   echo '<p>Bitte w&auml;hle oben einen Termin aus der Liste</p>';
}

//Kommentieren und löschen nur zulassen, wenn ein entsprechender Termin existiert
if ($termin!==false)
{
   include 'ergebnis_form.php';
?>
<form action="javascript:" method="post" id="loeschform">
 <input type="hidden" name="ID" value="<?php if (isset($termin->ID)) echo $termin->ID;?>" />
 <input type="button" value="Termin l&ouml;schen" onClick="terminLoeschen();" />
</form>
<div id="kommentare"></div>
<form action="javascript:document.getElementById('kommenterenbutton').disabled=true;kommentieren.send()" id="kommentarform">
<input type="hidden" name="bezugID" value="<?php if (isset($termin->ID)) echo $termin->ID;?>" />
<input type="hidden" name="bezugstabelle" value="termin" />
<textarea name="inhalt"></textarea>
<br /><input type="submit" id="kommentierenbutton" value="kommentieren" />
</form>
<div id="einladungen"></div>
<?php
}
?>
<!--  Variablen setzen für inputVars  -->
<form id="sendVars">
 <input type="hidden" name="bearbeiten" value="<?php if ($termin===false) echo 'false'; else echo 'true';?>" />
 <input type="hidden" name="id" value="<?php if (isset($termin->ID)) echo $termin->ID;?>" />
 <input type="hidden" name="terminTyp" value="<?php if (isset($termin->typ)) echo $termin->typ;?>" />
 <input type="hidden" name="spielTyp" value="<?php if (isset($termin->liga)) echo $termin->liga;?>" />
</form>
