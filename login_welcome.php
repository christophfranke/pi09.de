<?php
@session_start() or die('Could not start session in '.__FILE__);

include 'inc/all.php';

$loginmanager=new loginmanager();

if ($loginmanager->login_status())
{
   $user=$loginmanager->get_user();
   echo "Willkommen ".$user->name().'!';
   if ($loginmanager->is_admin())
      echo '<br /><a href="admin/admin.php" target="_blank">->Adminbereich</a>';
   echo '<br /><input type="button" onClick="logout.send();" value="logout" />';
}
else
{
   include 'login.php';
}
?>
