<?php
@session_start() or die('Could not start session in '.__FILE__);
include 'inc/all.php';

$terminmanager=new terminmanager();
$terminmanager->vergangene_anzeigen=false;
if ($terminmanager->monat_exist(MONAT_AKTUELL))
   $class0='highlight';
else
   $class0='normal';
if ($terminmanager->monat_exist(MONAT_EINLADUNG))
   $class1='highlight';
else
   $class1='normal';
?>

<a href="javascript:terminnaviFrame.navigate('termin_navi.php?monat=aktuell');" class="<?php echo $class0;?>">n&auml;chsten 10</a>
<br />

<div id="einladungenlink">
<a href="javascript:terminnaviFrame.navigate('termin_navi.php?monat=eingeladen');" class="<?php echo $class1;?>">eingeladen</a><br />
</div>

<div id="monatelinks">
<?php

$monat=date('m',time());
for ($i=$monat-1;$i<11+$monat;$i++)
{
   if ($terminmanager->monat_exist($i+1))
      $class='highlight';
   else
      $class='normal';
   echo '<a href="javascript:terminnaviFrame.navigate(\'termin_navi.php?monat='.($i+1).'\');" class="'.$class.'">'.$MONAT_KURZ[($i % 12) + 1].'</a><br />';
}
?>
</div>
