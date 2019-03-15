<?php
if (!isset($mit_registrieren))
   $mit_registrieren=false;
if (!isset($registrieren))
   $registrieren='';
if (!isset($loginform_id))
   $loginform_id='loginform';
?>
<form action="<?php echo $action;?>" id="<?php echo $loginform_id;?>">
<table>
 <tr>
  <td>Name:</td>
  <td><input type="text" name="loginname" /></td>
 </tr>
 <tr>
  <td>Passwort:</td>
  <td><input type="password" name="passwort" /></td>
 </tr>
 <tr>
  <td></td>
  <td>
   <input type="submit" value="login" />
   <?php if ($mit_registrieren){ ?><input type="button" onClick="<?php echo $registrieren;?>" value="registrieren" /><?php } ?>
  </td>
 </tr>
</table>
</form>

