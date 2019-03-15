<?php
$loginmanager=new loginmanager();

if ($loginmanager->is_admin())
{
   $user=$loginmanager->get_user();
   echo '<br />Willkommen '.$user->name();
}
else
{

?>
<script type="text/javascript" language="javascript" src="../javascript/develop.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/fader.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/multixhr.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/aktion.js"></script>
<script type="text/javascript" language="javascript">

var login;

function init()
{
   login=new aktion('login');
   login.stdResponseId='status';
   login.form='loginform';
}

</script>
<h2>Login</h2>
<div>Hier kannst du dich anmelden</div>
<div id="status"></div>
<form action="javascript:login.send();" id="loginform">
<table>
 <tr>
  <td>Benutzername</td>
  <td><input type="text" name="loginname" /></td>
 </tr>
 <tr>
  <td>Passwort</td>
  <td><input type="password" name="passwort" /></td>
 </tr>
 <tr>
  <td></td>
  <td><input type="submit" value="login" /></td>
 </tr>
</table>
</form>
<?php
}
?>
