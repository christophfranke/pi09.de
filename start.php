<?php
@session_start() or die('Could not start session in '.__FILE__);

include 'inc/all.php';
$loginmanager=new loginmanager();
if (!$loginmanager->login_status())
{
?>
<div id="startlinks" class="transparent">
<!--
<h3>Willkommen bei den Phrasendreschern!</h3>
<br />
<p>Um diese Seite gut benutzen zu k&ouml;nnen, solltest du dir ein Benutzerkonto erstellen.
</p>
-->
<h3>Willkommen bei den Phrasendreschern! </h3>
<br />
<p>Wenn du Spieler der Phrasendrescher bist, erstelle dir einen Account, um alle Funktionen nutzen zu können.<br /><br /></p>

<p>Wenn du Fan bist, schau' dich gerne bei uns um. Wir sind ein Freizeitfußballteam aus Köln, das 2009 gegründet wurde und seit 2011 die <a href="http://www.2cl.org" target="_blank"><u>Cologne Champions League</u></a> mit technisch
 erstklassigem Fußball verzückt.<br />
Falls du das mal live erleben möchtest, solltest du unsere nächsten Termine auschecken.</p>

</div>
<div id="startrechts" class="transparent">
<?php
$action='javascript:loginStart.send();';
$loginform_id='loginstartform';
include 'formulare/loginform.php';
?>
<input type="button" onClick="registerPopup();" value="jetzt registrieren!" />
</div>
<?php
}
else
{
?>
<div id="startlinks" class="transparent">
<p>
Willkommen <?php echo $loginmanager->name;?>!
<br />Hier kannst du die <b>Einstellungen</b> von deinem Benutzerkonto &auml;ndern.
</p>
<br />
<div class="transparent">
<?php
$submit="Daten &auml;ndern";
$action='javascript:userBearbeiten.send();';
$mit_status=false;
$mit_passwort=false;
if ($loginmanager->login_status())
{
   $account=$loginmanager->get_user();
   include 'formulare/userform_autofill.php';

   echo '</div><br /><div class="transparent">';
   $action="javascript:passwortBearbeiten.send();";
   $submit="Passwort &auml;ndern";
   include 'formulare/passwortform.php';
}
else
   include 'formulare/userform.php';
?>
</div>
</div>
<div id="startrechts" class="transparent">
<p>Hier kannst du deine <b>Einladungen</b> sehen.</p><br />
<?php
$einladungsmanager=new einladungsmanager($loginmanager);
$einladungsmanager->vergangene_anzeigen=false;
$user=new account();
$user->ID=$loginmanager->ID;
$liste=$einladungsmanager->einladungen_zum_account($user);
echo '<div class="transparent"><table width="100%">';
$offene_einladungen=false;
foreach($liste as $termin)
{
   if ($termin->zusageID!=ZUSAGE_KEINEANTWORT)
      continue;

   $offene_einladungen=true;
   echo '<tr onClick="zusagePopup('.$termin->ID.');"><td><img src="img/unbeantwortet_small.png" /></td><td>';
   switch($termin->typ)
   {
      case TYP_SPIEL:
         echo '<img src="img/spiel_small.png" /></td><td>vs '.$termin->gegner;
         break;
      case TYP_FREI:
         echo '<img src="img/freier_small.png" /></td><td>'.$termin->header;
         break;
      case TYP_TRAINING:
         echo '<img src="img/training_small.png" /></td><td>Training';
         break;
   }
   echo '</td><td>'.$WOCHENTAG_KURZ[date('w',$termin->zeit)].date(' d.m H:i',$termin->zeit).'</td></tr>';
}
echo '</table>';
if (!$offene_einladungen)
   echo 'Keine offenen Einladungen';
echo '<br /><hr /><br />';
echo '<table width="100%">';
foreach($liste as $termin)
{
   if ($termin->zusageID==ZUSAGE_KEINEANTWORT)
      continue;

   echo '<tr onClick="zusagePopup('.$termin->ID.');"><td>';
   switch($termin->zusageID)
   {
      case ZUSAGE_JA:
         echo '<img src="img/ja_small.png" /></td><td>';
         break;
      case ZUSAGE_NEIN:
         echo '<img src="img/nein_small.png" /></td><td>';
         break;
      case ZUSAGE_VIELLEICHT:
         echo '<img src="img/vielleicht_small.png" /></td><td>';
         break;
   }
   switch($termin->typ)
   {
      case TYP_SPIEL:
         echo '<img src="img/spiel_small.png" /></td><td>vs '.$termin->gegner;
         break;
      case TYP_FREI:
         echo '<img src="img/freier_small.png" /></td><td>'.$termin->header;
         break;
      case TYP_TRAINING:
         echo '<img src="img/training_small.png" /></td><td>Training';
         break;
   }
   echo '</td><td>'.$WOCHENTAG_KURZ[date('w',$termin->zeit)].date(' d.m H:i',$termin->zeit).'</td></tr>';
}
echo '</table></div>';
?>

<?php
}
?>