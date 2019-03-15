<?php
if (!isset($pflichtfelder))
   $pflichtfelder=false;
if (!isset($mit_passwort))
   $mit_passwort=false;
if (!isset($mit_status))
   $mit_status=false;
if (!isset($mit_loeschen))
   $mit_loeschen=false;
if (!isset($loeschen))
   $loeschen='';
?>
<form action="<?php echo $action;?>" method="post" id="userform">
<input type="hidden" name="ID" value="<?php if (isset($account->ID)) echo $account->ID;?>" />
<table>
 <tr>
 <td>Benutzername:</td>
 <td><input type="text" name="loginname" value="<?php if (isset($account->loginname)) echo $account->loginname;?>" size=39 /><?php if ($pflichtfelder) echo '*';?></td>
</tr>
<?php
if ($mit_passwort)
{
 ?><tr>
    <td>Passwort:</td>
    <td><input type="password" name="passwort" size=39 /><?php if ($pflichtfelder) echo '*';?></td>
   </tr>
   <tr>
    <td>Passwort wiederholen:</td>
    <td><input type="password" name="passwortwiederholung" size=39 /><?php if ($pflichtfelder) echo '*';?></td>
   </tr><?php
}
?>
 <tr>
  <td>Vorname:</td>
  <td><input type="text" name="vorname" value="<?php if (isset($account->vorname)) echo $account->vorname;?>" size=39 /></td>
 </tr>
 <tr>
  <td>Spitzname:</td>
  <td><input type="text" name="spitzname" value="<?php if (isset($account->spitzname)) echo $account->spitzname;?>" size=39 /></td>
 </tr>
<?php
if ($mit_status)
{
 ?><tr>
    <input type="hidden" name="statusflag" value="<?php if (isset($account->statusflag)) echo $account->statusflag;?>" value="1" />
    <td>Status:</td>
    <td><input type="checkbox" name="status_login" onClick="boxToStatus();" checked/> Login
        (Der Benutzer kann sich mit diesem Namen einloggen)<br />
        <input type="checkbox" name="status_spieler" onClick="boxToStatus();" /> Spieler (Der Benutzer wird in der Mannschaft gezeigt)<br />
        <input type="checkbox" name="status_admin" onClick="boxToStatus();" /> Admin (Der Benutzer hat Administratorrechte)</td>
   </tr><?php
}
?>
 <tr>
  <td>Email:</td>
  <td><input type="text" name="email" size=39 value="<?php if (isset($account->email)) echo $account->email;?>" /></td>
 </tr>
 <tr>
  <td></td>
  <td>
   <input type="submit" value="<?php echo $submit;?>" />
   <?php if ($mit_loeschen){ ?><input type="button" onClick="<?php echo $loeschen;?>" value="l&ouml;schen" /><?php } ?>
  </td>
 </tr>
</table>
</form>
