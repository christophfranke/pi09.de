<?php
@session_start() or die('Could not start session in '.__FILE__);

include '../inc/all.php';

$usermanager=new usermanager();
$userliste=$usermanager->alle_anzeigen();
$meldung=$usermanager->meldung();

?>
<h3>Übersicht User</h3>
<p>
<?php

if ($userliste!==false)
{
   if ($userliste->size()==0)
      echo 'Derzeit ist kein Benutzer in der Datenbank.';
   else
      foreach($userliste as $user)
         echo '<a href="javascript:userDetail.navigate(\'user_detail.php?id='.$user->ID.'\');" >'."$user->loginname (".$user->name().")</a><br />";
}
else
   echo "$meldung<br />";
?>
<br />
<a href="javascript:userDetail.navigate('user_detail.php');">Benutzer erstellen</a>

