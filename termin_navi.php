<?php
@session_start() or die('Could not start session in '.__FILE__);

include 'inc/all.php';

if(empty($_GET['monat']))
   $monat='aktuell';
else
   $monat=$_GET['monat'];
$terminmanager=new terminmanager();
$terminmanager->vergangene_anzeigen=false;
$terminliste=$terminmanager->monat_anzeigen($monat);

if ($terminliste===false)
{
   echo $terminmanager->meldung();
}
else if ($terminliste->size()==0)
{
   echo "Keine Termine.";
}
else
{
   echo '<table>';
   foreach($terminliste as $termin)
   {
      echo '<tr>';
      if ($termin->typ==TYP_SPIEL)
      {
         echo '<td><img src="img/spiel_small.png" /></td>'.
         '<td><a href="javascript:termindetailFrame.navigate(\'termin_detail.php?id='.$termin->ID.'\');">'.
         'vs '.$termin->gegner.'</a></td><td>'.$WOCHENTAG_KURZ[date('w',$termin->zeit)].date(' d.m H:i',$termin->zeit).'</td>';
      }
      if ($termin->typ==TYP_TRAINING)
      {
         echo '<td><img src="img/training_small.png" /></td>'.
         '<td><a href="javascript:termindetailFrame.navigate(\'termin_detail.php?id='.$termin->ID.'\');">'.
         'Training</a></td><td>'.$WOCHENTAG_KURZ[date('w',$termin->zeit)].date(' d.m H:i',$termin->zeit).'</td>';
      }
      if ($termin->typ==TYP_FREI)
      {
         echo '<td><img src="img/freier_small.png" /></td>'.
         '<td><a href="javascript:termindetailFrame.navigate(\'termin_detail.php?id='.$termin->ID.'\');">'.
         $termin->header.'</a></td><td>'.$WOCHENTAG_KURZ[date('w',$termin->zeit)].date(' d.m H:i',$termin->zeit).'</td>';
      }
      echo '</tr>';
   }
}

?>
