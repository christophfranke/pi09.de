<?php
if (!isset($mit_loeschen))
   $mit_loeschen=false;
?>
<form id="ligaform" action="<?php echo $action;?>">
<?php //ID eventuell als Hidden element in die Form schreiben
if (isset($liga->ID)) echo '<input type="hidden" name="ID" value='.$liga->ID.'" />';?>
<table>
 <tr>
  <td>Name:</td>
  <td><input type="text" name="name" value="<?php if (isset($liga->name)) echo $liga->name;?>"/></td>
 </tr>
 <tr>
  <td>Homepage:</td>
  <td><input type="text" name="homepage" value="<?php if (isset($liga->homepage)) echo $liga->homepage;?>"/></td>
 </tr>
 <tr>
  <td></td>
  <td>
   <input type="submit" value="<?php echo $submit;?>"/>
   <?php if ($mit_loeschen) { ?>
   <input type="button" onClick="ligaLoeschen.send();" value="l&ouml;schen"/>
   <?php } ?>
  </td>
</table>
</form>
