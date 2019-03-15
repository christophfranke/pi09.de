<?php
@session_start() or die('Could not start session in '.__FILE__);

include '../inc/all.php';


if(empty($_GET['id']))
{
}
else
{
   $id=$_GET['id'];
   $einladungsmanager=new einladungsmanager();
   $einladungsliste=$einladungsmanager->anzeigen($id);
?>
<br /><input type="button" value="Spieler einladen" onClick="spielerEinladen(<?php echo $id;?>);" />
<br /><input type="button" value="Alle einladen" onClick="alleEinladen(<?php echo $id;?>);" />
<br /><input type="button" value="Einzelne Person einladen" />
<br /><input type="button" value="Alle ausladen" onClick="alleAusladen(<?php echo $id;?>);" />
<h3>Eingeladen sind:</h3>
<?php
   if ($einladungsliste->size()>0)
   {
      echo '<table>';
      foreach($einladungsliste as $einladung)
      {
         echo '<tr><td><input type="button" value="ausladen" onClick="this.disabled=true;benutzerAusladen('.$einladung->ID.');" /></td>';
         echo '<td><a href="javascript:zusagePopup('.$einladung->ID.',true);">'.$einladung->name().'</a></td><td>';
         switch($einladung->zusageID)
         {
            case ZUSAGE_JA:
               echo '<img src="../img/ja_small.png" class="einladungsbildchen"/>';
               break;
            case ZUSAGE_NEIN:
               echo '<img src="../img/nein_small.png" class="einladungsbildchen"/>';
               break;
            case ZUSAGE_VIELLEICHT:
               echo '<img src="../img/vielleicht_small.png" class="einladungsbildchen"/>';
               break;
            case ZUSAGE_KEINEANTWORT:
               echo '<img src="../img/unbeantwortet_small.png" class="einladungsbildchen"/>';
               break;
         }
         echo '</td></tr>';
      }
      echo '</table>';
   }
   else
      echo '<br />Zu diesem Termin ist zur Zeit niemand eingeladen';
}
?>
