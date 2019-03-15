<?php
if (!isset($_GET['id']))
   echo 'Ein Systemfehler ist aufgetreten. Bitte lade die Seite neu (F5) und versuche es nocheinmal.<br /><a href="javascript:location.reload();" >Seite neuladen</a>';
else
{
   $id=$_GET['id'];
?>
<div>Zu diesem Termin zusagen?</div>
<br />
<a href="javascript:zusagen(<?php echo $id;?>,1)" class="zusageButton"><img src="img/ja_small.png" /></a>
<input type="button" value="ja" onClick="zusagen(<?php echo $id;?>,1);" />

<a href="javascript:zusagen(<?php echo $id;?>,3)" class="zusageButton">
<img src="img/nein_small.png" /></a><input type="button" value="nein" onClick="zusagen(<?php echo $id;?>,3);" />

<a href="javascript:zusagen(<?php echo $id;?>,2)" class="zusageButton">
<img src="img/vielleicht_small.png" /></a><input type="button" value="vielleicht" onClick="zusagen(<?php echo $id;?>,2);" />
<br />
<div style="float:right;">
<input type="button" onClick="zusageWindow.close();" value="abbrechen" class="abbrechenButton"/>
</div>
<?php
}
?>
