<?php
@session_start() or die('Could not start session in '.__FILE__);

include 'inc/all.php';

$loginmanager=new loginmanager();
$terminmanager=new terminmanager($loginmanager);
$kommentarmanager=new kommentarmanager($loginmanager);

if (empty($_GET['id']))
{
   echo '<h3>Details</h3>';
   echo 'Kein Termin ausgew&auml;hlt';
}
else
{
   $id=$_GET['id'];
   $termin=$terminmanager->anzeigen($id);
   if ($termin===false)
      echo $terminmanager->meldung();
   else
   {
      $kommentarliste=$kommentarmanager->anzeigen($termin->ID,'termin');
?>
<!-- html ausgabe ab hier -->
<h3>Details</h3>

<div id="termindetail_alles">
<!-- hier beginnt ein reativ kompliziertes verschachteltes div system -->
<div class="transparent" id="termindetail_oberbalken"></div>

<div id="termindetail_rechts">
<div class="transparent" id="termindetail_balkenrechts"></div>
<div id="termindetail_teilnehmer">
</div><!--teilnehmer zu -->
</div><!--rechts zu -->

<div id="termindetail_links">

<div class="transparent" id="termindetail_beschreibung">
<br />
<?php
if ($termin->typ==TYP_SPIEL)
{
   if ($termin->liga==FREUNDSCHAFTSSPIEL)
      echo "Freundschaftsspiel<br />";
   else
   {
      $ligamanager=new ligamanager($loginmanager);
      $liga=$ligamanager->anzeigen($termin->liga);
      echo "$liga->name Ligaspiel<br />";
   }
   echo "Phrasendrescher gegen $termin->gegner <br />";
}
if ($termin->typ==TYP_TRAINING)
   echo 'Training<br />';
if ($termin->typ==TYP_FREI)
   echo "$termin->header <br />";

echo "$termin->ort <br />";
echo $WOCHENTAG_KURZ[date('w',$termin->zeit)].date(' d.m H:i',$termin->zeit).'<br />';
echo "<br />".format_text($termin->inhalt)." <br /><br />";  
?>
</div><!-- beschreibung zu -->

<div class="transparent" id="termindetail_kommentarbalkenlinks">
</div>
<div class="transparent" id="termindetail_kommentarbalkenrechts">
</div>
<form action="javascript:kommentierenFkt();" id="kommentarform">
<input type="hidden" name="bezugID" value="<?php echo $termin->ID;?>" />
<input type="hidden" name="bezugstabelle" value="termin" />
<textarea name="inhalt"></textarea>
<div class="transparent" id="termindetail_kommentarbutton">
<br /><input type="submit" value="kommentieren" id="kommentierbutton" />
</div>
</form>
<div class="transparent" id="termindetail_kommentare">

<?php
   if ($kommentarliste->size()>0)
      foreach($kommentarliste as $kommentar)
      {
         echo '<br />';
         if ($loginmanager->is_admin() or $loginmanager->ID==$kommentar->accountID)
            echo '<form id="kommentarloeschen'.$kommentar->ID.'" action="javascript:kommentarLoeschenFkt('.$kommentar->ID.');">
                  <input type="hidden" name="ID" value="'.$kommentar->ID.'" />
                  <input type="submit" value="Kommentar l&ouml;schen" /></form> ';
         echo '<b>'.$kommentar->name().'</b> am '.date('d. m.',$kommentar->zeit).' um '.date('H:i',$kommentar->zeit);
         echo '<p>'.format_text($kommentar->inhalt).'</p>';
      }
?>
</div><!--kommentare zu -->
</div><!--links zu -->
<div class="transparent" id="termindetail_unterbalken"></div>
<!-- Variable senden -->
<form id="sendVars">
<input type="hidden" name="id" value="<?php echo $id;?>" />
</form>
<?php
   }
}
?>
</div>
