<?php
@session_start() or die('Could not start session in '.__FILE__);

include 'inc/all.php';

$loginmanager=new loginmanager();
if ($loginmanager->login_status())
{
   include 'login_welcome.php';
}
else
{
   $mit_registrieren=true;
   $registrieren='registerPopup();';
   $action='javascript:login.send();';
   include 'formulare/loginform.php';
}
?>
