<?php
@session_start() or die('Could not start session in '.__FILE__);
include 'inc/all.php';

//das älteste Jahr, das in der Ergebnisliste angezeigt wird
define('LAUNCH_YEAR', 2011);

$terminmanager=new terminmanager();
$terminmanager->vergangene_anzeigen=true;
$terminmanager->zukuenftige_anzeigen=false;
$terminmanager->nur_sichtbar_anzeigen=true;
$terminmanager->reihenfolge='DESC';
if ($terminmanager->monat_exist(MONAT_AKTUELL))
   $class0='highlight';
else
   $class0='normal';
if ($terminmanager->monat_exist(MONAT_EINLADUNG))
   $class1='highlight';
else
   $class1='normal';
?>

<a href="javascript:ergebnisnaviFrame.navigate('ergebnis_navi.php?monat=aktuell');" class="<?php echo $class0;?>">letzen 10</a>
<br />

<div id="einladungenlink">
<a href="javascript:ergebnisnaviFrame.navigate('ergebnis_navi.php?monat=eingeladen');" class="<?php echo $class1;?>">eingeladen</a><br />
</div>

<div id="monatelinks">
<?php

function print_monat($i)
{
   global $terminmanager;
   global $MONAT_KURZ;
   
   $monat_index = $i % 12 + 1;
   while ($monat_index <= 0)
      $monat_index += 12;
   
   if ($terminmanager->monat_exist($i+1))
      $class='highlight';
   else
      $class='normal';
   echo '<a href="javascript:ergebnisnaviFrame.navigate(\'ergebnis_navi.php?monat='.($i+1).'\');" class="'.$class.'">'.
         $MONAT_KURZ[$monat_index].
         '</a><br />';
}

//das ist die alte methode, die immer nur 12 monate anzeigt
/*
$monat=date('m',time());
for ($i=$monat-1;$i>$monat-13;$i--)
{
   if ($terminmanager->monat_exist($i+1))
      $class='highlight';
   else
      $class='normal';
   echo '<a href="javascript:ergebnisnaviFrame.navigate(\'ergebnis_navi.php?monat='.($i+1).'\');" class="'.$class.'">'.$MONAT_KURZ[( ($i+12) % 12) + 1].'</a><br />';
}
*/

//das ist die neue methode, die alle jahre ab LAUNCH_YEAR anzeigt
$year = date('Y', time());
$monat = date('m', time());

//jahre runterzählen
for($y=$year,$m=$monat;$y>=LAUNCH_YEAR;$y--)
{
   echo "<a klappe=\"$y\">$y</a>";
   ?><div class="jahr" id="jahr<?php echo $y;?>"><?php
   while($m>=0)
   {
      $dy = $year - $y;
      print_monat($m - 12*$dy);
      $m--;
   }
   $m=11;
   ?></div><?php
}




?>
</div>
